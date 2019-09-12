<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/11
 * Time: 10:22
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Product\Material;
use App\IekModel\Version1_0\Product\PosterBorderDefine;
use App\IekModel\Version1_0\Product\Shape;
use Illuminate\Support\Facades\DB;

class BorderDefineController extends Controller
{
/**
     * BorderDefine's add page
     */
    public function add(){
        $materials = Material::where(IekModel::CONDITION)->get();
        $shapes = Shape::where(IekModel::CONDITION)->get();
        return view('product.define.borderDefine',['materials'=>$materials,'shapes'=>$shapes]);
    }
    /**
     * BorderDefine's add data deal
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
        try{
            DB::beginTransaction();
            $border = new PosterBorderDefine();
            $border->material_id = $req['materialId'];
            $border->shape_id = $req['shapeId'];
            $border->phy_length_max = $req['phyLengthMax'];
            $border->phy_length_min = $req['phyLengthMin'];
            $border->price = $req['price'];
            $border->price_unit = $req['priceUnit'];
            $border->currency = $req['currency'];
            $border->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"保存成功","保存失败",$border);
    }
    /**
     * BorderDefine's all data list
     */
    public function listBorderDefine(){
        $model = new PosterBorderDefine();
        $type = 'border-define';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * BorderDefine's data detail
     */
    public function showBorderDefine(){
        //
    }
    /**
     * BorderDefine's edit page
     */
    public function edit($id){
        $materials = Material::where(IekModel::CONDITION)->get();
        $shapes = Shape::where(IekModel::CONDITION)->get();
        $action = 'edit';
        $border = PosterBorderDefine::find($id);
        return view('product.define.borderDefine',compact('materials','shapes','action','border'));
    }
    /**
     * BorderDefine's edit data deal
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
        try{
            DB::beginTransaction();
            $border = PosterBorderDefine::where(IekModel::ID,$id)
                ->update([
                    IekModel::MATERIAL_ID => $req_u['materialId'],
                    IekModel::SHAPE_ID => $req_u['shapeId'],
                    IekModel::LENGTH_MAX => $req_u['phyLengthMax'],
                    IekModel::LENGTH_MIN => $req_u['phyLengthMin'],
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
        return $this->curd(Errors::OK,Errors::FAILED,"修改成功","修改失败",$border);
    }
    /**
     * delete BorderDefine's record
     */
    public function del(){
        $model = new PosterBorderDefine();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover BorderDefine's record
     */
    public function cover(){
        $model = new PosterBorderDefine();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * validate
     */
    public function verify($req,$id){
        $materialId = $req['materialId'];
        $shapeId = $req['shapeId'];
        $unit = $req['priceUnit'];
        if(is_null($materialId)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择材料",$materialId);
        }
        if(is_null($shapeId)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择形状",$shapeId);
        }
        if(is_null($unit)){
            return $this->viewReturn(Errors::NOT_EMPTY,"单价不能为空",$unit);
        }
        $has = PosterBorderDefine::where(IekModel::MATERIAL_ID,$materialId)
            ->where(IekModel::SHAPE_ID,$shapeId)
            ->first();
        if(!is_null($has)){
            if(is_null($id)){
                return $this->viewReturn(Errors::EXIST,'此记录已添加',$materialId);
            }else{
                if($has->id != $id){
                    return $this->viewReturn(Errors::EXIST,'此记录已添加',$materialId);
                }
            }
        }
    }
}
?>