<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/5
 * Time: 15:25
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Product\Material;
use App\IekModel\Version1_0\Product\MaterialTexture;
use App\IekModel\Version1_0\Product\Texture;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MaterialTextureController extends Controller
{
/**
     * MaterialTexture's add page
     */
    public function add(){
        $materials = Material::where(IekModel::CONDITION)->get();
        $textures = Texture::where(IekModel::CONDITION)->get();
        return view('product.produce.materialTexture',compact('materials','textures'));
    }
    /**
     * MaterialTexture's add data deal
     */
    public function create(){
        $req = request()->all();
        //验证
        $verify = $this->verify($req,null);
        if($verify) return $verify;
        try{
            DB::beginTransaction();
            $mt = new MaterialTexture();
            $mt->material_id = $req['material'];
            $mt->texture_id = $req['texture'];
            $mt->position = $req['position'];
            $mt->perspective = $req['perspective'];
            $mt->partion = $req['partion'];
            $mt->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"保存成功","保存失败",$mt);
    }
    /**
     * MaterialTexture's all data list
     */
    public function listMaterialTexture(){
        $model = new MaterialTexture();
        $type = 'material-texture';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * MaterialTexture's data detail
     */
    public function showMaterialTexture(){
        //
    }
    /**
     * MaterialTexture's edit page
     */
    public function edit($id){
        $materials = Material::where(IekModel::CONDITION)->get();
        $textures = Texture::where(IekModel::CONDITION)->get();
        $action = 'edit';
        $mt = MaterialTexture::with('material')
            ->with('texture')
            ->find($id);
        return view('product.produce.materialTexture',compact('materials','textures','action','mt'));
    }
    /**
     * MaterialTexture's edit data deal
     */
    public function update($id){
        $req_u = request()->all();
        //验证
        $verify = $this->verify($req_u,$id);
        if($verify) return $verify;
        try{
            DB::beginTransaction();
            $mt = MaterialTexture::where(IekModel::ID,$id)
                ->update([
                    IekModel::MATERIAL_ID => $req_u['material'],
                    IekModel::TEXTURE_ID => $req_u['texture'],
                    IekModel::POSITION => $req_u['position'],
                    IekModel::PERSP => $req_u['perspective'],
                    IekModel::PARTION => $req_u['partion']
                ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"修改成功","修改失败",$mt);
    }
    /**
     * delete MaterialTexture's record
     */
    public function del(){
        $model = new MaterialTexture();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover MaterialTexture's record
     */
    public function cover(){
        $model = new MaterialTexture();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * validate
     */
    public function verify($req,$id){
        $mid = $req['material'];
        $tid = $req['texture'];
        $position = $req['position'];
        if(is_null($mid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择材料",$mid);
        }
        if(is_null($tid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择纹理",$tid);
        }
        if(is_null($position)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择摆放方位",$position);
        }
        $mt = MaterialTexture::where(IekModel::MATERIAL_ID,$mid)
            ->where(IekModel::TEXTURE_ID,$tid)
            ->where(IekModel::POSITION,$position)
            ->where(IekModel::PERSP,$req['perspective'])
            ->first();
        if(is_null($id)){
            if(!is_null($mt)){
                return $this->viewReturn(Errors::EXIST,"该材料纹理已存在",$mid);
            }
        }else{
            if(!is_null($mt)&& $mt->id != $id ){
                return $this->viewReturn(Errors::EXIST,"该纹理材料已存在",$mid);
            }
        }
    }
}
?>