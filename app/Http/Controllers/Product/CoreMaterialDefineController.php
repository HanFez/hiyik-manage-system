<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/13
 * Time: 17:13
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
use App\IekModel\Version1_0\Product\PosterCoreMaterialDefine;
use Illuminate\Support\Facades\DB;

class CoreMaterialDefineController extends Controller
{
/**
     * CoreMaterialDefine's add page
     */
    public function add(){
        $materials = Material::where(IekModel::CONDITION)->get();
        $facades = Facade::where(IekModel::CONDITION)->get();
        $mcs = MCategory::where(IekModel::CONDITION)->get();
        return view('product.define.coreMaterialDefine',compact('materials','facades','mcs'));
    }
    /**
     * CoreMaterialDefine's add data deal
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
            $core = new PosterCoreMaterialDefine();
            $core->name = $req['name'];
            $core->description = $req['des'];
            $core->material_id = $req['materialId'];
            $core->category_id = $req['categoryId'];
            $core->facade_id = $req['facadeId'];
            $core->phy_width = $req['phyWidth'];
            $core->phy_height = $req['phyHeight'];
            $core->phy_depth = $req['phyDepth'];
            $core->color_name = $req['colorName'];
            $core->rgba = $rgba;
            $core->price = $req['price'];
            $core->price_unit = $req['priceUnit'];
            $core->currency = $req['currency'];
            $core->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"保存成功","保存失败",$core);
    }
    /**
     * CoreMaterialDefine's all data list
     */
    public function listCMD(){
        $model = new PosterCoreMaterialDefine();
        $type = 'core-material-define';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * CoreMaterialDefine's data detail
     */
    public function showCMD(){
        //
    }
    /**
     * CoreMaterialDefine's edit page
     */
    public function edit($id){
        $materials = Material::where(IekModel::CONDITION)->get();
        $facades = Facade::where(IekModel::CONDITION)->get();
        $mcs = MCategory::where(IekModel::CONDITION)->get();
        $action = 'edit';
        $cmd = PosterCoreMaterialDefine::find($id);
        $rgba = turnRgba($cmd->rgba);
        return view('product.define.coreMaterialDefine',compact('materials','facades','action','cmd','mcs','rgba'));
    }
    /**
     * CoreMaterialDefine's edit data deal
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
            $core = PosterCoreMaterialDefine::where(IekModel::ID,$id)
                ->update([
                    IekModel::NAME => $req_u['name'],
                    IekModel::DESC => $req_u['des'],
                    IekModel::MATERIAL_ID => $req_u['materialId'],
                    IekModel::FACADE_ID => $req_u['facadeId'],
                    IekModel::CATEGORY_ID => $req_u['categoryId'],
                    IekModel::PHY_WIDTH => $req_u['phyWidth'],
                    IekModel::PHY_HEIGHT => $req_u['phyHeight'],
                    IekModel::PHY_DEPTH => $req_u['phyDepth'],
                    IekModel::COLOR_NAME => $req_u['colorName'],
                    IekModel::RGBA => $rgba,
                    IekModel::PRICE => $req_u['price'],
                    IekModel::PRICE_UNIT => $req_u['priceUnit'],
                    IekModel::CURRENCY => $req_u['currency']
                ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"修改成功","修改失败",$core);
    }
    /**
     * delete CoreMaterialDefine's record
     */
    public function del(){
        $model = new PosterCoreMaterialDefine();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover CoreMaterialDefine's record
     */
    public function cover(){
        $model = new PosterCoreMaterialDefine();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * validate
     */
    public function verify($req,$id){
        $mid = $req['materialId'];
        $cid = $req['categoryId'];
        $fid = $req['facadeId'];
        $width = $req['phyWidth'];
        $height = $req['phyHeight'];
        $depth = $req['phyDepth'];
        $color = $req['colorName'];
        $r = $req['r'];
        $g = $req['g'];
        $b = $req['b'];
        $a = $req['a'];
        $unit = $req['priceUnit'];
        if(is_null($mid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择材料",$mid);
        }
        if(is_null($cid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择材料分类",$cid);
        }
        if(is_null($fid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择材料外观",$fid);
        }
        if(is_null($width)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入物理宽度",$width);
        }
        if(is_null($height)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入物理高度",$height);
        }
        if(is_null($depth)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入物理厚度",$depth);
        }
        if(is_null($color)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入颜色名称",$color);
        }
        if(is_null($r) || is_null($g) || is_null($b) || is_null($a)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入颜色rgba值",$r);
        }
        if(is_null($unit)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入价格单位",$unit);
        }
        $has = PosterCoreMaterialDefine::where(IekModel::MATERIAL_ID,$mid)
            ->where(IekModel::CATEGORY_ID,$cid)
            ->first();
        if(!is_null($has)){
            if(is_null($id)){
                return $this->viewReturn(Errors::EXIST,'此记录已添加',$mid);
            }else{
                if($has->id != $id){
                    return $this->viewReturn(Errors::EXIST,'此记录已添加',$mid);
                }
            }
        }
    }
}
?>