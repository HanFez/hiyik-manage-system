<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/5/9
 * Time: 20:05
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\Hole;
use App\IekModel\Version1_0\HolePattern;
use App\IekModel\Version1_0\HolePatternDemi;
use App\IekModel\Version1_0\HolePatternParam;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Pattern;
use App\IekModel\Version1_0\ProductTemporary;
use App\IekModel\Version1_0\Shape;
use Illuminate\Support\Facades\DB;

class HoleController extends Controller
{
    /**
     * 成品卡纸列表
     */
    public function index(){
        $model = new Hole();
        $type = 'hole';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }

    /**
     * 成品卡纸添加页面
     *
     */
    public function create(){
        $pattern = Pattern::with('patternParam.param')
            ->with('patternDemiHole.demiHole')
            ->where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $shape = Shape::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        return view('admin.complete.addHole',compact('pattern','shape'));
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
        $hole = new Hole();
        $hole->shape_id = $input['shapeId'];
        $hole->width    = $input['width'];
        $hole->height   = $input['height'];
        $hole->weight   = $input['weight'];
        $hole->price    = $input['price'];
        $hole->currency = $input['currency'];
        $hole->is_official = true;
//        $hole->is_default = $input['isDefault'];
        $hole->save();
        if(!is_null($input['holePattern'])){
            foreach($input['holePattern'] as $item){
                $holePattern = new HolePattern();
                $holePattern->pattern_id = $item['patternId'];
                $holePattern->hole_id = $hole->id;
                $holePattern->price = $item['price'];
                $holePattern->weight = $item['weight'];
                $holePattern->currency = $input['currency'];
                $holePattern->save();
                $err ->holePattern = $holePattern;
                if(!is_null($item['param'])){
                    foreach($item['param'] as $val){
                        $holePatternParam = new HolePatternParam();
                        $holePatternParam->hole_pattern_id = $holePattern->id;
                        $holePatternParam->val = $val['val'];
                        $holePatternParam->param_id = $val['id'];
                        $holePatternParam->save();
                        $err ->holePatternParam = $holePatternParam;
                    }
                }
                if(!is_null($item['demiHole'])){
                    foreach($item['demiHole'] as $v){
                        $holePatternDemi = new HolePatternDemi();
                        $holePatternDemi->hole_pattern_id = $holePattern->id;
                        $holePatternDemi->weight = $v['weight'];
                        $holePatternDemi->price = $v['price'];
                        $holePatternDemi->currency = $input['currency'];
                        $holePatternDemi->demi_frame_id = $v['id'];
                        $holePatternDemi->save();
                        $err ->holePatternDemi = $holePatternDemi;
                    }
                }
            }
        }
        $err ->hole = $hole;
        return $err;
    }
    /**
     * 成品卡纸修改页面
     *
     */
    public function edit($id){
        $result = Hole::with(['holePattern'=>
            function($query){
                $query->with('holePatternParam.param')
                    ->with('holePatternDemi.demiHole')
                    ->where(IekModel::CONDITION);
            }])
            ->with('shape')
            ->where(IekModel::CONDITION)
            ->find($id);
        $pattern = Pattern::with('patternParam.param')
            ->with('patternDemiHole.demiHole')
            ->where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $shape = Shape::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        return view('admin.complete.editHole',compact('result','pattern','shape'));
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
            Hole::where(IekModel::ID,$id)
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
                $use_hole = Hole::whereHas('frameHole.frame.productFrame')
                    ->with('frameHole.frame')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_hole)){
                    $res = $this->limitHole($use_hole);
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
                    $re = Hole::where(IekModel::ID,$id)
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

    public function limitHole($use){
        if(!$use->frameHole->isEmpty()){
            $frameId = [];
            foreach($use->frameHole as $hole){
                if(!is_null($hole->frame)){
                    $frameId[] = $hole->frame->id;
                }
            }
            return $frameId;
        }
    }
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * 恢复数据
     */
    public function recover(){
        $model = new Hole();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }

}