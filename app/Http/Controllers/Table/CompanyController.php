<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2019/6/27
 * Time: 17:49
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Company;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    /**
     * company's all data list
     */
    public function index(){
        $model = new Company();
        $type = 'company';
        $getList = new IndexController();
        $result = $getList->tableList($model ,$type);
        return $result;
    }
    /**
     * company's add page
     */
    public function create(){
        $field = Company::tableSchema();
        foreach($field as $k => $fed){
            if($fed->column_name == 'id') unset($field[$k]);
        }
        return view('tableData.add',['field'=>$field]);
    }
    /**
     * company's add data deal
     */
    public function store(Request $request){

        $err = new Error();
        $input = request()->except('_token');
        if(is_null($input['name'])){
            return $this->viewReturn(Errors::NOT_EMPTY,'请输入快递公司名称','name');
        }
        if(is_null($input['description'])){
            return $this->viewReturn(Errors::NOT_EMPTY,'请输入快递公司描述','description');
        }
        $ext = Company::where(IekModel::CONDITION)
            ->where(IekModel::NAME,$input['name'])
            ->count();
        if($ext>0){
            return $this->viewReturn(Errors::EXIST,'该快递公司名称已存在','name');
        }
        DB::beginTransaction();
        try{
            $company = new Company();
            $company->name = $input['name'];
            $company->description = $input['description'];
            $re = $company->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'成功','失败',$re);
    }
    /**
     * company's data detail
     */
    public function show($id){
        //
    }
    /**
     * company's edit page
     */
    public function edit($id){
        $result = Company::find($id);
        $field = Company::tableSchema();
        foreach($field as $k => $fed){
            if($fed->column_name == 'id') unset($field[$k]);
        }
        return view('tableData.edit',compact('result','field'));
    }
    /**
     * company's edit data deal
     */
    public function update($id){
        $err = new Error();
        $input = request()->except('_token');
        if(is_null($input['name'])){
            return $this->viewReturn(Errors::NOT_EMPTY,'请输入快递公司名称','name');
        }
        if(is_null($input['description'])){
            return $this->viewReturn(Errors::NOT_EMPTY,'请输入快递公司描述','description');
        }
        DB::beginTransaction();
        try{
            $re = Company::where(IekModel::ID,$id)->update($input);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'成功','失败',$re);
    }
    /**
     * delete company's record
     */
    public function del(){
        $model = new Company();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover company's record
     */
    public function cover(){
        $model = new Company();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
}
?>