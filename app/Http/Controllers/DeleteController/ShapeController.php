<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/5/5
 * Time: 17:28
 */
namespace App\Http\Controllers\Table;



use App\Http\Controllers\Admin\PrivilegeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Path;
use App\IekModel\Version1_0\ProductTemporary;
use App\IekModel\Version1_0\Shape;
use App\IekModel\Version1_0\ShapePath;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShapeController extends Controller
{
    /**
     * 形状列表
     */
    public function index(){
        $model = new Shape();
        $type = 'shape';
        $getList = new IndexController();
        $result = $getList->tableList($model, $type);
        return $result;
    }
    /**
     * 添加页
     */
    public function add(){
        $err = new Error();
        $shape = Shape::where(IekModel::LEVEL,0)
            ->where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        if($shape -> isEmpty()){
            $shape = null;
        }
        $path = Path::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        if($path -> isEmpty()){
            $path = null;
        }
        $data = new \stdClass();
        $data -> path = $path;
        $data -> shapes = $shape;
        $err->setData($data);
        return view('admin.systemSetting.product.shapeAdd',['result'=>$err]);
    }

    public function create(Request $request){
        $result = self::createShape($request);
        return view('message.formResult',['result'=>$result]);
    }
    /**
     * 保存添加数据
     */
    public function createShape(Request $request){
        $err = new Error();
        $params = self::getCreateParams($request);
        if($params -> statusCode != 0){
            return $params;
        }
        $params = $params -> data;
        try{
            DB::beginTransaction();
            $shape = new Shape();
            $shape -> name = $params -> name;
            $shape -> parent_id = $params -> parentId;
            $shape -> description = $params -> description;
            $shape -> is_official = true;
            $shape -> level = $params -> level;
            $shape->save();

            $shapePaths = [];
            foreach ($params->path as $item=>$value){
                $shapePath = [];
                $shapePath['shape_id'] = $shape->{IekModel::ID};
                $shapePath['path_id'] = $value->pathId;
                $shapePath['start_x'] = $value->startX;
                $shapePath['start_y'] = $value->startY;
                $shapePath['width'] = $value->width;
                $shapePath['height'] = $value->height;
                $shapePaths[] = $shapePath;
            }
            ShapePath::insert($shapePaths);
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
        }
        return $err;
    }

    public function getCreateParams(Request $request){
        $err = new Error();
        $name = $request->input('name');
        $path = $request->input('path');
        if(is_null($name) || is_null($path)){
            $err->setError(Errors::INVALID_PARAMS);
            return $err;
        }
        $params = new \stdClass();
        $params -> name = $name;
        $params -> path = json_decode(json_encode($path));
        $params -> description = $request->input('description');
        $params -> isOfficial = $request->input('isOfficial');
        $params -> parentId = $request->input('parentId');
        $params -> level = $request->input('level');
        $err->setData($params);
        return $err;
    }

    /**
     * 修改页
     */
    public function edit($id){
        $err = new Error();
        $shape = Shape::where(IekModel::ID,$id)
            ->where(IekModel::CONDITION)
            ->with('shapePath.path')
            ->first();
        if(is_null($shape)){
            $err->setError(Errors::INVALID_PARAMS);
            return view('admin.systemSetting.product.shapeAdd',['result'=>$err]);
        }
        $shapes = Shape::where(IekModel::LEVEL,0)
            ->where(IekModel::IS_MODIFY,false)
            ->where(IekModel::CONDITION)
            ->get();
        if($shapes->isEmpty()){
            $shapes = null;
        }
        $path = Path::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        if($path->isEmpty()){
            $path = null;
        }
        $data = new \stdClass();
        $data -> path = $path;
        $data -> shapes = $shapes;
        $data -> shape = $shape;
        $err->setData($data);
        return view('admin.systemSetting.product.shapeAdd',['result'=>$err]);
    }

    /**
     * 保存修改数据
     */
    public function modify(Request $request , $id){
        $err = new Error();
        try{
            DB::beginTransaction();
            Shape::where(IekModel::ID,$id)
                ->update([
                    IekModel::IS_MODIFY => true
                ]);
            $shape = self::createShape($request);
            if($shape -> statusCode != 0){
                throw new \Exception('rollback');
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            if(isset($shape)){
                $err = $shape;
            }
        }
        return view('message.formResult',['result'=>$err]);
    }
    /**
     * 删除
     */
    public function delete(Request $request){
        $err = new Error();
        $ids = $request->input('ids');
        if(is_null($ids)){
            $err->setError(Errors::INVALID_PARAMS);
            return response()->json($err);
        }
        DB::beginTransaction();
        try{
            $temporary = ProductTemporary::where(IekModel::CONDITION)->pluck(IekModel::DATA);
            foreach($ids as $id){
                $use_shape = Shape::whereHas('hole.frameHole.frame.productFrame')
                    ->with('hole.frameHole.frame')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_shape)){
                    $res = $this->limitShape($use_shape);
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
                    $count = Shape::where(IekModel::PARENT_ID,$id)
                        ->where(IekModel::CONDITION)
                        ->where(IekModel::IS_MODIFY,false)
                        ->count();
                    if($count > 0){
                        $err->setError(Errors::INVALID_PARAMS);
                        $err->setMessage('这条数据是父级数据并且正在被使用，请不要删除');
                        return response()->json($err);
                    }
                    $re = Shape::where(IekModel::ID,$id)
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
    public function limitShape($use){
        if(!$use->hole->isEmpty()){
            $frameId = [];
            foreach($use->hole as $hole){
                if(!$hole->frameHole->isEmpty()){
                    foreach($hole->frameHole as $frame){
                        if(!is_null($frame->frame)){
                            $frameId[] = $frame->frame->id;
                        }
                    }
                }
            }
            return $frameId;
        }
    }
    /**
     * 恢复
     */
    public function recover(Request $request){
        $err = new Error();
        $ids = $request->input('ids');
        if(is_null($ids)){
            $err->setError(Errors::INVALID_PARAMS);
            return response()->json($err);
        }
        $model = new Shape();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }

    public function lists(Request $request){
        $tableName = 'tblShapes';
        $start = $request->input('start');
        $getAll = new PrivilegeController();
        $res = $getAll->getAllList($request , $tableName);
        if(is_null($start)){
            $params = new \stdClass();
            $params-> type = 'shape';
            $params-> url = 'shape';
            $field = $res;
            return view('tableData.index',compact('field', 'params'));
        }
        return response()->json($res);
    }

}