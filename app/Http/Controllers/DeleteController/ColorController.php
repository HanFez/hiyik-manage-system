<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/3/6
 * Time: 16:26
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Color;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ProductTemporary;
use Illuminate\Support\Facades\DB;

class ColorController extends Controller
{
    /**
     * 颜色列表
     */
    public function colorList(){
        $model = new Color();
        $type = 'color';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * 添加页
     */
    public function colorAdd(){
        return view('admin.systemSetting.product.colorAdd');
    }
    /**
     * 添加颜色
     */
    public function createColor(){
        $err = new Error();
        DB::beginTransaction();
        try{
            $err = $this->createParams();
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
        }
        return view('message.formResult',['result'=>$err]);
    }
    public function createParams(){
        $err = new Error();
        $name = request()->input('colorName');
        $colorRgb = request()->input('colorRgb');
        $checkColor = $this->checkParams();
        if($checkColor -> statusCode != 0){
            return $checkColor;
        }
        $str = str_replace(array('rgb(',')'),array('',''),$colorRgb);
        $rgb = explode(',',$str);
        $r = $rgb[0];
        $g = $rgb[1];
        $b = $rgb[2];
        $description = request()->input('colorDescription');
        $color = new Color();
        $color->name = $name;
        $color->r = $r;
        $color->g = $g;
        $color->b = $b;
        $color->description = $description;
        $color->is_official = true;
        $color->save();
        $err->setData($color);
        return $err;
    }
    public function checkParams(){
        $err = new Error();
        $name = request()->input('colorName');
        if(is_null($name) || empty($name)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('请输入名称');
            $err->setData('colorName');
            return $err;
        }
        $ckName = Color::checkName($name);
        if($ckName){
            $err->setError(Errors::EXIST);
            $err->setMessage('名称已使用');
            $err->setData('colorName');
            return $err;
        }
        $colorRgb = request()->input('colorRgb');
        if(is_null($colorRgb)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('请选择颜色并确保格式为：rgb(0,0,0)');
            $err->setData('colorRgb');
            return $err;
        }else{
            $str = substr($colorRgb,0,3);
            if($str != 'rgb'){
                $err->setError(Errors::INVALID_PARAMS);
                $err->setMessage('rgb颜色格式不正确（格式为：rgb(0,0,0)）');
                $err->setData('colorRgb');
                return $err;
            }
            $str = str_replace(array('rgb(',')'),array('',''),$colorRgb);
            $rgb = explode(',',$str);
            $r = $rgb[0];
            $g = $rgb[1];
            $b = $rgb[2];
            $ckRgb = Color::checkRgb($r,$g,$b);
            if($ckRgb){
                $err->setError(Errors::EXIST);
                $err->setMessage('颜色已存在');
                $err->setData('colorRgb');
                return $err;
            }
        }
        return $err;
    }
    /**
     *修改颜色页
     */
    public function colorEdit($id){
        $color = Color::find($id);
        return view('admin.systemSetting.product.colorEdit',compact('color'));
    }
    /**
     * 修改颜色
     */
    public function modifyColor($id){
        $err = new Error();
        DB::beginTransaction();
        try{
            Color::where(IekModel::ID,$id)
                ->update([
                    IekModel::IS_MODIFY => true
                ]);
            $err = $this->createParams();
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
        }
        return view('message.formResult',['result'=>$err]);
    }
    /**
     * 删除颜色
     */
    public function deleteColor(){
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
                $use_border = Color::whereHas('texture.demiBorder.borderPatternDemi.borderPattern.border.productBorder')
                    ->with('texture.demiBorder.borderPatternDemi.borderPattern.border')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                //判断临时表使用数据
                if(!is_null($use_border)){
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
                $use_back = Color::whereHas('texture.demiBack.backPatternDemi.backPattern.back.productBack')
                    ->with('texture.demiBack.backPatternDemi.backPattern.back')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_back)){
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
                $use_frame = Color::whereHas('texture.demiFrame.framePatternDemi.framePattern.frame.productFrame')
                    ->with('texture.demiFrame.framePatternDemi.framePattern.frame')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_frame)){
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
                $use_front = Color::whereHas('texture.demiFront.frontPatternDemi.frontPattern.front.productFront')
                    ->with('texture.demiFront.frontPatternDemi.frontPattern.front')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_front)){
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
                //判断产品使用数据
                if(!is_null($use_border) || !is_null($use_back) || !is_null($use_frame) || !is_null($use_front)){
                    $err->setError(Errors::INVALID_PARAMS);
                    $err->setMessage("有产品正在使用这条数据，请不要删除");
                    return response()->json($err);
                }else{
                    $re = Color::where(IekModel::ID, $id)
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
        if(!$use->texture->isEmpty()){
            $borderId = [];
            foreach($use->texture as $tet){
                if(!$tet->demiBorder->isEmpty()){
                    foreach($tet->demiBorder as $demi){
                        if(!$demi->borderPatternDemi->isEmpty()){
                            foreach($demi->borderPatternDemi as $value){
                                if(!is_null($value->borderPattern)){
                                    if(!is_null($value->borderPattern->border)) {
                                        $borderId[] = $value->borderPattern->border->id;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $borderId;
    }
    public function limitBack($use){
        if(!$use->texture->isEmpty()){
            $backId = [];
            foreach($use->texture as $tet){
                if(!$tet->demiBack->isEmpty()){
                    foreach($tet->demiBack as $demi){
                        if(!$demi->backPatternDemi->isEmpty()){
                            foreach($demi->backPatternDemi as $value){
                                if(!is_null($value->backPattern)){
                                    if(!is_null($value->backPattern->back)) {
                                        $backId[] = $value->backPattern->back->id;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $backId;
    }
    public function limitFrame($use){
        if(!$use->texture->isEmpty()){
            $frameId = [];
            foreach($use->texture as $tet){
                if(!$tet->demiFrame->isEmpty()){
                    foreach($tet->demiFrame as $demi){
                        if(!$demi->framePatternDemi->isEmpty()){
                            foreach($demi->framePatternDemi as $value){
                                if(!is_null($value->framePattern)){
                                    if(!is_null($value->framePattern->frame)) {
                                        $frameId[] = $value->framePattern->frame->id;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $frameId;
    }
    public function limitFront($use){
        if(!$use->texture->isEmpty()) {
            $frontId = [];
            foreach ($use->texture as $tet) {
                if (!$tet->demiFront->isEmpty()) {
                    foreach ($tet->demiFront as $demi) {
                        if (!$demi->frontPatternDemi->isEmpty()) {
                            foreach ($demi->frontPatternDemi as $value) {
                                if (!is_null($value->frontPattern)) {
                                    if (!is_null($value->frontPattern->front)) {
                                        $frontId[] = $value->frontPattern->front->id;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return $frontId;
    }
    /**
     * 恢复颜色
     */
    public function recoverColor(){
        $model = new Color();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
}
?>