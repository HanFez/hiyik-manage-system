<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/4/3
 * Time: 17:22
 * 成品框
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Border;
use App\IekModel\Version1_0\BorderPattern;
use App\IekModel\Version1_0\BorderPatternDemi;
use App\IekModel\Version1_0\BorderPatternParam;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\DemiBorder;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Pattern;
use App\IekModel\Version1_0\ProductTemporary;
use Illuminate\Support\Facades\DB;

class BorderController extends Controller
{
    /**
     * 成品框列表
     */
    public function index(){
        $model = new Border();
        $type = 'border';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * 成品框添加页面
     *
     */
    public function create(){
        $pattern = Pattern::with('patternParam.param')
            ->with('patternDemiBorder.demiBorder')
            ->where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $semis = DemiBorder::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        return view('admin.complete.addBorder',compact('pattern','semis'));
    }
    /**
     * 执行数据添加
     */
    public function store(){
        $err = new Error();
        DB::beginTransaction();
        try{
            $err = $this->createParams();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
        }
        return view('message.formResult',['result'=>$err]);
    }
    /**
     * 保存数据
     */
    public function createParams(){
        $err = new Error();
        $input = request()->except('_token');
        $border = new Border();
        $border->width     = $input['width'];
        $border->height    = $input['height'];
        $border->weight    = $input['weight'];
        $border->price     = $input['price'];
        $border->currency  = $input['currency'];
        $border->is_official = true;
        $border->is_default = $input['isDefault'];
        $border->save();
        if(!is_null($input['borderPattern'])){
            foreach($input['borderPattern'] as $item) {
                $borderPattern = new BorderPattern();
                $borderPattern->pattern_id = $item['patternId'];
                $borderPattern->border_id = $border->id;
                $borderPattern->price = $item['price'];
                $borderPattern->weight = $item['weight'];
                $borderPattern->currency = $input['currency'];
                $borderPattern->save();
                $err ->borderPattern = $borderPattern;
                if(!is_null($item['param'])){
                    foreach ($item['param'] as $val) {
                        $borderPatternParam = new BorderPatternParam();
                        $borderPatternParam->border_pattern_id = $borderPattern->id;
                        $borderPatternParam->val = $val['val'];
                        $borderPatternParam->param_id = $val['id'];
                        $borderPatternParam->save();
                        $err ->borderPatternParam = $borderPatternParam;
                    }
                }
                if(!is_null($item['demiBorder'])){
                    foreach ($item['demiBorder'] as $v) {
                        $borderPatternDemi = new BorderPatternDemi();
                        $borderPatternDemi->border_pattern_id = $borderPattern->id;
                        $borderPatternDemi->weight = $v['weight'];
                        $borderPatternDemi->price = $v['price'];
                        $borderPatternDemi->currency = $input['currency'];
                        $borderPatternDemi->demi_border_id = $v['id'];
                        $borderPatternDemi->save();
                        $err ->borderPatternDemi = $borderPatternDemi;
                    }
                }
            }
        }
        $err ->border = $border;
        return $err;
    }
    /**
     * 成品框修改页面
     *
     */
    public function edit($id){
        $result = Border::with(['borderPattern'=>
            function($query){
                $query->with('borderPatternParam.param')
                    ->with('borderPatternDemi.demiBorder')
                    ->where(IekModel::CONDITION);
            }])
            ->where(IekModel::CONDITION)
            ->find($id);
        $semis = DemiBorder::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $pattern = Pattern::with('patternParam.param')
            ->with('patternDemiBorder.demiBorder')
            ->where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        return view('admin.complete.editBorder',compact('semis','result','pattern'));
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 执行数据修改
     */
    public function update($id){
        $err = new Error();
        DB::beginTransaction();
        try{
            Border::where(IekModel::ID,$id)
                ->update([
                    IekModel::IS_MODIFY => true
                ]);
            $err = $this->createParams();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
        }
        return view('message.formResult',['result'=>$err]);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 删除数据
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
                $use_border = Border::whereHas('productBorder')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_border)){
                    foreach($temporary as $tem){
                        $str = substr_count($tem,$id);
                        if($str>0){
                            $err->setError(Errors::INVALID_PARAMS);
                            $err->setMessage('有产品草稿正在使用这条数据，请不要删除');
                            return response()->json($err);
                        }
                    }
                    $err->setError(Errors::INVALID_PARAMS);
                    $err->setMessage('有产品正在使用这条数据，请不要删除');
                    return response()->json($err);
                }else{
                    $re = Border::where(IekModel::ID,$id)
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

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 恢复数据
     */
    public function recover(){
        $model = new Border();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }

}