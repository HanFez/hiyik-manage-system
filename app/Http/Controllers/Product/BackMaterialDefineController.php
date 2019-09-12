<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/18
 * Time: 15:22
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
use App\IekModel\Version1_0\Product\PosterBackMaterialDefine;
use Illuminate\Support\Facades\DB;

class BackMaterialDefineController extends Controller
{
/**
     * BackMaterialDefine's add page
     */
    public function add(){
        $materials = Material::where(IekModel::CONDITION)->get();
        $mcs = MCategory::where(IekModel::CONDITION)->get();
        $facades = Facade::where(IekModel::CONDITION)->get();
        return view('product.define.backMaterialDefine',compact('materials','mcs','facades'));
    }
    /**
     * BackMaterialDefine's add data deal
     */
    public function create(){
        $err = new Error();
        $req = request()->all();
        //验证
        $verify = $this->verify($req,null);
        if($verify) return $verify;
        $model = new Material();
        $limit = self::limitOther($model,$req['materialId']);
        if(is_null($limit)){
            $err->setError(Errors::NOT_FOUND);
            $err->setMessage('材料关系不完整，请先补充材料纹理关联数据再来添加。');
            return view('message.formResult',['result'=>$err]);
        }
        $rgba = turnInt($req['r'],$req['g'],$req['b'],$req['a']);
        try{
            DB::beginTransaction();
            $fmd = new PosterBackMaterialDefine();
            $fmd->name = $req['name'];
            $fmd->description = $req['des'];
            $fmd->material_id = $req['materialId'];
            $fmd->category_id = $req['categoryId'];
            $fmd->facade_id = $req['facadeId'];
            $fmd->phy_depth = $req['phyDepth'];
            $fmd->phy_width_max = $req['phyWidthMax'];
            $fmd->phy_width_min = $req['phyWidthMin'];
            $fmd->phy_height_max = $req['phyHeightMax'];
            $fmd->phy_height_min = $req['phyHeightMin'];
            $fmd->color_name = $req['colorName'];
            $fmd->rgba = $rgba;
            $fmd->price = $req['price'];
            $fmd->price_unit = $req['priceUnit'];
            $fmd->currency = $req['currency'];
            $fmd->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"保存成功","保存失败",$fmd);
    }
    /**
     * BackMaterialDefine's all data list
     */
    public function listBKD(){
        $model = new PosterBackMaterialDefine();
        $type = 'back-material-define';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * BackMaterialDefine's data detail
     */
    public function showBKD(){
        //
    }
    /**
     * BackMaterialDefine's edit page
     */
    public function edit($id){
        $materials = Material::where(IekModel::CONDITION)->get();
        $mcs = MCategory::where(IekModel::CONDITION)->get();
        $facades = Facade::where(IekModel::CONDITION)->get();
        $action = 'edit';
        $bkd = PosterBackMaterialDefine::find($id);
        $rgba = turnRgba($bkd->rgba);
        return view('product.define.backMaterialDefine',compact('materials','mcs','facades','action','bkd','rgba'));
    }
    /**
     * BackMaterialDefine's edit data deal
     */
    public function update($id){
        $err = new Error();
        $req_u = request()->all();
        //验证
        $verify = $this->verify($req_u,$id);
        if($verify) return $verify;
        $model = new Material();
        $limit = self::limitOther($model,$req_u['materialId']);
        if(is_null($limit)){
            $err->setError(Errors::NOT_FOUND);
            $err->setMessage('材料关系不完整，请先补充材料纹理关联数据再来添加。');
            return view('message.formResult',['result'=>$err]);
        }
        $rgba = turnInt($req_u['r'],$req_u['g'],$req_u['b'],$req_u['a']);
        try{
            DB::beginTransaction();
            $bkd = PosterBackMaterialDefine::where(IekModel::ID,$id)
                ->update([
                    IekModel::NAME => $req_u['name'],
                    IekModel::DESC => $req_u['des'],
                    IekModel::MATERIAL_ID => $req_u['materialId'],
                    IekModel::CATEGORY_ID => $req_u['categoryId'],
                    IekModel::FACADE_ID => $req_u['facadeId'],
                    IekModel::PHY_DEPTH => $req_u['phyDepth'],
                    IekModel::WIDTH_MAX => $req_u['phyWidthMax'],
                    IekModel::WIDTH_MIN => $req_u['phyWidthMin'],
                    IekModel::HEIGHT_MAX => $req_u['phyHeightMax'],
                    IekModel::HEIGHT_MIN => $req_u['phyHeightMin'],
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
        return $this->curd(Errors::OK,Errors::FAILED,"修改成功","修改失败",$bkd);
    }
    /**
     * delete BackMaterialDefine's record
     */
    public function del(){
        $model = new PosterBackMaterialDefine();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover BackMaterialDefine's record
     */
    public function cover(){
        $model = new PosterBackMaterialDefine();
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
        $mid= $req['materialId'];
        $cid = $req['categoryId'];
        $fid = $req['facadeId'];
        $color = $req['colorName'];
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
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择外观",$fid);
        }
        if(is_null($color)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入颜色名称",$color);
        }
        if(is_null($r) && is_null($g) && is_null($b) && is_null($a)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入RGBA值",$r);
        }
        if(is_null($unit)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入价格单位",$unit);
        }
        $has = PosterBackMaterialDefine::where(IekModel::NAME,$name)
            ->where(IekModel::MATERIAL_ID,$mid)
            ->where(IekModel::CATEGORY_ID,$cid)
            ->first();
        if(!is_null($has)){
            if(is_null($id)){
                return $this->viewReturn(Errors::EXIST,'此记录已存在',$name);
            }else{
                if($has->id != $id){
                    return $this->viewReturn(Errors::EXIST,'此记录已存在',$name);
                }
            }
        }
    }
}
?>