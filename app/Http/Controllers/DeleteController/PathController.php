<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/5/9
 * Time: 9:46
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Admin\PrivilegeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Path;
use App\IekModel\Version1_0\PathPoint;
use App\IekModel\Version1_0\ProductTemporary;
use Illuminate\Support\Facades\DB;

class PathController extends Controller
{
    /**
     * path列表页
     */
    public function lists(){
        $model = new Path();
        $type = 'path';
        $getList = new IndexController();
        $result = $getList->tableList($model , $type);
        return $result;
    }
    /**
     * path添加页
     */
    public function add(){
        return view('admin.systemSetting.product.path');
    }
    /**
     * 保存path数据
     */
    public function create(){
        $err = new Error();
        DB::beginTransaction();
        try{
            $err = $this->saveParam();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
        }
        return view('message.formResult',['result'=>$err]);
    }
    public function saveParam(){
        $err = new Error();
        $input = request()->except('_token');
        if(is_null($input['name'])){
            $err->setError(Errors::NOT_EMPTY);
            $err->setMessage('名字不能为空');
            $err->setData('name');
            return $err;
        }
        $path = new Path();
        $path->name = $input['name'];
        $path->description = $input['description'];
        $path->is_official = true;
        $path->is_closed = $input['isClosed'];
        $path->save();
        foreach($input['point'] as $point){
            $point = json_decode(json_encode($point));
            $pathPoint = new PathPoint();
            $pathPoint->path_id = $path->id;
            $pathPoint->in_x = $point->inX;
            $pathPoint->in_y = $point->inY;
            $pathPoint->out_x = $point->outX;
            $pathPoint->out_y = $point->outY;
            $pathPoint->x = $point->x;
            $pathPoint->y = $point->y;
            $pathPoint->inx = $point->index;
            $re = $pathPoint->save();
        }
        if($re){
            $err->setError(Errors::OK);
            $err->setMessage('成功');
        }else{
            $err->setError(Errors::FAILED);
            $err->setMessage('失败');
        }
        return $err;
    }
    /**
     * path修改页
     */
    public function edit($id){
        $path = Path::with('pathPoint')->find($id);
        return view('admin.systemSetting.product.path',compact('path'));
    }
    /**
     * 保存path数据
     */
    public function modify($id){
        $err = new Error();
        DB::beginTransaction();
        try{
            Path::where(IekModel::ID,$id)
                ->update([
                    IekModel::IS_MODIFY => true
                ]);
            $err = $this->saveParam();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
        }
        return view('message.formResult',['result'=>$err]);
    }
    /**
     * 删除path
     */
    public function del(){
        $err = new Error();
        $ids = request()->input('ids');
        if(is_null($ids)){
            $err->setError(Errors::INVALID_PARAMS);
            return response()->json($err);
        }
        DB::beginTransaction();
        try{
            $temporary = ProductTemporary::where(IekModel::CONDITION)->pluck(IekModel::DATA);
            foreach($ids as $id){
                $use_path = Path::whereHas('shapePath.shape.hole.frameHole.frame.productFrame')
                    ->with('shapePath.shape.hole.frameHole.frame')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_path)){
                    $res = $this->limitPath($use_path);
                    foreach($res as $re){
                        foreach($temporary as $tem){
                            $str = substr_count($tem,$re);
                            if($str>0){
                                $err->setError(Errors::INVALID_PARAMS);
                                $err->setMessage('有产品草稿正在使用这条数据，请不要删除');
                                return response()->json($err);
                            }
                        }
                    }
                    $err->setError(Errors::INVALID_PARAMS);
                    $err->setMessage('有产品正在使用这条数据，请不要删除');
                    return response()->json($err);
                }else{
                    $re = Path::where(IekModel::ID,$id)
                        ->update([
                            IekModel::REMOVED => true
                        ]);
                    if($re){
                        $err->setError(Errors::OK);
                        $err->setMessage('删除成功');
                    }else{
                        $err->setError(Errors::FAILED);
                        $err->setMessage('删除失败');
                    }
                }
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
        }
        return response()->json($err);
    }
    public function limitPath($use){
        if(!$use->shapePath->isEmpty()){
            $frameId = [];
            foreach($use->shapePath as $shap){
                if($shap->shape != null){
                    if(!$shap->shape->hole->isEmpty()){
                        foreach($shap->shape->hole as $hole){
                            if(!$hole->frameHole->isEmpty()){
                                foreach($hole->frameHole as $ho){
                                    if(!is_null($ho->frame)){
                                        $frameId[] = $ho->frame->id;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            return $frameId;
        }
    }
    /**
     * 恢复path
     */
    public function recover(){
        $model = new Path();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * path由point组成
     */
    public function pathPoint($id){
        $tableName = 'tblPathPoints';
        $start = request()->input('start');
        $getAll = new PrivilegeController();
        $res = $getAll->getAllList(request() , $tableName , [IekModel::PATH_ID => $id]);
        if(is_null($start)){
            $params = new \stdClass();
            $params-> type = 'pathPoint';
            $params-> url = 'pathPoint';
            $field = $res;
            return view('tableData.index',compact('field', 'params'));
        }
        return response()->json($res);
    }
    /**
     * 删除pathPoint
     */
    public function delPoint(){
        $model = new PathPoint();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * 恢复pathPoint
     */
    public function recoverPoint(){
        $model = new PathPoint();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
}