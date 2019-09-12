<?php

namespace App\Http\Controllers\Table;

use App\Http\Controllers\Admin\PrivilegeController;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Param;
use App\IekModel\Version1_0\Pattern;
use App\IekModel\Version1_0\PatternParam;
use App\IekModel\Version1_0\ProductTemporary;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PatternController extends Controller
{
    /**
     *查看生产模式
     */
    public function patternList(Request $request){
        $tableName = 'tblPatterns';
        $start = $request->input('start');
        $getAll = new PrivilegeController();
        $res = $getAll->getAllList($request , $tableName);
        if(is_null($start)){
            $params = new \stdClass();
            $params-> type = 'pattern';
            $params-> url = 'pattern';
            $field = $res;
            return view('tableData.index',compact('field', 'params'));
        }
        return response()->json($res);
    }
    /**
     * 添加页
     */
    public function patternAdd(){
        $err = new Error();
        $param = Param::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        if($param->isEmpty()){
            $param = null;
        }
        $err->setData($param);
        return view('admin.systemSetting.product.patternAdd',['result'=>$err]);
    }
    /**
     * 提交添加数据
     */
    public function createPattern(Request $request){
        $pattern = self::create($request);
        return view('message.formResult',['result'=>$pattern]);
    }

    public function create(Request $request){
        $err = new Error();
        $params = self::getCreateParams($request);
        if($params -> statusCode != 0){
            return $params;
        }
        $params = $params->data;
        try{
            DB::beginTransaction();
            $pattern = new Pattern();
            $pattern -> description = $params -> description;
            $pattern -> name = $params -> name;
            $pattern -> save();

            $patternParams = [];
            foreach ($params->param as $item=>$value){
                $patternParam = [];
                $patternParam['pattern_id'] = $pattern->{IekModel::ID};
                $patternParam['param_id'] = $value->id;
                $patternParam['val'] = $value->val;
                $patternParam['is_default'] = $value->isDefault;
                $patternParams[] = $patternParam;
            }
            PatternParam::insert($patternParams);
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
        $param = json_decode(json_encode($request->input('param')));
        if(is_null($name)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('请输入名字');
            $err->setData('name');
            return $err;
        }
        if(is_null($param)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('请输入参数');
            $err->setData('param');
            return $err;
        }
        $params = new \stdClass();
        $params -> name = $name;
        $params -> param = $param;
        $params -> isDefault = $request->input('isDefault');
        $params -> description = $request -> input('description');
        $err->setData($params);
        return $err;
    }

    /**
     * 生产参数列表
     */
    public function patternParamList(Request $request , $id){
        $tableName = 'tblPatternParams';
        $start = $request->input('start');
        $getAll = new PrivilegeController();
        $res = $getAll->getAllList($request , $tableName , ['pattern_id'=>$id]);
        if(is_null($start)){
            $params = new \stdClass();
            $params-> type = 'patternParam';
            $params-> url = 'patternParam';
            $field = $res;
            return view('tableData.index',compact('field', 'params'));
        }
        return response()->json($res);
    }

    /**
     * 修改页
     */
    public function patternEdit($id){
        $err = new Error();
        $pattern = Pattern::where(IekModel::CONDITION)
            ->with(['patternParam'=>function($q){
                $q->where(IekModel::CONDITION);
            }])
            ->where(IekModel::ID,$id)
            ->first();
        if(is_null($pattern)){
            $err->setError(Errors::INVALID_PARAMS);
            return view('admin.systemSetting.product.patternEdit',['result'=>$err]);
        }
        $param = Param::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        if($param -> isEmpty()){
            $param = null;
        }
        $data = new \stdClass();
        $data -> param = $param;
        $data -> pattern = $pattern;
        $err -> setData($data);
        return view('admin.systemSetting.product.patternEdit',['result'=>$err]);
    }
    /**
     * 提交修改数据
     */
    public function modifyPattern(Request $request , $id){
        $err = new Error();
        DB::beginTransaction();
        try{
            Pattern::where(IekModel::ID,$id)
                ->update([
                    IekModel::IS_MODIFY => true
                ]);
            $pattern = self::create($request);
            if($pattern->statusCode != 0){
                throw new \Exception('rollback');
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            if(isset($pattern)){
                $err = $pattern;
            }
        }
        return view('message.formResult',['result'=>$err]);

    }
    /**
     * 删除生产模式
     */
    public function deletePattern(){
        $err = new Error();
        $ids = request()->input('ids');
        DB::beginTransaction();
        try{
            $temporary = ProductTemporary::where(IekModel::CONDITION)->pluck(IekModel::DATA);
            foreach($ids as $id){
                $use_border = Pattern::whereHas('borderPattern.border.productBorder')
                    ->with('borderPattern.border')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_border)) {
                    $res = $this->limitBorder($use_border);
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
                }
                $use_frame = Pattern::whereHas('framePattern.frame.productFrame')
                    ->with('framePattern.frame')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_frame)) {
                    $res = $this->limitFrame($use_frame);
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
                }
                $use_front = Pattern::whereHas('frontPattern.front.productFront')
                    ->with('frontPattern.front')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_front)) {
                    $res = $this->limitFront($use_front);
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
                }
                $use_back = Pattern::whereHas('backPattern.back.productBack')
                    ->with('backPattern.back')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_back)) {
                    $res = $this->limitBack($use_back);
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
                }
                $use_core = Pattern::whereHas('corePattern.core.productCore')
                    ->with('corePattern.core')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_core)) {
                    $res = $this->limitCore($use_core);
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
                }
                if(!is_null($use_border) || !is_null($use_frame) || !is_null($use_front) ||
                    !is_null($use_back) || !is_null($use_core)) {
                    $err->setError(Errors::INVALID_PARAMS);
                    $err->setMessage('有产品正在使用这条数据，请不要删除');
                    return response()->json($err);
                }else{
                    $re = Pattern::where(IekModel::ID,$id)
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
    public function limitBorder($use){
        if(!$use->borderPattern->isEmpty()){
            $borderId = [];
            foreach($use->borderPattern as $pattern){
                if(!is_null($pattern->border)){
                    $borderId[] = $pattern->border->id;
                }
            }
            return $borderId;
        }
    }
    public function limitFrame($use){
        if(!is_null($use->framePattern)){
            $frameId = [];
            foreach($use->framePattern as $pattern){
                if(!is_null($pattern->frame)){
                    $frameId[] = $pattern->frame->id;
                }
            }
            return $frameId;
        }
    }
    public function limitFront($use){
        if(!is_null($use->frontPattern)){
            $frontId = [];
            foreach($use->frontPattern as $pattern){
                if(!is_null($pattern->front)){
                    $frontId[] = $pattern->front->id;
                }
            }
            return $frontId;
        }
    }
    public function limitBack($use){
        if(!is_null($use->backPattern)){
            $backId = [];
            foreach($use->backPattern as $pattern){
                if(!is_null($pattern->back)){
                    $backId[] = $pattern->back->id;
                }
            }
            return $backId;
        }
    }
    public function limitCore($use){
        if(!is_null($use->corePattern)){
            $coreId = [];
            foreach($use->corePattern as $pattern){
                if(!is_null($pattern->core)){
                    $coreId[] = $pattern->core->id;
                }
            }
            return $coreId;
        }
    }
    /**
     * 恢复生产模式
     */
    public function recoverPattern(){
        $model = new Pattern();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * 删除生产参数
     */
    public function deletePatternParam(){
        $model = new PatternParam();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * 恢复生产参数
     */
    public function recoverPatternParam(){
        $model = new PatternParam();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }

}
