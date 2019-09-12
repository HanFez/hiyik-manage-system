<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 5/4/17
 * Time: 7:12 PM
 */

namespace App\Http\Controllers\Table;


use App\Http\Controllers\Admin\PrivilegeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\DemiFrame;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Pattern;
use App\IekModel\Version1_0\PatternDemiFrame;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatternDemiFrameController extends Controller
{
    public function add(){
        $err = new Error();
        $pattern = Pattern::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        if(is_null($pattern)){
            $pattern = null;
        }
        $demiFrame = DemiFrame::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $data = new \stdClass();
        $data -> pattern = $pattern;
        $data -> demiFrame = $demiFrame;
        $err->setData($data);
        return view('admin.systemSetting.product.patternDemiFrame',['result'=>$err]);
    }

    public function create(Request $request){
        $patternDemiFrame = self::createPatternDemiFrame($request);
        return view('message.formResult',['result'=>$patternDemiFrame]);
    }

    public function createPatternDemiFrame(Request $request){
        $err = new Error();
        $params = self::getCreateParams($request);
        if($params -> statusCode != 0){
            return $params;
        }
        $params = $params->data;
        $patternDemiFrames = [];
        foreach ($params->demiFrame as $demiFrame){
            $patternDemiFrame = [];
            $patternDemiFrame['unit'] = $demiFrame  -> unit;
            $patternDemiFrame['currency'] = $demiFrame  -> currency;
            $patternDemiFrame['price'] = $demiFrame -> price;
            $patternDemiFrame['demi_frame_id'] = $demiFrame -> demiFrameId;
            $patternDemiFrame['pattern_id'] = $params -> patternId;
            $patternDemiFrames[] = $patternDemiFrame;
        }
        PatternDemiFrame::insert($patternDemiFrames);
        return $err;
    }

    public function getCreateParams(Request $request){
        $err = new Error();
        $patternId = $request->input('patternId');
        $demiFrame = $request->input('demiFrame');
        if(is_null($patternId)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('请选择生产方式');
            return $err;
        }
        if(count($demiFrame) == 0){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('请添加半成品卡纸');
            return $err;
        }
        $data = new \stdClass();
        $data -> patternId = $patternId;
        $data -> demiFrame = json_decode(json_encode($demiFrame));
        $err->setData($data);
        return $err;
    }

    public function edit($id){
        $err = new Error();
        $pattern = Pattern::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $demiFrame = DemiFrame::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $patternDemiFrame = PatternDemiFrame::where(IekModel::ID,$id)
            ->where(IekModel::CONDITION)
            ->get();
        $data = new \stdClass();
        $data->pattern = $pattern;
        $data->demiFrame = $demiFrame;
        $data->patternDemiFrame = $patternDemiFrame;
        if($patternDemiFrame->isEmpty()){
            $data = null;
        }
        $err->setData($data);
        return view('admin.systemSetting.product.patternDemiFrame',['result'=>$err]);
    }

    public function modify(Request $request , $id){
        $err = new Error();
        try{
            DB::beginTransaction();
            PatternDemiFrame::where(IekModel::ID,$id)
                ->update([
                    IekModel::IS_MODIFY => true
                ]);
            $patternDemiFrame = self::createPatternDemiFrame($request);
            if($patternDemiFrame -> statusCode != 0){
                throw new \Exception('rollback');
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            if(isset($patternDemiFrame)){
                $err = $patternDemiFrame;
            }
        }
        return view('message.formResult',['result'=>$err]);
    }

    public function lists(Request $request){
        $tableName = 'tblPatternDemiFrames';
        $start = $request->input('start');
        $getAll = new PrivilegeController();
        $res = $getAll->getAllList($request , $tableName);
        if(is_null($start)){
            $params = new \stdClass();
            $params-> type = 'patternDemiFrame';
            $params-> url = 'patternDemiFrame';
            $field = $res;
            return view('tableData.index',compact('field', 'params'));
        }
        return response()->json($res);
    }

    public function del(){
        $model = new PatternDemiFrame();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }

    public function recover(){
        $model = new PatternDemiFrame();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
}