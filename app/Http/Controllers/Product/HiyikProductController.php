<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/7
 * Time: 15:57
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Product\HiyikProduct;
use App\IekModel\Version1_0\Product\Product;
use App\IekModel\Version1_0\Product\ProductDefine;
use Illuminate\Support\Facades\DB;

class HiyikProductController extends Controller
{
/**
     * PredefineProduct's add page
     */
    public function add(){
        $pds = ProductDefine::where(IekModel::CONDITION)->get();
        $products = Product::where(IekModel::CONDITION)->get();
        return view('product.define.predefine',['pds'=>$pds,'products'=>$products]);
    }
    /**
     * PredefineProduct's add data
     */
    public function create(){
        $req = request()->all();
        //验证
        $verify = $this->verify($req,null);
        if($verify) return $verify;
        try{
            DB::beginTransaction();
            $predefine = new HiyikProduct();
            $predefine->product_define_id = $req['productDefineId'];
            $predefine->product_id = $req['productId'];
            $predefine->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"保存成功","保存失败",$predefine);
    }
    /**
     * PredefineProduct's all data list
     */
    public function listPredefine(){
        $model = new HiyikProduct();
        $type = 'predefine';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * PredefineProduct's data detail
     */
    public function showPredefine(){
        //
    }
    /**
     * PredefineProduct's edit page
     */
    public function edit($id){
        $action = 'edit';
        $pds = ProductDefine::where(IekModel::CONDITION)->get();
        $products = Product::where(IekModel::CONDITION)->get();
        $predefine = HiyikProduct::find($id);
        return view('product.define.predefine',compact('action','pds','products','predefine'));
    }
    /**
     * PredefineProduct's edit data
     */
    public function update($id){
        $req_u = request()->all();
        //验证
        $verify = $this->verify($req_u,$id);
        if($verify) return $verify;
        try{
            DB::beginTransaction();
            $predefine = HiyikProduct::where(IekModel::ID,$id)
                ->update([
                    IekModel::PRODUCT_DID => $req_u['productDefineId'],
                    IekModel::PRODUCT_ID => $req_u['productId']
                ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"修改成功","修改失败",$predefine);
    }
    /**
     * delete PredefineProduct's record
     */
    public function del(){
        $model = new HiyikProduct();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover PredefineProduct's record
     */
    public function cover(){
        $model = new HiyikProduct();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * validate
     */
    public function verify($req,$id){
        $pdid = $req['productDefineId'];
        $pid = $req['productId'];
        if(is_null($pdid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择产品定义",$pdid);
        }
        if(is_null($pid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择产品",$pid);
        }

        $re = HiyikProduct::where(IekModel::PRODUCT_DID,$pdid)
            ->where(IekModel::PRODUCT_ID,$pid)
            ->first();
        if(is_null($id)){
            if(!is_null($re)){
                return $this->viewReturn(Errors::EXIST,"该预定义产品已存在",$pdid);
            }
        }else{
            if(!is_null($re) && $re->id != $id){
                return $this->viewReturn(Errors::EXIST,"该预定义产品已存在",$pdid);
            }
        }
    }
}
?>