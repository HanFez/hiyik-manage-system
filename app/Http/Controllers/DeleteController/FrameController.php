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
use App\IekModel\Version1_0\DemiFrame;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\Frame;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\FrameHole;
use App\IekModel\Version1_0\FramePattern;
use App\IekModel\Version1_0\FramePatternDemi;
use App\IekModel\Version1_0\FramePatternParam;
use App\IekModel\Version1_0\Hole;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Pattern;
use App\IekModel\Version1_0\ProductTemporary;
use Illuminate\Support\Facades\DB;

class FrameController extends Controller
{
    /**
     * 成品卡纸列表
     */
    public function index(){
        $model = new Frame();
        $type = 'frame';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }

    /**
     * 成品卡纸添加页面
     * view
     */
    public function create(){
        $pattern = Pattern::with('patternParam.param')
            ->with('patternDemiFrame.demiFrame')
            ->where(IekModel::CONDITION)
            ->get();
        $hole = Hole::with('shape')
            ->where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $semis = DemiFrame::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        return view('admin.complete.addFrame',compact('hole','pattern','semis'));
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
        $frame = new Frame();
        $frame->width    = $input['width'];
        $frame->height   = $input['height'];
        $frame->weight   = $input['weight'];
        $frame->price    = $input['price'];
        $frame->currency = $input['currency'];
        $frame->is_official = true;
        $frame->is_default = $input['isDefault'];
        $frame->save();
        if(!is_null($input['framePattern'])){
            foreach($input['framePattern'] as $item){
                $framePattern = new FramePattern();
                $framePattern->pattern_id = $item['patternId'];
                $framePattern->frame_id = $frame->id;
                $framePattern->price = $item['price'];
                $framePattern->weight = $item['weight'];
                $framePattern->currency = $input['currency'];
                $framePattern->save();
                $err ->framePattern = $framePattern;
                if(!is_null($item['param'])){
                    foreach($item['param'] as $val){
                        $framePatternParam = new FramePatternParam();
                        $framePatternParam->frame_pattern_id = $framePattern->id;
                        $framePatternParam->val = $val['val'];
                        $framePatternParam->param_id = $val['id'];
                        $framePatternParam->save();
                        $err ->framePatternParam = $framePatternParam;
                    }
                }
                if(!is_null($item['demiFrame'])){
                    foreach($item['demiFrame'] as $v){
                        $framePatternDemi = new FramePatternDemi();
                        $framePatternDemi->frame_pattern_id = $framePattern->id;
                        $framePatternDemi->weight = $v['weight'];
                        $framePatternDemi->price = $v['price'];
                        $framePatternDemi->currency = $input['currency'];
                        $framePatternDemi->demi_frame_id = $v['id'];
                        $framePatternDemi->save();
                        $err ->framePatternDemi = $framePatternDemi;
                    }
                }
            }
        }
        if(!is_null($input['frameHole'])){
            foreach($input['frameHole'] as $hole){
                $frameHole = new FrameHole();
                $frameHole->frame_id = $frame->id;
                $frameHole->hole_id = $hole['holeId'];
                $frameHole->start_x = $hole['startX'];
                $frameHole->start_y = $hole['startY'];
                $frameHole->save();
                $err->frameHole = $frameHole;
            }
        }
        $err ->frame = $frame;
        return $err;
    }
    /**
     * 成品卡纸修改页面
     * edit view
     */
    public function edit($id){
        $result = Frame::with(['framePattern'=> function($query){
                $query->with('framePatternParam.param')
                    ->with('framePatternDemi.demiFrame')
                    ->where(IekModel::CONDITION);
                }])
            ->with('frameHole.hole.shape')
            ->where(IekModel::CONDITION)
            ->find($id);
        $pattern = Pattern::with('patternParam.param')
            ->with('patternDemiFrame.demiFrame')
            ->where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $hole = Hole::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $semis = DemiFrame::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        return view('admin.complete.editFrame',compact('result','pattern','hole','semis'));
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
            Frame::where(IekModel::ID,$id)
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
                $use_frame = Frame::whereHas('productFrame')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_frame)){
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
                    $re = Frame::where(IekModel::ID,$id)
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
        $model = new Frame();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }

}
