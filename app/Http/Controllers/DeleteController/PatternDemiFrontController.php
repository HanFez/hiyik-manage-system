<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 5/4/17
 * Time: 7:13 PM
 */

namespace App\Http\Controllers\Table;


use App\Http\Controllers\Admin\PrivilegeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\DemiFront;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Pattern;
use App\IekModel\Version1_0\PatternDemiFront;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatternDemiFrontController extends Controller
{
    public function add(){
        $err = new Error();
        $pattern = Pattern::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        if(is_null($pattern)){
            $pattern = null;
        }
        $demiFront = DemiFront::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $data = new \stdClass();
        $data -> pattern = $pattern;
        $data -> demiFront = $demiFront;
        $err->setData($data);
        return view('admin.systemSetting.product.patternDemiFront',['result'=>$err]);
    }

    public function create(Request $request){
        $patternDemiFront = self::createPatternDemiFront($request);
        return view('message.formResult',['result'=>$patternDemiFront]);
    }

    public function createPatternDemiFront(Request $request){
        $err = new Error();
        $params = self::getCreateParams($request);
        if($params -> statusCode != 0){
            return $params;
        }
        $params = $params->data;
        $patternDemiFronts = [];
        foreach ($params->demiFront as $demiFront){
            $patternDemiFront = [];
            $patternDemiFront['unit'] = $demiFront  -> unit;
            $patternDemiFront['currency'] = $demiFront  -> currency;
            $patternDemiFront['price'] = $demiFront -> price;
            $patternDemiFront['demi_front_id'] = $demiFront -> demiFrontId;
            $patternDemiFront['pattern_id'] = $params -> patternId;
            $patternDemiFronts[] = $patternDemiFront;
        }
        PatternDemiFront::insert($patternDemiFronts);
        return $err;
    }

    public function getCreateParams(Request $request){
        $err = new Error();
        $patternId = $request->input('patternId');
        $demiFront = $request->input('demiFront');
        if(is_null($patternId)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('请选择生产方式');
            return $err;
        }
        if(count($demiFront) == 0){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('请添加半成品玻璃');
            return $err;
        }
        $data = new \stdClass();
        $data -> patternId = $patternId;
        $data -> demiFront = json_decode(json_encode($demiFront));
        $err->setData($data);
        return $err;
    }

    public function edit($id){
        $err = new Error();
        $pattern = Pattern::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $demiFront = DemiFront::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $patternDemiFront = PatternDemiFront::where(IekModel::ID,$id)
            ->where(IekModel::CONDITION)
            ->get();
        $data = new \stdClass();
        $data->pattern = $pattern;
        $data->demiFront = $demiFront;
        $data->patternDemiFront = $patternDemiFront;
        if($patternDemiFront->isEmpty()){
            $data = null;
        }
        $err->setData($data);
        return view('admin.systemSetting.product.patternDemiFront',['result'=>$err]);
    }

    public function modify(Request $request , $id){
        $err = new Error();
        try{
            DB::beginTransaction();
            PatternDemiFront::where(IekModel::ID,$id)
                ->update([
                    IekModel::IS_MODIFY => true
                ]);
            $patternDemiFront = self::createPatternDemiFront($request);
            if($patternDemiFront -> statusCode != 0){
                throw new \Exception('rollback');
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            if(isset($patternDemiFront)){
                $err = $patternDemiFront;
            }
        }
        return view('message.formResult',['result'=>$err]);
    }

    public function lists(Request $request){
        $tableName = 'tblPatternDemiFronts';
        $start = $request->input('start');
        $getAll = new PrivilegeController();
        $res = $getAll->getAllList($request , $tableName);
        if(is_null($start)){
            $params = new \stdClass();
            $params-> type = 'patternDemiFront';
            $params-> url = 'patternDemiFront';
            $field = $res;
            return view('tableData.index',compact('field', 'params'));
        }
        return response()->json($res);
    }

    public function del(){
        $model = new PatternDemiFront();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }

    public function recover(){
        $model = new PatternDemiFront();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
}