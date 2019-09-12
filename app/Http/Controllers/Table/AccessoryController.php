<?php

namespace App\Http\Controllers\Table;

use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Accessory;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class AccessoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $model = new Accessory();
        $type = 'accessory';
        $getList = new IndexController();
        $result = $getList->tableList($model ,$type);
        return $result;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $field = Accessory::tableSchema();
        foreach($field as $k => $fed){
            if($fed->column_name == 'id') unset($field[$k]);
        }
        return view('tableData.add',['field'=>$field]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $err = new Error();
        $input = request()->except('_token');
        if(is_null($input['name'])){
            return $this->viewReturn(Errors::NOT_EMPTY,'请输入配件名','name');
        }
        if(is_null($input['num'])){
            return $this->viewReturn(Errors::NOT_EMPTY,'请输入配件数量','num');
        }
        $ext = Accessory::where(IekModel::CONDITION)
            ->where(IekModel::NAME,$input['name'])
            ->count();
        if($ext>0){
            return $this->viewReturn(Errors::EXIST,'该配件名称已存在','name');
        }
        DB::beginTransaction();
        try{
            $accessory = new Accessory();
            $accessory->name = $input['name'];
            $accessory->num = $input['num'];
            $accessory->only_core_need = $input['only_core_need'];
            $re = $accessory->save();
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
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result = Accessory::find($id);
        $field = Accessory::tableSchema();
        foreach($field as $k => $fed){
            if($fed->column_name == 'id') unset($field[$k]);
        }
        return view('tableData.edit',compact('result','field'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $err = new Error();
        $input = $request->except('_token');
        if(is_null($input['name'])){
            return $this->viewReturn(Errors::NOT_EMPTY,'请输入配件名','name');
        }
        if(is_null($input['num'])){
            return $this->viewReturn(Errors::NOT_EMPTY,'请输入配件数量','num');
        }
        DB::beginTransaction();
        try{
            $re = Accessory::where(IekModel::ID,$id)->update($input);
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
     * delete accessory
     */
    public function del(){
        $model = new Accessory();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover accessory
     */
    public function recover(){
        $model = new Accessory();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
}
