<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/11/22
 * Time: 14:43
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Product\Handle;
use Illuminate\Support\Facades\DB;

class HandleController extends Controller
{
    /**
     * handle's add page
     */
    public function addHandle(){
        return view('product.produce.handle');
    }
    /**
     * handle's add data deal
     */
    public function createHandle(){
        $req = request()->all();
        //验证
        $verify = $this->verify($req,null);
        if($verify){
            return $verify;
        }
        try{
            DB::beginTransaction();
            $handle = new Handle();
            $handle->name = $req['name'];
            $handle->description = $req['des'];
            $handle->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'添加成功','添加失败',$handle);
    }
    /**
     * handle's all data list
     */
    public function listHandle(){
        $model = new Handle();
        $type = 'handle';
        $getList = new IndexController();
        $result = $getList->tableList($model ,$type);
        return $result;
    }
    /**
     * handle's data detail
     */
    public function showHandle(){
        //
    }
    /**
     * handle's edit page
     */
    public function editHandle($id){
        $action = 'edit';
        $handle = Handle::find($id);
        return view('product.produce.handle',['action'=>$action,'handle'=>$handle]);
    }
    /**
     * handle's edit data deal
     */
    public function updateHandle($id){
        $req_u = request()->all();
        //验证
        $verify = $this->verify($req_u,$id);
        if($verify){
            return $verify;
        }
        try{
            DB::beginTransaction();
            $handle = Handle::where(IekModel::ID,$id)
                ->where(IekModel::CONDITION)
                ->update([
                    IekModel::NAME => $req_u['name'],
                    IekModel::DESC => $req_u['des']
                ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'修改成功','修改失败',$handle);
    }
    /**
     * delete handle's record
     */
    public function delHandle(){
        $model = new Handle();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover handle's record
     */
    public function coverHandle(){
        $model = new Handle();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * validate
     */
    public function verify($req,$id){
        $name = $req['name'];
        $des = $req['des'];
        if(is_null($name)){
            return $this->viewReturn(Errors::NOT_EMPTY,"工艺名称不能为空",$name);
        }
        if(is_null($des)){
            return $this->viewReturn(Errors::NOT_EMPTY,"工艺描述不能为空",$des);
        }
        $has_name = Handle::where(IekModel::NAME,$name)->first();
        if(!is_null($has_name)){
            if(is_null($id)){
                return $this->viewReturn(Errors::EXIST,"此工艺名称已存在",$name);
            }else{
                if($has_name->id !== $id){
                    return $this->viewReturn(Errors::EXIST,"此工艺名称已存在",$name);
                }
            }
        }
    }
}
?>