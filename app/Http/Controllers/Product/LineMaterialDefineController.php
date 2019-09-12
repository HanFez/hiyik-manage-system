<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/18
 * Time: 17:16
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
use App\IekModel\Version1_0\Product\PosterHoleLineMaterialDefine;
use Illuminate\Support\Facades\DB;

class LineMaterialDefineController extends Controller
{
/**
     * holeLineMaterialDefine's add page
     */
    public function add(){
        $materials = Material::where(IekModel::CONDITION)->get();
        $mcs = MCategory::where(IekModel::CONDITION)->get();
        $facades = Facade::where(IekModel::CONDITION)->get();
        return view('product.define.lineMaterialDefine',compact('materials','mcs','facades'));
    }
    /**
     * holeLineMaterialDefine's add data deal
     */
    public function create(){
        $req = request()->all();
        //验证
        $verify = $this->verify($req,null);
        if($verify) return $verify;
        $rgba = turnInt($req['r'],$req['g'],$req['b'],$req['a']);
        try{
            DB::beginTransaction();
            $lineMaterial = new PosterHoleLineMaterialDefine();
            $lineMaterial->name = $req['name'];
            $lineMaterial->description = $req['des'];
            $lineMaterial->material_id = $req['materialId'];
            $lineMaterial->category_id = $req['categoryId'];
            $lineMaterial->facade_id = $req['facadeId'];
            $lineMaterial->phy_depth = $req['phyDepth'];
            $lineMaterial->phy_height = $req['phyHeight'];
            $lineMaterial->phy_content_depth = $req['phyContentDepth'];
            $lineMaterial->phy_press_height = $req['phyPressHeight'];
            $lineMaterial->phy_press_depth = $req['phyPressDepth'];
            $lineMaterial->phy_press_offset = $req['phyPressOffset'];
            $lineMaterial->phy_slot_height = $req['phySlotHeight'];
            $lineMaterial->phy_slot_depth = $req['phySlotDepth'];
            $lineMaterial->phy_slot_offset = $req['phySlotOffset'];
            $lineMaterial->color_name = $req['colorName'];
            $lineMaterial->rgba = $rgba;
            $lineMaterial->price = $req['price'];
            $lineMaterial->price_unit = $req['priceUnit'];
            $lineMaterial->currency = $req['currency'];
            $lineMaterial->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"保存成功","保存失败",$lineMaterial);
    }
    /**
     * holeLineMaterialDefine's all data list
     */
    public function listHLMD(){
        $model = new PosterHoleLineMaterialDefine();
        $type = 'line-material-define';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * holeLineMaterialDefine's data detail
     */
    public function showHLMD(){
        //
    }
    /**
     * holeLineMaterialDefine's edit page
     */
    public function edit($id){
        $materials = Material::where(IekModel::CONDITION)->get();
        $mcs = MCategory::where(IekModel::CONDITION)->get();
        $facades = Facade::where(IekModel::CONDITION)->get();
        $action = 'edit';
        $lineMaterial = PosterHoleLineMaterialDefine::find($id);
        $rgba = turnRgba($lineMaterial->rgba);
        return view('product.define.lineMaterialDefine',compact('materials','facades','mcs','action','lineMaterial','rgba'));
    }
    /**
     * holeLineMaterialDefine's edit data deal
     */
    public function update($id){
        $req_u = request()->all();
        //验证
        $verify = $this->verify($req_u,$id);
        if($verify) return $verify;
        $rgba = turnInt($req_u['r'],$req_u['g'],$req_u['b'],$req_u['a']);
        try{
            DB::beginTransaction();
            $lineMaterial = PosterHoleLineMaterialDefine::where(IekModel::ID,$id)
                ->update([
                    IekModel::NAME => $req_u['name'],
                    IekModel::DESC => $req_u['des'],
                    IekModel::MATERIAL_ID => $req_u['materialId'],
                    IekModel::CATEGORY_ID => $req_u['categoryId'],
                    IekModel::FACADE_ID => $req_u['facadeId'],
                    IekModel::FACADE_ID => $req_u['facadeId'],
                    IekModel::PHY_DEPTH => $req_u['phyDepth'],
                    IekModel::PHY_HEIGHT => $req_u['phyHeight'],
                    IekModel::CONTENT_DEPTH => $req_u['phyContentDepth'],
                    IekModel::PRESS_HEIGHT => $req_u['phyPressHeight'],
                    IekModel::PRESS_DEPTH => $req_u['phyPressDepth'],
                    IekModel::PRESS_OFFSET => $req_u['phyPressOffset'],
                    IekModel::SLOT_HEIGHT => $req_u['phySlotHeight'],
                    IekModel::SLOT_DEPTH => $req_u['phySlotDepth'],
                    IekModel::SLOT_OFFSET => $req_u['phySlotOffset'],
                    IekModel::COLOR_NAME => $req_u['colorName'],
                    IekModel::RGBA => $rgba,
                    IekModel::PRICE => $req_u['price'],
                    IekModel::PRICE_UNIT => $req_u['priceUnit'],
                    IekModel::CURRENCY => $req_u['currency'],
                ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"修改成功","修改失败",$lineMaterial);
    }
    /**
     * delete holeLineMaterialDefine's record
     */
    public function del(){
        $model = new PosterHoleLineMaterialDefine();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover holeLineMaterialDefine's record
     */
    public function cover(){
        $model = new PosterHoleLineMaterialDefine();
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
        $contentDepth = $req['phyContentDepth'];
        $pressHeight = $req['phyPressHeight'];
        $pressDepth = $req['phyPressDepth'];
        $pressOffset = $req['phyPressOffset'];
        $slotHeight = $req['phySlotHeight'];
        $slotDepth = $req['phySlotDepth'];
        $slotOffset = $req['phySlotOffset'];
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
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入物理宽度",$phyDepth);
        }
        if(is_null($phyHeight)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入物理高度",$phyHeight);
        }
        if(is_null($contentDepth)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入内容深度",$contentDepth);
        }
        if(is_null($pressHeight)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入压画高度",$pressHeight);
        }
        if(is_null($pressDepth)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入压画宽度",$pressDepth);
        }
        if(is_null($pressOffset)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入压画偏移量",$pressOffset);
        }
        if(is_null($slotHeight)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入槽高",$slotHeight);
        }
        if(is_null($slotDepth)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入槽深",$slotDepth);
        }
        if(is_null($slotOffset)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入槽偏移量",$slotOffset);
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
        $has = PosterHoleLineMaterialDefine::where(IekModel::NAME,$name)
            ->where(IekModel::MATERIAL_ID,$mid)
            ->where(IekModel::CATEGORY_ID,$cid)
            ->first();
        if(!is_null($has)){
            if(is_null($id)){
                return $this->viewReturn(Errors::EXIST,"此记录已添加",$name);
            }else{
                if($has->id != $id){
                    return $this->viewReturn(Errors::EXIST,"此记录已添加",$name);
                }
            }
        }
    }
}
?>