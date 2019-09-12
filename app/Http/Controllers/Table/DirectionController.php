<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/3/6
 * Time: 16:29
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Direction;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use Illuminate\Support\Facades\DB;

class DirectionController extends Controller
{
    /**
     * direction add view
     */
    public function directionAdd(){
        return view('admin.systemSetting.product.directionAdd');
    }
    /**
     * add post
     */
    public function createDirection(){
        $err = new Error();
        $name = request()->input('direction');
        $description = request()->input('description');
        if(is_null($name) || empty($name)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入名称','direction');
        }
        if(direction::checkExist($name)){
            return $this->viewReturn(Errors::EXIST,'该形状已存在','direction');
        }
        DB::beginTransaction();
        try{
            $direction = new Direction();
            $direction->name = $name;
            $direction->description = $description;
            $re = $direction->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'添加成功','添加失败',$re,$direction);
    }
    /**
     * list
     */
    public function directionList(){
        $model = new Direction();
        $type = 'direction';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * edit view
     */
    public function directionEdit($id){
        $direction = Direction::find($id);
        return view('admin.systemSetting.product.directionEdit',compact('direction'));
    }
    /**
     * edit put
     */
    public function modifyDirection($id){
        $err = new Error();
        $name = request()->input('direction');
        $description = request()->input('description');
        if(is_null($name) || empty($name)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入名称','direction');
        }
        DB::beginTransaction();
        try{
            $direction = new Direction();
            $re = $direction->where(IekModel::ID,$id)
                ->update([
                    IekModel::NAME => $name,
                    IekModel::DESC => $description
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
     * delete
     */
    public function deleteDirection(){
        $model = new Direction();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover
     */
    public function recoverDirection(){
        $model = new Direction();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
}
?>