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
use App\IekModel\Version1_0\Back;
use App\IekModel\Version1_0\BackPattern;
use App\IekModel\Version1_0\BackPatternDemi;
use App\IekModel\Version1_0\BackPatternParam;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\DemiBack;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Pattern;
use App\IekModel\Version1_0\ProductTemporary;
use Illuminate\Support\Facades\DB;

class BackController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * 背板列表显示
     */
    public function index(){
        $model = new Back();
        $type = 'back';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * 成品背板添加页面
     *
     */
    public function create(){
        $pattern = Pattern::with('patternParam.param')
            ->with('patternDemiBack.demiBack')
            ->where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $semis = DemiBack::where(IekModel::IS_MODIFY,false)
            ->where(IekModel::CONDITION)
            ->get();
        return view('admin.complete.addBack',compact('pattern','semis'));
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
        return view('message.formResult', ['result' => $err]);
    }
    /**
     * 保存数据
     */
    public function createParams(){
        $err = new Error();
        $input = request()->except('_token');
        $back = new Back();
        $back -> width    = $input['width'];
        $back -> height   = $input['height'];
        $back -> weight   = $input['weight'];
        $back -> price    = $input['price'];
        $back -> currency = $input['currency'];
        $back -> is_official = true;
        $back -> is_default = $input['isDefault'];
        $back -> save();
        if(!is_null($input['backPattern'])){
            foreach($input['backPattern'] as $item){
                $backPattern = new BackPattern();
                $backPattern -> pattern_id = $item['patternId'];
                $backPattern -> back_id = $back->id;
                $backPattern -> price = $item['price'];
                $backPattern -> weight = $item['weight'];
                $backPattern -> currency = $input['currency'];
                $backPattern -> save();
                $err -> backPattern = $backPattern;
                if(!is_null($item['param'])){
                    foreach($item['param'] as $val){
                        $backPatternParam = new BackPatternParam();
                        $backPatternParam -> back_pattern_id = $backPattern->id;
                        $backPatternParam -> val = $val['val'];
                        $backPatternParam -> param_id = $val['id'];
                        $backPatternParam -> save();
                        $err ->backPatternParam = $backPatternParam;
                    }
                }
                if(!is_null($item['demiBack'])){
                    foreach($item['demiBack'] as $v){
                        $backPatternDemi = new BackPatternDemi();
                        $backPatternDemi -> back_pattern_id = $backPattern->id;
                        $backPatternDemi -> weight = $v['weight'];
                        $backPatternDemi -> price = $v['price'];
                        $backPatternDemi -> currency = $input['currency'];
                        $backPatternDemi -> demi_back_id = $v['id'];
                        $backPatternDemi -> save();
                        $err -> backPatternDemi = $backPatternDemi;
                    }
                }
            }
        }
        $err -> back = $back;
        return $err;
    }
    /**
     * 成品背板修改页面
     *
     */
    public function edit($id){
        $result = Back::with(['backPattern' =>
            function($query){
                $query->with('backPatternParam.param')
                    ->with('backPatternDemi.demiBack')
                    ->where(IekModel::CONDITION);
            }])
            ->where(IekModel::CONDITION)
            ->find($id);
        $pattern = Pattern::with('patternParam.param')
            ->with('patternDemiBack.demiBack')
            ->where(IekModel::CONDITION)
            ->get();
        $semis = DemiBack::where(IekModel::IS_MODIFY,false)
            ->where(IekModel::CONDITION)
            ->get();
        return view('admin.complete.editBack',compact('result','pattern','semis'));
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
            Back::where(IekModel::ID,$id)
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
        return view('message.formResult', ['result' => $err]);
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
                $use_back = Back::whereHas('productBack')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_back)){
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
                    $re = Back::where(IekModel::ID,$id)
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
        $model = new Back();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }

}