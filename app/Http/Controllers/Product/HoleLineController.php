<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/18
 * Time: 16:46
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Product\Material;
use App\IekModel\Version1_0\Product\PosterHoleLineDefine;
use App\IekModel\Version1_0\Product\Shape;
use Illuminate\Support\Facades\DB;

class HoleLineController extends Controller
{
/**
     * HoleLine's add page
     */
    public function add(){
        $materials = Material::where(IekModel::CONDITION)->get();
        $shapes = Shape::where(IekModel::CONDITION)->get();
        return view('product.define.holeLineDefine',compact('materials','shapes'));
    }
    /**
     * HoleLine's add data deal
     */
    public function create(){
        $req = request()->all();
        //验证
        $verify = $this->verify($req,null);
        if($verify) return $verify;
        try{
            DB::beginTransaction();
            $holeLime = new PosterHoleLineDefine();
            $holeLime->material_id = $req['materialId'];
            $holeLime->shape_id = $req['shapeId'];
            $holeLime->is_deformable = $req['isDeformable'];
            $holeLime->price = $req['price'];
            $holeLime->price_unit = $req['priceUnit'];
            $holeLime->currency = $req['currency'];
            $holeLime->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"保存成功","保存失败",$holeLime);
    }
    /**
     * HoleLine's all data list
     */
    public function listHL(){
        $model = new PosterHoleLineDefine();
        $type = 'hole-line-define';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * HoleLine's data detail
     */
    public function showHL(){
        //
    }
    /**
     * HoleLine's edit page
     */
    public function edit($id){
        $materials = Material::where(IekModel::CONDITION)->get();
        $shapes = Shape::where(IekModel::CONDITION)->get();
        $action = 'edit';
        $holeLine = PosterHoleLineDefine::find($id);
        return view('product.define.holeLineDefine',compact('materials','holeLine','shapes','action'));
    }
    /**
     * HoleLine's edit data deal
     */
    public function update($id){
        $req_u = request()->all();
        //验证
        $verify = $this->verify($req_u,$id);
        if($verify) return $verify;
        try{
            DB::beginTransaction();
            $holeLime = PosterHoleLineDefine::where(IekModel::ID,$id)
                ->update([
                    IekModel::MATERIAL_ID => $req_u['materialId'],
                    IekModel::SHAPE_ID => $req_u['shapeId'],
                    IekModel::DEFORMABLE => $req_u['isDeformable'],
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
        return $this->curd(Errors::OK,Errors::FAILED,"修改成功","修改失败",$holeLime);
    }
    /**
     * delete HoleLine's record
     */
    public function del(){
        $model = new PosterHoleLineDefine();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover HoleLine's record
     */
    public function cover(){
        $model = new PosterHoleLineDefine();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * validate
     */
    public function verify($req,$id){
        $mid = $req['materialId'];
        $sid = $req['shapeId'];
        $unit = $req['priceUnit'];
        if(is_null($mid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择材料",$mid);
        }
        if(is_null($sid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择形状",$sid);
        }
        if(is_null($unit)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入价格单位",$unit);
        }
        $has = PosterHoleLineDefine::where(IekModel::MATERIAL_ID,$mid)
            ->where(IekModel::SHAPE_ID,$sid)
            ->where(IekModel::DEFORMABLE,$req['isDeformable'])
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