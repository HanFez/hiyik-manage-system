<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/14
 * Time: 15:40
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Product\PosterShowDefine;
use Illuminate\Support\Facades\DB;

class ShowDefineController extends Controller
{
/**
     * ShowDefine's add page
     */
    public function add(){
        return view('product.define.showDefine');
    }
    /**
     * ShowDefine's add data deal
     */
    public function create(){
        $req = request()->all();
        if($req['isDefault'] == "") $req['isDefault'] = false;
        //验证
        $verify = $this->verify($req,null);
        if($verify) return $verify;
        try{
            DB::beginTransaction();
            $core = new PosterShowDefine();
            $core->name = $req['name'];
            $core->description = $req['des'];
            $core->is_default = $req['isDefault'];
            $core->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"保存成功","保存失败",$core);
    }
    /**
     * ShowDefine's all data list
     */
    public function listShowDefine(){
        $model = new PosterShowDefine();
        $type = 'show-define';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * ShowDefine's data detail
     */
    public function showShowDefine(){
        //
    }
    /**
     * ShowDefine's edit page
     */
    public function edit($id){
        $action = 'edit';
        $show = PosterShowDefine::find($id);
        return view('product.define.showDefine',['action'=>$action,'show'=>$show]);
    }
    /**
     * ShowDefine's edit data deal
     */
    public function update($id){
        $req_u = request()->all();
        if($req_u['isDefault'] == "") $req_u['isDefault'] = false;
        //验证
        $verify = $this->verify($req_u,$id);
        if($verify) return $verify;
        try{
            DB::beginTransaction();
            $core = PosterShowDefine::where(IekModel::ID,$id)
                ->update([
                    IekModel::NAME => $req_u['name'],
                    IekModel::DESC => $req_u['des'],
                    IekModel::IS_DEFAULT => $req_u['isDefault']
                ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"修改成功","修改失败",$core);
    }
    /**
     * delete ShowDefine's record
     */
    public function del(){
        $model = new PosterShowDefine();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover ShowDefine's record
     */
    public function cover(){
        $model = new PosterShowDefine();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * validate
     */
    public function verify($req,$id){
        $name = $req['name'];
        $des= $req['des'];
        if(is_null($name)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入装饰名称",$name);
        }
        if(is_null($des)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入装饰描述",$des);
        }
        $re = PosterShowDefine::where(IekModel::NAME,$name)->first();
        if(!is_null($re)){
            if(is_null($id)){
                return $this->viewReturn(Errors::EXIST,'装饰名称已存在',$name);
            }else{
                if($re->id != $id){
                    return $this->viewReturn(Errors::EXIST,'装饰名称已存在',$name);
                }
            }
        }
    }
}
?>