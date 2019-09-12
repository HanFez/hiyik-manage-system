<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/19
 * Time: 9:49
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Product\PosterFrameHoleDefine;
use App\IekModel\Version1_0\Product\Shape;
use Illuminate\Support\Facades\DB;

class FrameHoleDefineController extends Controller
{
/**
     * frameHoleDefine's add page
     */
    public function add(){
        $shapes = Shape::where(IekModel::CONDITION)->get();
        return view('product.define.frameHoleDefine',['shapes'=>$shapes]);
    }
    /**
     * frameHoleDefine's add data deal
     */
    public function create(){
        $req = request()->all();
        //验证
        $verify = $this->verify($req,null);
        if($verify) return $verify;
        $bevel = explode(',',$req['bevel']);
        foreach($bevel as $k=>$v){
            $val[$k] = intval($bevel[$k]);
        }
        $bevel = $val;
        try{
            DB::beginTransaction();
            $frameHole = new PosterFrameHoleDefine();
            $frameHole->shape_id = $req['shapeId'];
            $frameHole->bevel = json_encode($bevel);
            $frameHole->phy_width_min = $req['phyWidthMin'];
            $frameHole->phy_height_min = $req['phyHeightMin'];
            $frameHole->phy_margin_min = $req['phyMarginMin'];
            $frameHole->is_deformable = $req['isDeformable'];
            $frameHole->price = $req['price'];
            $frameHole->price_unit = $req['priceUnit'];
            $frameHole->currency = $req['currency'];
            $frameHole->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"保存成功","保存失败",$frameHole);
    }
    /**
     * frameHoleDefine's all data list
     */
    public function listFHD(){
        $model = new PosterFrameHoleDefine();
        $type = 'frame-hole-define';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * frameHoleDefine's data detail
     */
    public function showFHD(){
        //
    }
    /**
     * frameHoleDefine's edit page
     */
    public function edit($id){
        $shapes = Shape::where(IekModel::CONDITION)->get();
        $action = 'edit';
        $frameHole = PosterFrameHoleDefine::find($id);
        return view('product.define.frameHoleDefine',compact('shapes','action','frameHole'));
    }
    /**
     * frameHoleDefine's edit data deal
     */
    public function update($id){
        $req_u = request()->all();
        //验证
        $verify = $this->verify($req_u,$id);
        if($verify) return $verify;
        $bevel = explode(',',$req_u['bevel']);
        foreach($bevel as $k=>$v){
            $val[$k] = intval($bevel[$k]);
        }
        $bevel = $val;
        try{
            DB::beginTransaction();
            $frameHole = PosterFrameHoleDefine::where(IekModel::ID,$id)
                ->update([
                    IekModel::SHAPE_ID => $req_u['shapeId'],
                    IekModel::BEVEL => json_encode($bevel),
                    IekModel::WIDTH_MIN => $req_u['phyWidthMin'],
                    IekModel::HEIGHT_MIN => $req_u['phyHeightMin'],
                    IekModel::MARGIN_MIN => $req_u['phyMarginMin'],
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
        return $this->curd(Errors::OK,Errors::FAILED,"修改成功","修改失败",$frameHole);
    }
    /**
     * delete frameHoleDefine's record
     */
    public function del(){
        $model = new PosterFrameHoleDefine();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover frameHoleDefine's record
     */
    public function cover(){
        $model = new PosterFrameHoleDefine();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * validate
     */
    public function verify($req,$id){
        $shapeId = $req['shapeId'];
        $bevel = $req['bevel'];
        $unit = $req['priceUnit'];
        if(is_null($shapeId)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择形状",$shapeId);
        }
        if(is_null($bevel)){
            return $this->viewReturn(Errors::NOT_EMPTY,"斜面度不能为空",$bevel);
        }
        if(is_null($unit)){
            return $this->viewReturn(Errors::NOT_EMPTY,"单位不能为空",$unit);
        }
        $has = PosterFrameHoleDefine::where(IekModel::SHAPE_ID,$shapeId)
            ->where(IekModel::DEFORMABLE,$req['isDeformable'])
            ->first();
        if(!is_null($has)){
            if(is_null($id)){
                return $this->viewReturn(Errors::EXIST,'此记录已添加',$shapeId);
            }else{
                if($has->id != $id){
                    return $this->viewReturn(Errors::EXIST,'此记录已添加',$shapeId);
                }
            }
        }
    }
}
?>