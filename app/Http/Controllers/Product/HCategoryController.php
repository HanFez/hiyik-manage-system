<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/11/22
 * Time: 15:41
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Product\HCategory;
use Illuminate\Support\Facades\DB;

class HCategoryController extends Controller
{
    /**
     * HCategory's add page
     */
    public function addHCategory(){
        $hcs = HCategory::where(IekModel::CONDITION)->get();
        return view('product.category.hCategory',['hcs'=>$hcs]);
    }
    /**
     * HCategory's add data deal
     */
    public function createHCategory(){
        $req = request()->all();
        if($req['pid'] == 'null') $req['pid'] = null;
        //验证
        $verify = $this->verify($req,null);
        if($verify){
            return $verify;
        }
        try{
            DB::beginTransaction();
            $HC = new HCategory();
            $HC->name = $req['name'];
            $HC->description = $req['des'];
            $HC->parent_id = $req['pid'];
            $HC->level = $req['level'];
            $HC->save();
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'添加成功','添加失败',$HC);
    }
    /**
     * HCategory's all data list
     */
    public function listHCategory(){
        $model = new HCategory();
        $type = 'handle-category';
        $getList = new IndexController();
        $result = $getList->tableList($model ,$type);
        return $result;
    }
    /**
     * HCategory's data detail
     */
    public function showHCategory(){
        //
    }
    /**
     * HCategory's edit page
     */
    public function editHCategory($id){
        $action = 'edit';
        $hcs = HCategory::where(IekModel::CONDITION)->get();
        $hc = HCategory::find($id);
        return view('product.category.hCategory',compact('action','hcs','hc'));
    }
    /**
     * HCategory's edit data deal
     */
    public function updateHCategory($id){
        $req_u = request()->all();
        if($req_u['pid'] == 'null') $req_u['pid'] = null;
        //验证
        $verify = $this->verify($req_u,$id);
        if($verify){
            return $verify;
        }
        try{
            DB::beginTransaction();
            $HC = HCategory::where(IekModel::ID,$id)
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
        return $this->curd(Errors::OK,Errors::FAILED,'修改成功','修改失败',$HC);
    }
    /**
     * delete HCategory's record
     */
    public function delHCategory(){
        $model = new HCategory();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover HCategory's record
     */
    public function coverHCategory(){
        $model = new HCategory();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * validate HCategory's add
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
        $has_name = HCategory::where(IekModel::NAME,$name)
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