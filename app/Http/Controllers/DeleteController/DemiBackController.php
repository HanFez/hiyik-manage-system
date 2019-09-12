<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 5/2/17
 * Time: 9:28 AM
 */

namespace App\Http\Controllers\Table;


use App\Http\Controllers\Admin\PrivilegeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\DemiBack;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Material;
use App\IekModel\Version1_0\ProductTemporary;
use App\IekModel\Version1_0\Texture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DemiBackController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * add view
     */
    public function add(){
        $err = new Error();
        $material = Material::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $texture = Texture::where(IekModel::CONDITION)
            ->with(['textureImages'=> function($q){
                $q->where(IekModel::CONDITION);
            }])
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        if($material->isEmpty()){
            $material = null;
        }
        if($texture->isEmpty()){
            $texture = null;
        }
        $data = new \stdClass();
        $data -> material = $material;
        $data -> texture = $texture;
        $err->setData($data);
        return view('admin.systemSetting.product.backAdd',['result'=>$err]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * store
     */
    public function create(Request $request){
        $demiBack = self::createDemiBack($request);
        return view('message.formResult',['result'=>$demiBack]);
    }

    public function createDemiBack(Request $request){
        $err = new Error();
        $demiBack = new DemiBack();
        $params = self::getCreateParams($request);
        if($params->statusCode != 0){
            return $params;
        }
        $checkMark = DemiBack::checkMark($params->data->mark);
        if($checkMark){
            $err->setError(Errors::EXIST);
            $err->setMessage("该编号已使用");
            $err->setData('mark');
            return $err;
        }
        $params = $params ->data;
        $demiBack -> name = $params -> name;
        $demiBack -> description = $params -> description;
        $demiBack -> material_id = $params -> materialId;
        $demiBack -> texture_id = $params -> textureId;
        $demiBack -> mark = $params -> mark;
        $demiBack -> thick = $params -> thick;
        $demiBack -> weight = $params -> weight;
        $demiBack -> price = $request -> price;
        $demiBack -> currency = $params -> currency;
        $demiBack -> unit = $params -> unit;
        $demiBack -> save();
        $err -> setData($demiBack);
        return $err;
    }

    /**
     * @param Request $request
     * @return Error
     * get param
     */
    public function getCreateParams(Request $request){
        $err = new Error();
        $name = $request->input('name');
        $mark = $request->input('mark');
        $unit = $request->input('unit');
        $materialId = $request->input('materialId');
        if(is_null($name) || is_null($mark) || is_null($unit) || is_null($materialId)){
            $err->setError(Errors::INVALID_PARAMS);
            return $err;
        }
        $params = new \stdClass();
        $params -> unit = $unit;
        $params -> mark = $mark;
        $params -> materialId = $materialId;
        $params -> name = $name;
        $params -> price = $request->input('price');
        $params -> thick = $request->input('thick');
        $params -> weight = $request->input('weight');
        $params -> textureId = $request->input('textureId');
        $params -> currency = $request->input('currency');
        $params -> description = $request -> input('description');
        $err->setData($params);
        return $err;
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * edit view
     */
    public function edit($id){
        $err = new Error();
        $demiBack = DemiBack::where(IekModel::ID,$id)
            ->where(IekModel::CONDITION)
            ->with(['materials'=>
                function($query){
                    $query->where(IekModel::CONDITION);
                }])
            ->with(['textures'=>
                function($query){
                    $query->where(IekModel::CONDITION);
                }])
            ->first();
        if(is_null($demiBack)){
            $err->setError(Errors::INVALID_PARAMS);
            return view('admin.systemSetting.product.backEdit',['result'=>$err]);
        }
        $material = Material::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $texture = Texture::where(IekModel::CONDITION)
            ->with(['textureImages'=>
                function($query){
                    $query->where(IekModel::CONDITION);
                }])
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        if($material->isEmpty()){
            $material = null;
        }
        if($texture->isEmpty()){
            $texture = null;
        }
        $data = new \stdClass();
        $data -> material = $material;
        $data -> texture = $texture;
        $data -> demiBack = $demiBack;
        $err -> setData($data);
        return view('admin.systemSetting.product.backEdit',['result'=>$err]);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * update
     */
    public function modify(Request $request , $id){
        $err = new Error();
        try{
            DB::beginTransaction();
            DemiBack::where(IekModel::ID,$id)
                ->update([
                    IekModel::IS_MODIFY => true
                ]);
            $demiBack = self::createDemiBack($request);
            if($demiBack->statusCode != 0){
                throw new \Exception('rollback');
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            if(isset($demiBack)){
                $err = $demiBack;
            }
        }
        return view('message.formResult',['result'=>$err]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * list view
     */
    public function lists(Request $request){
        $tableName = 'tblDemiBacks';
        $start = $request->input('start');
        $getAll = new PrivilegeController();
        $res = $getAll->getAllList($request , $tableName);
        if(is_null($start)){
            $params = new \stdClass();
            $params-> type = 'demiBack';
            $params-> url = 'demiBack';
            $field = $res;
            return view('tableData.index',compact('field', 'params'));
        }
        return response()->json($res);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * delete
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
                $use_demiBack = DemiBack::whereHas('backPatternDemi.backPattern.back.productBack')
                    ->with('backPatternDemi.backPattern.back')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_demiBack)){
                    $res = $this->limitDemi($use_demiBack);
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
                    $re = DemiBack::where(IekModel::ID,$id)
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

    public function limitDemi($use){
        if(!$use->backPatternDemi->isEmpty()){
            $backId = [];
            foreach($use->backPatternDemi as $demi){
                if(!is_null($demi->backPattern)){
                    if(!is_null($demi->backPattern->back)){
                        $backId[] = $demi->backPattern->back->id;
                    }
                }
            }
            return $backId;
        }
    }
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * recover
     */
    public function recover(Request $request){
        $err = new Error();
        $ids = $request->input('ids');
        if(is_null($ids)){
            $err->setError(Errors::INVALID_PARAMS);
            return response()->json($err);
        }
        $model = new DemiBack();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
}