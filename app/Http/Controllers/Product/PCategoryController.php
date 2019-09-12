<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/11/26
 * Time: 14:59
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Product\ProductCategory;
use Illuminate\Support\Facades\DB;

class PCategoryController extends Controller
{
    /**
     * PCategory's add page
     */
    public function addPCategory(){
        $pcs = ProductCategory::where(IekModel::CONDITION)->get();
        return view('product.category.pCategory',['pcs'=>$pcs]);
    }
    /**
     * PCategory's add data deal
     */
    public function createPCategory(){
        $req = request()->all();
        if($req['pid'] == 'null') $req['pid'] = null;
        //验证
        $verify = $this->verify($req,null);
        if($verify){
            return $verify;
        }
        try{
            DB::beginTransaction();
            $PC = new ProductCategory();
            $PC->name = $req['name'];
            $PC->description = $req['des'];
            $PC->parent_id = $req['pid'];
            $PC->level = $req['level'];
            $PC->save();
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'添加成功','添加失败',$PC);
    }
    /**
     * PCategory's all data list
     */
    public function listPCategory(){
        $model = new ProductCategory();
        $type = 'product-category';
        $getList = new IndexController();
        $result = $getList->tableList($model ,$type);
        return $result;
    }
    /**
     * PCategory's data detail
     */
    public function showPCategory(){
        //
    }
    /**
     * PCategory's edit page
     */
    public function editPCategory($id){
        $action = 'edit';
        $pc = ProductCategory::find($id);
        $pcs = ProductCategory::where(IekModel::CONDITION)->get();
        return view('product.category.pCategory',compact('action','pcs','pc'));
    }
    /**
     * PCategory's edit data deal
     */
    public function updatePCategory($id){
        $req_u = request()->all();
        if($req_u['pid'] == 'null') $req_u['pid'] = null;
        //验证
        $verify = $this->verify($req_u,$id);
        if($verify){
            return $verify;
        }
        try{
            DB::beginTransaction();
            $PC = ProductCategory::where(IekModel::ID,$id)
                ->where(IekModel::CONDITION)
                ->update([
                    IekModel::NAME => $req_u['name'],
                    IekModel::DESC => $req_u['des'],
                    IekModel::LEVEL => $req_u['level'],
                    IekModel::PARENT_ID => $req_u['pid']
                ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'修改成功','修改失败',$PC);
    }
    /**
     * delete PCategory's record
     */
    public function delPCategory(){
        $model = new ProductCategory();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover PCategory's record
     */
    public function coverPCategory(){
        $model = new ProductCategory();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * validate PCategory's add
     */
    public function verify($req,$id){
        $name = $req['name'];
        $des = $req['des'];
        $level = $req['level'];
        if(is_null($name)){
            return $this->viewReturn(Errors::NOT_EMPTY,"分类名称不能为空",$name);
        }
        if(is_null($des)){
            return $this->viewReturn(Errors::NOT_EMPTY,"分类描述不能为空",$des);
        }
        if(is_null($level)){
            return $this->viewReturn(Errors::NOT_EMPTY,"分类等级不能为空",$level);
        }
        if(!is_integer($level)){
            return $this->viewReturn(Errors::INVALID_PARAMS,"请输入整型数字",$level);
        }

        $has_name = ProductCategory::where(IekModel::NAME,$name)
            //->where(IekModel::DESC,$des)
            ->where(IekModel::LEVEL,$level)
            ->where(IekModel::PARENT_ID,$req['pid'])
            ->first();
        if(!is_null($has_name)){
            if(is_null($id)){
                return $this->viewReturn(Errors::EXIST,"此分类已存在",$name);
            }else{
                if($has_name->id !== $id){
                    return $this->viewReturn(Errors::EXIST,"此分类已存在",$name);
                }
            }
        }
    }
}
?>