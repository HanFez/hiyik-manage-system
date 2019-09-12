<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 5/4/17
 * Time: 7:03 PM
 */

namespace App\Http\Controllers\Table;


use App\Http\Controllers\Admin\PrivilegeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\DemiBack;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Pattern;
use App\IekModel\Version1_0\PatternDemiBack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatternDemiBackController extends Controller
{
    public function lists(Request $request){
        $tableName = 'tblPatternDemiBacks';
        $start = $request->input('start');
        $getAll = new PrivilegeController();
        $res = $getAll->getAllList($request , $tableName);
        if(is_null($start)){
            $params = new \stdClass();
            $params-> type = 'patternDemiBack';
            $params-> url = 'patternDemiBack';
            $field = $res;
            return view('tableData.index',compact('field', 'params'));
        }
        return response()->json($res);
    }

    public function add(){
        $err = new Error();
        $pattern = Pattern::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        if(is_null($pattern)){
            $pattern = null;
        }
        $demiBack = DemiBack::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $data = new \stdClass();
        $data -> pattern = $pattern;
        $data -> demiBack = $demiBack;
        $err->setData($data);
        return view('admin.systemSetting.product.patternDemiBack',['result'=>$err]);
    }

    public function create(Request $request){
        $patternDemiBack = self::createPatternDemiBack($request);
        return view('message.formResult',['result'=>$patternDemiBack]);
    }

    public function createPatternDemiBack(Request $request){
        $err = new Error();
        $params = self::getCreateParams($request);
        if($params -> statusCode != 0){
            return $params;
        }
        $params = $params->data;
        $patternDemiBacks = [];
        foreach ($params->demiBack as $demiBack){
            $patternDemiBack = [];
            $patternDemiBack['unit'] = $demiBack  -> unit;
            $patternDemiBack['currency'] = $demiBack  -> currency;
            $patternDemiBack['price'] = $demiBack -> price;
            $patternDemiBack['demi_back_id'] = $demiBack -> demiBackId;
            $patternDemiBack['pattern_id'] = $params -> patternId;
            $patternDemiBacks[] = $patternDemiBack;
        }
        PatternDemiBack::insert($patternDemiBacks);
        return $err;
    }

    public function getCreateParams(Request $request){
        $err = new Error();
        $patternId = $request->input('patternId');
        $demiBack = $request->input('demiBack');
        if(is_null($patternId)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('请选择生产方式');
            return $err;
        }
        if(count($demiBack) == 0){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('请添加半成品背板');
            return $err;
        }
        $data = new \stdClass();
        $data -> patternId = $patternId;
        $data -> demiBack = json_decode(json_encode($demiBack));
        $err->setData($data);
        return $err;
    }

    public function edit($id){
        $err = new Error();
        $pattern = Pattern::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $demiBack = DemiBack::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $patternDemiBack = PatternDemiBack::where(IekModel::ID,$id)
            ->where(IekModel::CONDITION)
            ->get();
        $data = new \stdClass();
        $data->pattern = $pattern;
        $data->demiBack = $demiBack;
        $data->patternDemiBack = $patternDemiBack;
        if($patternDemiBack->isEmpty()){
            $data = null;
        }
        $err->setData($data);
        return view('admin.systemSetting.product.patternDemiBack',['result'=>$err]);
    }

    public function modify(Request $request , $id){
        $err = new Error();
        try{
            DB::beginTransaction();
            PatternDemiBack::where(IekModel::ID,$id)
                ->update([
                    IekModel::IS_MODIFY => true
                ]);
            $patternDemiBack = self::createPatternDemiBack($request);
            if($patternDemiBack -> statusCode != 0){
                throw new \Exception('rollBack');
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            if(isset($patternDemiBack)){
                $err = $patternDemiBack;
            }
        }
        return view('message.formResult',['result'=>$err]);
    }


    public function del(){
        $model = new PatternDemiBack();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }

    public function recover(){
        $model = new PatternDemiBack();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
}