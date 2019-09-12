<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/9
 * Time: 15:49
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Sex;
use Illuminate\Support\Facades\DB;

class SexController extends Controller
{
    /**
     * 添加性别页
     */
    public function sexAdd(){
        return view('admin.systemSetting.product.sexAdd');
    }
    /**
     * 添加性别
     */
    public function createSex(){
        $err = new Error();
        $name        = request()->input('sexName');
        $description = request()->input('sexDescription');
        if(is_null($name) || empty($name)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入名称','sexName');
        }
        if(Sex::checkExist($name)){
            return $this->viewReturn(Errors::EXIST,'该性别已存在','sexName');
        }
        DB::beginTransaction();
        try{
            $sex              = new Sex();
            $sex->name        = $name;
            $sex->description = $description;
            $re               = $sex->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'添加成功','添加失败',$re);
    }
    /**
     * 查看性别
     */
    public function sexList(){
        $model = new Sex();
        $type = 'sex';
        $getList = new IndexController();
        $result = $getList->tableList($model , $type);
        return $result;
    }
    /**
     * 修改性别页
     */
    public function sexEdit($id){
        $sex = Sex::find($id);
        return view('admin.systemSetting.product.sexEdit',compact('sex'));
    }
    /**
     * 修改性别
     */
    public function modifySex($id){
        $err = new Error();
        $name        = request()->input('sexName');
        $description = request()->input('sexDescription');
        if(is_null($name) || empty($name)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入名称','sexName');
        }
        DB::beginTransaction();
        try{
            $sex = new Sex();
            $re  = $sex->where(IekModel::ID,$id)
                ->update([
                    IekModel::NAME => $name,
                    IekModel::DESC => $description,
                ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'修改成功','修改失败',$re);
    }
    /**
     * 删除性别
     */
    public function deleteSex(){
        $model = new Sex();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * 恢复性别
     */
    public function recoverSex(){
        $model = new Sex();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
}