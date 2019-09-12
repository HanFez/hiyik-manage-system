<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/6
 * Time: 15:50
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Product\ProductDefine;
use Illuminate\Support\Facades\DB;

class ProductDefineController extends Controller
{
/**
     * ProductDefine's add page
     */
    public function add(){
        return view('product.define.productDefine');
    }
    /**
     * ProductDefine's add data deal
     */
    public function create(){
        $req = request()->all();
        //验证
        $verify = $this->verify($req,null);
        if($verify) return $verify;
        try{
            DB::beginTransaction();
            $pd = new ProductDefine();
            $pd->name_abbr = $req['name_abbr'];
            $pd->name = $req['name'];
            $pd->description = $req['des'];
            $pd->icon = $req['icon'];
            $pd->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"保存成功","保存失败",$pd);
    }
    /**
     * ProductDefine's all data list
     */
    public function listProductDefine(){
        $model = new ProductDefine();
        $type = 'product-define';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * ProductDefine's data detail
     */
    public function showProductDefine(){
        //
    }
    /**
     * ProductDefine's edit page
     */
    public function edit($id){
        $action = 'edit';
        $pd = ProductDefine::find($id);
        return view('product.define.productDefine',['action'=>$action,'pd'=>$pd]);
    }
    /**
     * ProductDefine's edit data deal
     */
    public function update($id){
        $req_u = request()->all();
        //验证
        $verify = $this->verify($req_u,$id);
        if($verify) return $verify;
        try{
            DB::beginTransaction();
            $pd = ProductDefine::where(IekModel::ID,$id)
                ->update([
                    IekModel::NAME_ABBR => $req_u['name_abbr'],
                    IekModel::NAME => $req_u['name'],
                    IekModel::DESC => $req_u['des'],
                    IekModel::ICON => $req_u['icon'],
                ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"修改成功","修改失败",$pd);
    }
    /**
     * delete ProductDefine's record
     */
    public function del(){
        $model = new ProductDefine();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover ProductDefine's record
     */
    public function cover(){
        $model = new ProductDefine();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * validate
     */
    public function verify($req ,$id){
        $abbr = $req['name_abbr'];
        $name = $req['name'];
        $des = $req['des'];
        $icon = $req['icon'];
        if(is_null($abbr)){
            return $this->viewReturn(Errors::NOT_EMPTY,"名称缩写不能为空",$abbr);
        }
        if(mb_strlen($abbr)>4){
            return $this->viewReturn(Errors::INVALID_PARAMS,"名称缩写不能超过4位字符",$abbr);
        }
        if(is_null($name)){
            return $this->viewReturn(Errors::NOT_EMPTY,"名称不能为空",$name);
        }
        if(is_null($des)){
            return $this->viewReturn(Errors::NOT_EMPTY,"描述不能为空",$des);
        }
        if(is_null($icon)){
            return $this->viewReturn(Errors::NOT_EMPTY,"图标不能为空",$icon);
        }
        $pd = ProductDefine::where(IekModel::NAME_ABBR,$abbr)
            ->where(IekModel::NAME,$name)
            ->where(IekModel::ICON,$icon)
            ->first();
        if(is_null($id)){
            if(!is_null($pd)){
                return $this->viewReturn(Errors::EXIST, "该产品已定义", $abbr);
            }
        }else{
            if(!is_null($pd) && $pd->id != $id){
                return $this->viewReturn(Errors::EXIST, "该产品已定义", $abbr);
            }
        }
    }
}
?>