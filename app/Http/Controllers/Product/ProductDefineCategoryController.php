<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/10
 * Time: 14:39
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Product\ProductCategory;
use App\IekModel\Version1_0\Product\ProductDefine;
use App\IekModel\Version1_0\Product\ProductDefineCategory;
use Illuminate\Support\Facades\DB;

class ProductDefineCategoryController extends Controller
{
/**
     * ProductDefineCategory's add page
     */
    public function add(){
        $pds = ProductDefine::where(IekModel::CONDITION)->get();
        $pcs = ProductCategory::where(IekModel::CONDITION)->get();
        return view('product.define.productDefineCategory',['pds'=>$pds,'pcs'=>$pcs]);
    }
    /**
     * ProductDefineCategory's add data deal
     */
    public function create(){
        $req = request()->all();
        //验证
        $verify = $this->verify($req,null);
        if($verify) return $verify;
        try{
            DB::beginTransaction();
            $pdc = new ProductDefineCategory();
            $pdc->product_define_id = $req['productDefineId'];
            $pdc->category_id = $req['categoryId'];
            $pdc->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"保存成功","保存失败",$pdc);
    }
    /**
     * ProductDefineCategory's all data list
     */
    public function listProductDefineCategory(){
        $model = new ProductDefineCategory();
        $type = 'product-define-category';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * ProductDefineCategory's data detail
     */
    public function showProductDefineCategory(){
        //
    }
    /**
     * ProductDefineCategory's edit page
     */
    public function edit($id){
        $pds = ProductDefine::where(IekModel::CONDITION)->get();
        $pcs = ProductCategory::where(IekModel::CONDITION)->get();
        $pdc = ProductDefineCategory::find($id);
        $action = 'edit';
        return view('product.define.productDefineCategory',compact('pds','pcs','pdc','action'));
    }
    /**
     * ProductDefineCategory's edit data deal
     */
    public function update($id){
        $req_u = request()->all();
        //验证
        $verify = $this->verify($req_u,$id);
        if($verify) return $verify;
        try{
            DB::beginTransaction();
            $pdc = ProductDefineCategory::where(IekModel::ID,$id)
                ->update([
                    IekModel::PRODUCT_DID => $req_u['productDefineId'],
                    IekModel::CATEGORY_ID => $req_u['categoryId']
                ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"修改成功","修改失败",$pdc);
    }
    /**
     * delete ProductDefineCategory's record
     */
    public function del(){
        $model = new ProductDefineCategory();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover ProductDefineCategory's record
     */
    public function cover(){
        $model = new ProductDefineCategory();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * validate
     */
    public function verify($req,$id){
        $pdid = $req['productDefineId'];
        $cid = $req['categoryId'];
        if(is_null($pdid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择定义产品",$pdid);
        }
        if(is_null($cid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择产品分类",$cid);
        }
        $re = ProductDefineCategory::where(IekModel::PRODUCT_DID,$pdid)
            ->where(IekModel::CATEGORY_ID,$cid)
            ->first();
        if(is_null($id)){
            if(!is_null($re)){
                return $this->viewReturn(Errors::EXIST,"产品定义分类已存在",$pdid);
            }
        }else{
            if(!is_null($re)&&$re->id != $id){
                return $this->viewReturn(Errors::EXIST,"产品定义分类已存在",$pdid);
            }
        }
    }
}
?>