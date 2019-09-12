<?php

namespace App\Http\Controllers\Table;

use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Crowd;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class CrowdController extends Controller
{
    /**
     * 添加人群页
     */
    public function crowdAdd(){
        return view('admin.systemSetting.product.crowdAdd');
    }
    /**
     * 添加人群
     */
    public function createCrowd(){
        $err = new Error();
        $name        = request()->input('crowdName');
        $description = request()->input('crowdDescription');
        if(is_null($name) || empty($name)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入人群名称','crowdName');
        }
        if(Crowd::checkExist($name)){
            return $this->viewReturn(Errors::EXIST,'该人群已存在','crowdName');
        }
        DB::beginTransaction();
        try{
            $crowd              = new Crowd();
            $crowd->name        = $name;
            $crowd->description = $description;
            $re                 = $crowd->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'添加成功','添加失败',$re,$crowd);
    }
    /**
     * 查看人群
     */
    public function crowdList(){
        $model = new Crowd();
        $type = 'crowd';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * 修改人群页
     */
    public function crowdEdit($id){
        $crowd = Crowd::find($id);
        return view('admin.systemSetting.product.crowdEdit',compact('crowd'));
    }
    /**
     * 修改人群
     */
    public function modifyCrowd($id){
        $err = new Error();
        $name        = request()->input('crowdName');
        $description = request()->input('crowdDescription');
        if(is_null($name) || empty($name)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入人群名称','crowdName');
        }
        DB::beginTransaction();
        try{
            $crowd = new Crowd();
            $re    = $crowd->where(IekModel::ID,$id)
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
     * 删除人群
     */
    public function deleteCrowd(){
        $model = new Crowd();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * 恢复人群
     */
    public function recoverCrowd(){
        $model = new Crowd();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }

}
