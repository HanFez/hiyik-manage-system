<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/11
 * Time: 16:42
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Product\Facade;
use App\IekModel\Version1_0\Product\Material;
use App\IekModel\Version1_0\Product\MCategory;
use App\IekModel\Version1_0\Product\PosterBorderMaterialDefine;
use Illuminate\Support\Facades\DB;

class BorderMaterialDefineController extends Controller
{
/**
     * BorderMaterialDefine's add page
     */
    public function add(){
        $materials = Material::where(IekModel::CONDITION)->get();
        $mcs = MCategory::where(IekModel::CONDITION)->get();
        $facades = Facade::where(IekModel::CONDITION)->get();
        return view('product.define.borderMaterialDefine',compact('materials','mcs','facades'));
    }
    /**
     * BorderMaterialDefine's add data deal
     */
    public function create(){
        $err = new Error();
        $req = request()->all();
        //验证
        $verify = $this->verify($req,null);
        if($verify) return $verify;
        $model = new Material();
        $limit = self::limitBorder($model,$req['materialId']);
        if(is_null($limit)){
            $err->setError(Errors::NOT_FOUND);
            $err->setMessage('材料关系不完整，请先补充相应材料截面数据或材料纹理数据再来添加。');
            return view('message.formResult',['result'=>$err]);
        }
        $rgba = turnInt($req['r'],$req['g'],$req['b'],$req['a']);
        try{
            DB::beginTransaction();
            $bmd = new PosterBorderMaterialDefine();
            $bmd->name = $req['name'];
            $bmd->description = $req['des'];
            $bmd->material_id = $req['materialId'];
            $bmd->category_id = $req['categoryId'];
            $bmd->facade_id = $req['facadeId'];
            $bmd->phy_depth = $req['phyDepth'];//侧面的尺寸
            $bmd->phy_height = $req['phyHeight'];//后面的尺寸
            $bmd->phy_visual_height = $req['phyVisualHeight'];//正面的可视尺寸
            $bmd->phy_press_height = $req['phyPressHeight'];//压画高度
            $bmd->phy_press_height_offset = $req['phyPressHeightOffset'];//压画高度偏移
            $bmd->phy_press_depth = $req['phyPressDepth'];//压画深度
            $bmd->phy_press_depth_offset = $req['phyPressDepthOffset'];//压画深度偏移
            $bmd->phy_slot_height = $req['phySlotHeight'];
            $bmd->phy_slot_height_offset = $req['phySlotHeightOffset'];
            $bmd->phy_slot_depth = $req['phySlotDepth'];
            $bmd->phy_slot_depth_offset = $req['phySlotDepthOffset'];
            $bmd->phy_length_max = $req['phyLengthMax'];
            $bmd->phy_length_min = $req['phyLengthMin'];
            $bmd->color_name = $req['colorName'];
            $bmd->rgba = $rgba;
            $bmd->price = $req['price'];
            $bmd->price_unit = $req['priceUnit'];
            $bmd->currency = $req['currency'];
            $bmd->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"保存成功","保存失败",$bmd);
    }
    /**
     * BorderMaterialDefine's all data list
     */
    public function listBMD(){
        $model = new PosterBorderMaterialDefine();
        $type = 'border-material-define';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * BorderMaterialDefine's data detail
     */
    public function showBMD(){
        //
    }
    /**
     * BorderMaterialDefine's edit page
     */
    public function edit($id){
        $materials = Material::where(IekModel::CONDITION)->get();
        $mcs = MCategory::where(IekModel::CONDITION)->get();
        $facades = Facade::where(IekModel::CONDITION)->get();
        $action = 'edit';
        $bmd = PosterBorderMaterialDefine::find($id);
        $rgba = turnRgba($bmd->rgba);
        return view('product.define.borderMaterialDefine',compact('materials','facades','mcs','action','bmd','rgba'));
    }
    /**
     * BorderMaterialDefine's edit data deal
     */
    public function update($id){
        $err = new Error();
        $req_u = request()->all();
        //验证
        $verify = $this->verify($req_u,$id);
        if($verify) return $verify;
        $model = new Material();
        $limit = self::limitBorder($model,$req_u['materialId']);
        if(is_null($limit)){
            $err->setError(Errors::NOT_FOUND);
            $err->setMessage('材料关系不完整，请先补充相应材料截面数据或材料纹理数据再来添加。');
            return view('message.formResult',['result'=>$err]);
        }
        $rgba = turnInt($req_u['r'],$req_u['g'],$req_u['b'],$req_u['a']);
        try{
            DB::beginTransaction();
            $bmd = PosterBorderMaterialDefine::where(IekModel::ID,$id)
                ->update([
                    IekModel::NAME => $req_u['name'],
                    IekModel::DESC => $req_u['des'],
                    IekModel::MATERIAL_ID => $req_u['materialId'],
                    IekModel::CATEGORY_ID => $req_u['categoryId'],
                    IekModel::FACADE_ID => $req_u['facadeId'],
                    IekModel::PHY_DEPTH => $req_u['phyDepth'],
                    IekModel::PHY_HEIGHT => $req_u['phyHeight'],
                    IekModel::VISUAL_HEIGHT => $req_u['phyVisualHeight'],
                    IekModel::PRESS_HEIGHT => $req_u['phyPressHeight'],
                    IekModel::PRESS_HEIGHT_OFFSET => $req_u['phyPressHeightOffset'],
                    IekModel::PRESS_DEPTH => $req_u['phyPressDepth'],
                    IekModel::PRESS_DEPTH_OFFSET => $req_u['phyPressDepthOffset'],
                    IekModel::SLOT_HEIGHT=> $req_u['phySlotHeight'],
                    IekModel::SLOT_HEIGHT_OFFSET => $req_u['phySlotHeightOffset'],
                    IekModel::SLOT_DEPTH => $req_u['phySlotDepth'],
                    IekModel::SLOT_DEPTH_OFFSET => $req_u['phySlotDepthOffset'],
                    IekModel::LENGTH_MAX => $req_u['phyLengthMax'],
                    IekModel::LENGTH_MIN => $req_u['phyLengthMin'],
                    IekModel::COLOR_NAME => $req_u['colorName'],
                    IekModel::RGBA => $rgba,
                    IekModel::PRICE => $req_u['price'],
                    IekModel::PRICE_UNIT => $req_u['priceUnit'],
                    IekModel::CURRENCY => $req_u['currency'],
                ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"修改成功","修改失败",$bmd);
    }
    /**
     * delete BorderMaterialDefine's record
     */
    public function del(){
        $model = new PosterBorderMaterialDefine();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover BorderMaterialDefine's record
     */
    public function cover(){
        $model = new PosterBorderMaterialDefine();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * validate
     */
    public function verify($req,$id){
        $name = $req['name'];
        $des = $req['des'];
        $mid = $req['materialId'];
        $cid = $req['categoryId'];
        $fid = $req['facadeId'];
        $phyDepth = $req['phyDepth'];
        $phyHeight = $req['phyHeight'];
        $visualHeight = $req['phyVisualHeight'];
        $pressHeight = $req['phyPressHeight'];
        $pressHeightOffset = $req['phyPressHeightOffset'];
        $pressDepth = $req['phyPressDepth'];
        $pressDepthOffset = $req['phyPressDepthOffset'];
        $slotHeight = $req['phySlotHeight'];
        $slotHeightOffset = $req['phySlotHeightOffset'];
        $slotDepth = $req['phySlotDepth'];
        $slotDepthOffset = $req['phySlotDepthOffset'];
        $colorName = $req['colorName'];
        $r = $req['r'];
        $g = $req['g'];
        $b = $req['b'];
        $a = $req['a'];
        $unit = $req['priceUnit'];
        if(is_null($name)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入名称",$name);
        }
        if(is_null($des)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入描述",$des);
        }
        if(is_null($mid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择材料",$mid);
        }
        if(is_null($cid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择材料分类",$cid);
        }
        if(is_null($fid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择材料外观",$fid);
        }
        if(is_null($phyDepth)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入物理深度",$phyDepth);
        }
        if(is_null($phyHeight)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入物理高度",$phyHeight);
        }
        if(is_null($visualHeight)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入可视高度",$visualHeight);
        }
        if(is_null($pressHeight)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入压画高度",$pressHeight);
        }
        if(is_null($pressHeightOffset)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入压画高度偏移",$pressHeightOffset);
        }
        if(is_null($pressDepth)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入压画深度",$pressDepth);
        }
        if(is_null($pressDepthOffset)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入压画深度偏移",$pressDepthOffset);
        }
        if(is_null($slotHeight)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入槽高度",$slotHeight);
        }
        if(is_null($slotHeightOffset)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入槽高度偏移",$slotHeightOffset);
        }
        if(is_null($slotDepth)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入槽深度",$slotDepth);
        }
        if(is_null($slotDepthOffset)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入槽深度偏移",$slotDepthOffset);
        }
        if(is_null($colorName)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入颜色名称",$colorName);
        }
        if(is_null($r) || is_null($g) || is_null($b) || is_null($a)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入颜色rgba值",$r);
        }
        if(is_null($unit)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入价格单位",$unit);
        }
        $has = PosterBorderMaterialDefine::where(IekModel::MATERIAL_ID,$mid)
            ->where(IekModel::CATEGORY_ID,$cid)
            ->first();
        if(!is_null($has)){
            if(is_null($id)){
                return $this->viewReturn(Errors::EXIST,'此记录已添加',$name);
            }else{
                if($has->id != $id){
                    return $this->viewReturn(Errors::EXIST,'此记录已添加',$name);
                }
            }
        }
    }
}
?>
