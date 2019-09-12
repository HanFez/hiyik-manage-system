<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/11/26
 * Time: 11:37
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Product\MCategory;
use Illuminate\Support\Facades\DB;

class MCategoryController extends Controller
{
    /**
     * MCategory's add page
     */
    public function addMCategory(){
        $mcs = MCategory::where(IekModel::CONDITION)->get();
        return view('product.category.mCategory',['mcs'=>$mcs]);
    }
    /**
     * MCategory's add data deal
     */
    public function createMCategory(){
        $req = request()->all();
        if($req['pid'] == 'null') $req['pid'] = null;
        //验证
        $verify = $this->verify($req,null);
        if($verify){
            return $verify;
        }
        try{
            DB::beginTransaction();
            $MC = new MCategory();
            $MC->name = $req['name'];
            $MC->description = $req['des'];
            $MC->parent_id = $req['pid'];
            $MC->level = $req['level'];
            $MC->save();
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'添加成功','添加失败',$MC);
    }
    /**
     * MCategory's all data list
     */
    public function listMCategory(){
        $model = new MCategory();
        $type = 'material-category';
        $getList = new IndexController();
        $result = $getList->tableList($model ,$type);
        return $result;
    }
    /**
     * MCategory's data detail
     */
    public function showMCategory(){
        //
    }
    /**
     * MCategory's edit page
     */
    public function editMCategory($id){
        $action = 'edit';
        $mc = MCategory::find($id);
        $mcs = MCategory::where(IekModel::CONDITION)->get();
        return view('product.category.mCategory',compact('action','mcs','mc'));
    }
    /**
     * MCategory's edit data deal
     */
    public function updateMCategory($id){
        $req_u = request()->all();
        if($req_u['pid'] == 'null') $req_u['pid'] = null;
        //验证
        $verify = $this->verify($req_u,$id);
        if($verify){
            return $verify;
        }
        try{
            DB::beginTransaction();
            $MC = MCategory::where(IekModel::ID,$id)
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
        return $this->curd(Errors::OK,Errors::FAILED,'修改成功','修改失败',$MC);
    }
    /**
     * delete MCategory's record
     */
    public function delMCategory(){
        $model = new MCategory();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover MCategory's record
     */
    public function coverMCategory(){
        $model = new MCategory();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * validate MCategory's add
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
        $has_name = MCategory::where(IekModel::NAME,$name)
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