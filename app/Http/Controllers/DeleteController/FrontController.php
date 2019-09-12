<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/4/4
 * Time: 11:19
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\DemiFront;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\Front;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\FrontPattern;
use App\IekModel\Version1_0\FrontPatternDemi;
use App\IekModel\Version1_0\FrontPatternParam;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Pattern;
use App\IekModel\Version1_0\ProductTemporary;
use Illuminate\Support\Facades\DB;

class FrontController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * 玻璃列表显示
     */
    public function index(){
        $model = new Front();
        $type = 'front';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * 成品玻璃添加页面
     *
     */
    public function create(){
        $pattern = Pattern::with('patternParam.param')
            ->with('patternDemiFront.demiFront')
            ->where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $semis = DemiFront::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        return view('admin.complete.addFront',compact('semis','pattern'));
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
//        dd($input);
        $front = new Front();
        $front->width    = $input['width'];
        $front->height   = $input['height'];
        $front->weight   = $input['weight'];
        $front->price    = $input['price'];
        $front->currency = $input['currency'];
        $front->is_official = true;
        $front->is_default = $input['isDefault'];
        $front->save();
        if(!is_null($input['frontPattern'])){
            foreach($input['frontPattern'] as $item){
                $frontPattern = new FrontPattern();
                $frontPattern->pattern_id = $item['patternId'];
                $frontPattern->front_id = $front->id;
                $frontPattern->price = $item['price'];
                $frontPattern->weight = $item['weight'];
                $frontPattern->currency = $input['currency'];
                $frontPattern->save();
                $err ->frontPattern = $frontPattern;
                if(!is_null($item['param'])){
                    foreach($item['param'] as $val){
                        $frontPatternParam = new FrontPatternParam();
                        $frontPatternParam->front_pattern_id = $frontPattern->id;
                        $frontPatternParam->val = $val['val'];
                        $frontPatternParam->param_id = $val['id'];
                        $frontPatternParam->save();
                        $err ->frontPatternParam = $frontPatternParam;
                    }
                }
                if(!is_null($item['demiFront'])){
                    foreach($item['demiFront'] as $v){
                        $frontPatternDemi = new FrontPatternDemi();
                        $frontPatternDemi->front_pattern_id = $frontPattern->id;
                        $frontPatternDemi->weight = $v['weight'];
                        $frontPatternDemi->price = $v['price'];
                        $frontPatternDemi->currency = $input['currency'];
                        $frontPatternDemi->demi_front_id = $v['id'];
                        $frontPatternDemi->save();
                        $err ->frontPatternDemi = $frontPatternDemi;
                    }
                }
            }
        }
        $err ->front = $front;
        return $err;
    }
    /**
     * 成品玻璃修改页面
     *
     */
    public function edit($id){
        $result = Front::with(['frontPattern'=>
            function($query){
                $query->with('frontPatternParam.param')
                    ->with('frontPatternDemi.demiFront')
                    ->where(IekModel::CONDITION);
            }])
            ->where(IekModel::CONDITION)
            ->find($id);
        $pattern = Pattern::with('patternParam.param')
            ->with('patternDemiFront.demiFront')
            ->where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $semis = DemiFront::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        return view('admin.complete.editFront',compact('result', 'pattern','semis'));
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
            Front::where(IekModel::ID,$id)
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
                $use_front = Front::whereHas('productFront')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_front)){
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
                    $re = Front::where(IekModel::ID,$id)
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
        $model = new Front();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
}