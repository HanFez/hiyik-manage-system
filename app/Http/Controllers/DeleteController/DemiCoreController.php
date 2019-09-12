<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 5/2/17
 * Time: 4:12 PM
 */

namespace App\Http\Controllers\Table;


use App\Http\Controllers\Admin\PrivilegeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\DemiCore;
use App\IekModel\Version1_0\DemiCoreParam;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Material;
use App\IekModel\Version1_0\Param;
use App\IekModel\Version1_0\ProductTemporary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DemiCoreController extends Controller
{
    public function add(){
        $err = new Error();
        $material = Material::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        if($material->isEmpty()){
            $material = null;
        }
        $param = Param::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        if($param->isEmpty()){
            $param = null;
        }
        $data = new \stdClass();
        $data -> param = $param;
        $data -> material = $material;
        $err->setData($data);
        return view('admin.systemSetting.product.coreAdd',['result'=>$err]);
    }

    public function create(Request $request){
        $err = self::createDemiCore($request);
        return view('message.formResult',['result'=>$err]);
    }

    public function createDemiCore(Request $request){
        $err = new Error();
        $params = self::getCreateParams($request);
        if($params->statusCode != 0){
            return $params;
        }
        $params = $params->data;
        $checkMark = DemiCore::checkMark($params->mark);
        if($checkMark){
            $err->setError(Errors::EXIST);
            $err->setMessage("该编号已使用");
            $err->setData('mark');
            return $err;
        }
        try{
            DB::beginTransaction();
            $demiCore = new DemiCore();
            $demiCore -> name = $params->name;
            $demiCore -> description = $params->description;
            $demiCore -> material_id = $params->materialId;
            $demiCore -> thick = $params->thick;
            $demiCore -> weight = $params->weight;
            $demiCore -> price = $params->price;
            $demiCore -> unit = $params->unit;
            $demiCore -> mark = $params->mark;
            $demiCore -> currency = $params->currency;
            $demiCore -> save();
            $demiCoreParams = [];
            foreach ($params->param as $item=>$value){
                $demiCoreParam = [];
                $demiCoreParam['demi_core_id'] = $demiCore->{IekModel::ID};
                $demiCoreParam['param_id'] = $value->{IekModel::ID};
                $demiCoreParam['val'] = $value->val;
                $demiCoreParams[] = $demiCoreParam;
            }
            DemiCoreParam::insert($demiCoreParams);
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::FAILED);
            $err->setMessage($e->getMessage());
        }
        return $err;
    }

    public function getCreateParams(Request $request){
        $err = new Error();
        $name = $request->input('name');
        $materialId = $request->input('materialId');
        $unit = $request->input('unit');
        $param = $request->input('param');
        if(is_null($name) || is_null($materialId) || is_null($unit) || is_null($param)){
            $err->setError(Errors::INVALID_PARAMS);
            return $err;
        }
        $params = new \stdClass();
        $params -> name = $name;
        $params -> materialId = $materialId;
        $params -> param = json_decode(json_encode($param));
        $params -> mark = $request->input('mark');
        $params -> weight = $request->input('weight');
        $params -> thick = $request->input('thick');
        $params -> price = $request->input('price');
        $params -> currency = $request->input('currency');
        $params -> unit = $unit;
        $params -> description = $request->input('description');
        $err->setData($params);
        return $err;
    }

    public function edit($id){
        $err = new Error();
        $demiCore = DemiCore::where(IekModel::ID,$id)
            ->where(IekModel::CONDITION)
            ->with(['materials'=>function($q){
                $q->where(IekModel::CONDITION);
            }])
            ->with('demiCoreParam')
            ->first();
        if(is_null($demiCore)){
            $err->setError(Errors::INVALID_PARAMS);
            return view('admin.systemSetting.product.coreEdit',['result'=>$err]);
        }
        $material = Material::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        if($material->isEmpty()){
            $material = null;
        }
        $param = Param::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        if($param->isEmpty()){
            $param = null;
        }
        $data = new \stdClass();
        $data -> param = $param;
        $data -> material = $material;
        $data -> demiCore = $demiCore;
        $err->setData($data);
        return view('admin.systemSetting.product.coreEdit',['result'=>$err]);
    }

    public function modify(Request $request , $id){
        $err = new Error();
        try{
            DB::beginTransaction();
            DemiCore::where(IekModel::ID,$id)
                ->update([
                    IekModel::IS_MODIFY => true
                ]);
            $demiCore = self::createDemiCore($request);
            if($demiCore->statusCode != 0){
                throw new \Exception('rollback');
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            if(isset($demiCore)){
                $err = $demiCore;
            }
        }
        return view('message.formResult',['result'=>$err]);
    }

    public function lists(Request $request){
        $tableName = 'tblDemiCores';
        $start = $request->input('start');
        $getAll = new PrivilegeController();
        $res = $getAll->getAllList($request , $tableName);
        if(is_null($start)){
            $params = new \stdClass();
            $params-> type = 'demiCore';
            $params-> url = 'demiCore';
            $field = $res;
            return view('tableData.index',compact('field', 'params'));
        }
        return response()->json($res);
    }

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
                $use_demiCore = DemiCore::whereHas('corePatternDemi.corePattern.core.productCore')
                    ->with('corePatternDemi.corePattern.core')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_demiCore)){
                    $res = $this->limitDemi($use_demiCore);
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
                    $re = DemiCore::where(IekModel::ID,$id)
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
        if(!$use->corePatternDemi->isEmpty()){
            $coreId = [];
            foreach($use->corePatternDemi as $demi){
                if(!is_null($demi->corePattern) && !is_null($demi->corePattern->core)){
                    $coreId[] = $demi->corePattern->core->id;
                }
            }
            return $coreId;
        }
    }
    public function recover(Request $request){
        $err = new Error();
        $ids = $request->input('ids');
        if(is_null($ids)){
            $err->setError(Errors::INVALID_PARAMS);
            return response()->json($err);
        }
        $model = new DemiCore();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }

}