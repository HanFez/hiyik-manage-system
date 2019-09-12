<?php

namespace App\Http\Controllers\Table;

use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Mount;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class MountController extends Controller
{
    /**
     * 添加装裱页
     */
    public function mountAdd(){
        return view('admin.systemSetting.product.mountAdd');
    }
    /**
     * 添加装裱
     */
    public function createMount(){
        $err = new Error();
        $name        = request()->input('mountName');
        $description = request()->input('mountDescription');
        if(is_null($name) || empty($name)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入装裱名称','mountName');
        }
        if(Mount::checkExist($name)){
            return $this->viewReturn(Errors::EXIST,'该装裱已存在','mountName');
        }
        DB::beginTransaction();
        try{
            $mount              = new Mount();
            $mount->name        = $name;
            $mount->description = $description;
            $re                 = $mount->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'添加成功','添加失败',$re,$mount);
    }
    /**
     * 查看装裱
     */
    public function mountList(){
        $model = new Mount();
        $type = 'mount';
        $getList = new IndexController();
        $result = $getList->tableList($model , $type);
        return $result;
    }
    /**
     * 修改装裱页
     */
    public function mountEdit($id){
        $mount = Mount::find($id);
        return view('admin.systemSetting.product.mountEdit',compact('mount'));
    }
    /**
     * 修改装裱
     */
    public function modifyMount($id){
        $err = new Error();
        $name        = request()->input('mountName');
        $description = request()->input('mountDescription');
        if(is_null($name) || empty($name)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入装裱名称','mountName');
        }
        DB::beginTransaction();
        try{
            $mount = new Mount();
            $re = $mount->where(IekModel::ID,$id)
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
     * 删除装裱
     */
    public function deleteMount(){
        $model = new Mount();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * 恢复装裱
     */
    public function recoverMount(){
        $model = new Mount();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }

}
