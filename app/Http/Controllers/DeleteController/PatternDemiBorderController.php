<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 5/4/17
 * Time: 7:08 PM
 */

namespace App\Http\Controllers\Table;


use App\Http\Controllers\Admin\PrivilegeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\DemiBorder;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Pattern;
use App\IekModel\Version1_0\PatternDemiBorder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatternDemiBorderController extends Controller
{
    public function add(){
        $err = new Error();
        $pattern = Pattern::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        if(is_null($pattern)){
            $pattern = null;
        }
        $demiBorder = DemiBorder::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $data = new \stdClass();
        $data -> pattern = $pattern;
        $data -> demiBorder = $demiBorder;
        $err->setData($data);
        return view('admin.systemSetting.product.patternDemiBorder',['result'=>$err]);
    }

    public function create(Request $request){
        $patternDemiBorder = self::createPatternDemiBorder($request);
        return view('message.formResult',['result'=>$patternDemiBorder]);
    }

    public function createPatternDemiBorder(Request $request){
        $err = new Error();
        $params = self::getCreateParams($request);
        if($params -> statusCode != 0){
            return $params;
        }
        $params = $params->data;
        $patternDemiBorders = [];
        foreach ($params->demiBorder as $demiBorder){
            $patternDemiBorder = [];
            $patternDemiBorder['unit'] = $demiBorder  -> unit;
            $patternDemiBorder['currency'] = $demiBorder  -> currency;
            $patternDemiBorder['price'] = $demiBorder -> price;
            $patternDemiBorder['demi_border_id'] = $demiBorder -> demiBorderId;
            $patternDemiBorder['pattern_id'] = $params -> patternId;
            $patternDemiBorders[] = $patternDemiBorder;
        }
        PatternDemiBorder::insert($patternDemiBorders);
        return $err;
    }

    public function getCreateParams(Request $request){
        $err = new Error();
        $patternId = $request->input('patternId');
        $demiBorder = $request->input('demiBorder');
        if(is_null($patternId)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('请选择生产方式');
            return $err;
        }
        if(count($demiBorder) == 0){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('请添加半成品框');
            return $err;
        }
        $data = new \stdClass();
        $data -> patternId = $patternId;
        $data -> demiBorder = json_decode(json_encode($demiBorder));
        $err->setData($data);
        return $err;
    }

    public function edit($id){
        $err = new Error();
        $pattern = Pattern::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $demiBorder = DemiBorder::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $patternDemiBorder = PatternDemiBorder::where(IekModel::ID,$id)
            ->where(IekModel::CONDITION)
            ->get();
        $data = new \stdClass();
        $data->pattern = $pattern;
        $data->demiBorder = $demiBorder;
        $data->patternDemiBorder = $patternDemiBorder;
        if($patternDemiBorder->isEmpty()){
            $data = null;
        }
        $err->setData($data);
        return view('admin.systemSetting.product.patternDemiBorder',['result'=>$err]);
    }

    public function modify(Request $request , $id){
        $err = new Error();
        try{
            DB::beginTransaction();
            PatternDemiBorder::where(IekModel::ID,$id)
                ->update([
                    IekModel::IS_MODIFY => true
                ]);
            $patternDemiBorder = self::createPatternDemiBorder($request);
            if($patternDemiBorder -> statusCode != 0){
                throw new \Exception('rollBack');
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            if(isset($patternDemiBorder)){
                $err = $patternDemiBorder;
            }
        }
        return view('message.formResult',['result'=>$err]);
    }

    public function lists(Request $request){
        $tableName = 'tblPatternDemiBorders';
        $start = $request->input('start');
        $getAll = new PrivilegeController();
        $res = $getAll->getAllList($request , $tableName);
        if(is_null($start)){
            $params = new \stdClass();
            $params-> type = 'patternDemiBorder';
            $params-> url = 'patternDemiBorder';
            $field = $res;
            return view('tableData.index',compact('field', 'params'));
        }
        return response()->json($res);
    }

    public function del(){
        $model = new PatternDemiBorder();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }

    public function recover(){
        $model = new PatternDemiBorder();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
}