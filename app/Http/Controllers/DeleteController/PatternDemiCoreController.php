<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 5/4/17
 * Time: 7:09 PM
 */

namespace App\Http\Controllers\Table;


use App\Http\Controllers\Admin\PrivilegeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\DemiCore;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Pattern;
use App\IekModel\Version1_0\PatternDemiCore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatternDemiCoreController extends Controller
{
    public function add(){
        $err = new Error();
        $pattern = Pattern::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        if(is_null($pattern)){
            $pattern = null;
        }
        $demiCore = DemiCore::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $data = new \stdClass();
        $data -> pattern = $pattern;
        $data -> demiCore = $demiCore;
        $err->setData($data);
        return view('admin.systemSetting.product.patternDemiCore',['result'=>$err]);
    }

    public function create(Request $request){
        $patternDemiCore = self::createPatternDemiCore($request);
        return view('message.formResult',['result'=>$patternDemiCore]);
    }

    public function createPatternDemiCore(Request $request){
        $err = new Error();
        $params = self::getCreateParams($request);
        if($params -> statusCode != 0){
            return $params;
        }
        $params = $params->data;
        $patternDemiCores = [];
        foreach ($params->demiCore as $demiCore){
            $patternDemiCore = [];
            $patternDemiCore['unit'] = $demiCore  -> unit;
            $patternDemiCore['currency'] = $demiCore  -> currency;
            $patternDemiCore['price'] = $demiCore -> price;
            $patternDemiCore['demi_core_id'] = $demiCore -> demiCoreId;
            $patternDemiCore['pattern_id'] = $params -> patternId;
            $patternDemiCores[] = $patternDemiCore;
        }
        PatternDemiCore::insert($patternDemiCores);
        return $err;
    }

    public function getCreateParams(Request $request){
        $err = new Error();
        $patternId = $request->input('patternId');
        $demiCore = $request->input('demiCore');
        if(is_null($patternId)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('请选择生产方式');
            return $err;
        }
        if(count($demiCore) == 0){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('请添加半成品画芯');
            return $err;
        }
        $data = new \stdClass();
        $data -> patternId = $patternId;
        $data -> demiCore = json_decode(json_encode($demiCore));
        $err->setData($data);
        return $err;
    }

    public function edit($id){
        $err = new Error();
        $pattern = Pattern::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $demiCore = DemiCore::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $patternDemiCore = PatternDemiCore::where(IekModel::ID,$id)
            ->where(IekModel::CONDITION)
            ->get();
        $data = new \stdClass();
        $data->pattern = $pattern;
        $data->demiCore = $demiCore;
        $data->patternDemiCore = $patternDemiCore;
        if($patternDemiCore->isEmpty()){
            $data = null;
        }
        $err->setData($data);
        return view('admin.systemSetting.product.patternDemiCore',['result'=>$err]);
    }

    public function modify(Request $request , $id){
        $err = new Error();
        try{
            DB::beginTransaction();
            PatternDemiCore::where(IekModel::ID,$id)
                ->update([
                    IekModel::IS_MODIFY => true
                ]);
            $patternDemiCore = self::createPatternDemiCore($request);
            if($patternDemiCore -> statusCode != 0){
                throw new \Exception('rollBack');
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            if(isset($patternDemiCore)){
                $err = $patternDemiCore;
            }
        }
        return view('message.formResult',['result'=>$err]);
    }

    public function lists(Request $request){
        $tableName = 'tblPatternDemiCores';
        $start = $request->input('start');
        $getAll = new PrivilegeController();
        $res = $getAll->getAllList($request , $tableName);
        if(is_null($start)){
            $params = new \stdClass();
            $params-> type = 'patternDemiCore';
            $params-> url = 'patternDemiCore';
            $field = $res;
            return view('tableData.index',compact('field', 'params'));
        }
        return response()->json($res);
    }

    public function del(){
        $model = new PatternDemiCore();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }

    public function recover(){
        $model = new PatternDemiCore();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
}