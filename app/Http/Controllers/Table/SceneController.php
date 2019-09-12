<?php

namespace App\Http\Controllers\Table;

use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Scene;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class SceneController extends Controller
{
    /**
     * 添加场景页
     */
    public function sceneAdd(){
        return view('admin.systemSetting.product.sceneAdd');
    }
    /**
     * 添加场景
     */
    public function createScene(){
        $err = new Error();
        $name        = request()->input('sceneName');
        $class       = request()->input('sceneClass');
        $description = request()->input('sceneDescription');
        if(is_null($name) || empty($name)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入场景名称','sceneName');
        }
        if(is_null($class) || empty($class)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入场景类型','sceneClass');
        }
        DB::beginTransaction();
        try{
            $scene              = new Scene();
            $scene->name        = $name;
            $scene->class       = $class;
            $scene->description = $description;
            $re                 = $scene->save();
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
     * 查看场景
     */
    public function sceneList(){
        $model = new Scene();
        $type = 'scene';
        $getList = new IndexController();
        $result = $getList->tableList($model ,$type);
        return $result;
    }
    /**
     * 修改场景页
     */
    public function sceneEdit($id){
        $scene = Scene::find($id);
        return view('admin.systemSetting.product.sceneEdit',compact('scene'));
    }
    /**
     * 修改场景
     */
    public function modifyScene($id){
        $err = new Error();
        $name        = request()->input('sceneName');
        $class       = request()->input('sceneClass');
        $description = request()->input('sceneDescription');
        if(is_null($name) || empty($name)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入场景名称','sceneName');
        }
        if(is_null($class) || empty($class)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入场景类型','sceneClass');
        }
        DB::beginTransaction();
        try{
            $scene = new Scene();
            $re    = $scene->where(IekModel::ID,$id)
                ->update([
                    'name'        => $name,
                    'class'       => $class,
                    'description' => $description
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
     * 删除场景
     */
    public function deleteScene(){
        $model = new Scene();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * 恢复场景
     */
    public function recoverScene(){
        $model = new Scene();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }

}
