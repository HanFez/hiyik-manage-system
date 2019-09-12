<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/7/12
 * Time: 14:35
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Status;
use Illuminate\Support\Facades\DB;

class StatusController extends Controller
{
    /**
     * order status list
     */
    public function index(){
        $model = new Status();
        $type = 'status';
        $getList = new IndexController();
        $result = $getList->tableList($model ,$type);
        return $result;
    }
    /**
     * status add view
     */
    public function add(){
        return view('admin.status.add');
    }
    /**
     * save status
     */
    public function postStatus(){
        $err = new Error();
        $input = request()->except('_token');
        if(is_null($input['status'])){
            return $this->viewReturn(Errors::INVALID_PARAMS,'状态名不能为空',$input['status']);
        }
        $statusck = Status::addExist($input['status']);
        if($statusck){
            return $this->viewReturn(Errors::EXIST,'状态名字已存在','status');
        }
        try{
            DB::beginTransaction();
            $status = new Status();
            $status->name = $input['status'];
            $status->description = $input['description'];
            $status->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'添加成功','添加失败',$status);
    }
    /**
     * status edit view
     */
    public function edit($id){
        $status = Status::find($id);
        return view('admin.status.edit',compact('status'));
    }
    /**
     * modify status
     */
    public function putStatus($id){
        $err = new Error();
        $input = request()->except('_token');
        if(is_null($input['status'])){
            return $this->viewReturn(Errors::INVALID_PARAMS,'状态名不能为空','status');
        }
        $statusck = Status::updateExist($input['status'],$id);
        if(!$statusck){
            return $this->viewReturn(Errors::EXIST,'状态名字已存在','status');
        }
        try{
            DB::beginTransaction();
            $status = new Status();
            $status->where(IekModel::ID,$id)
                ->update([
                    IekModel::NAME => $input['status'],
                    IekModel::DESC => $input['description']
                ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'修改成功','修改失败',$status);
    }
    /**
     * delete status
     */
    public function del(){
        $model = new Status();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover status
     */
    public function recover(){
        $model = new Status();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
}