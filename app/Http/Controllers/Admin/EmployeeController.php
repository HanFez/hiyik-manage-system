<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\IndexController;
use App\Http\Controllers\TraitRequestParams;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Employee;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Manager;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    use TraitRequestParams;

    /***
     * employee list
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function index()
    {
        $model = new Employee();
        $type = 'employee';
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
        $emp = new Employee();
        $field = $emp->tableSchema();
        return view('tableData.add',compact('field'));
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
        if($this->checkFiled($request)){
            return $this->checkFiled($request);
        }
        $employee = $request->except('_token');
        $info = Employee::find($employee['id']);
        if($info['id'] === $employee['id']){
            return $this->viewReturn(Errors::INVALID_PARAMS,'此账号已被使用',$employee['id']);
        }
        if(empty($employee['birthday'])){
            unset($employee['birthday']);
        }
        DB::beginTransaction();
        try{
            $re = Employee::create($employee);
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
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $result = Employee::find($id);
        $emp = new Employee();
        $field = $emp->tableSchema();
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
        if($this->checkFiled($request)){
            return $this->checkFiled($request);
        }
        $employee = $request->except('_token');
        DB::beginTransaction();
        try{
            $re = Employee::where(IekModel::ID,$id)
                ->update($employee);
            DB::commit();
        }catch (\Exception $e ){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'修改成功 !','修改失败 !',$re);
    }

    /**
     * 批量删除
     */
    public function del(){
        $err = new Error();
        $ids = request()->input('ids');
        DB::beginTransaction();
        try{
            $re = Employee::whereIn(IekModel::ID, $ids)
                ->update([
                    IekModel::REMOVED => true
                ]);
            foreach($ids as $id){
                $res = Manager::where(IekModel::ID,$id)
                    ->where(IekModel::CONDITION)
                    ->count();
                if($res>0){
                    Manager::where(IekModel::ID,$id)
                        ->update([
                            IekModel::REMOVED => true
                        ]);
                }
            }
            $err->setData($re);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        if($re){
            $err->setError(Errors::OK);
        }else{
            $err->setError(Errors::FAILED);
        }
        return response()->json($err);
    }

    /**
     * 批量恢复
     */
    public function recover(){
        $err = new Error();
        $ids = request()->input('ids');
        DB::beginTransaction();
        try{
            $re = Employee::whereIn(IekModel::ID, $ids)
                ->update([
                    IekModel::REMOVED => false
                ]);
            foreach($ids as $id){
                $res = Manager::where(IekModel::ID,$id)
                    ->where(IekModel::CONDITION)
                    ->count();
                if($res>0){
                    Manager::where(IekModel::ID,$id)
                        ->update([
                            IekModel::REMOVED => false
                        ]);
                }
            }
            $err->setData($re);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        if($re){
            $err->setError(Errors::OK);
        }else{
            $err->setError(Errors::FAILED);
        }
        return response()->json($err);
    }

    /**
     * 验证前端输入条件
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function checkFiled(Request $request){
        $userId = $this->getRequestParam($request,'id');
        $userName = $this->getRequestParam($request,'name');
        $phone = $this->getRequestParam($request,'phone');
        $email = $this->getRequestParam($request,'mail');
        $identityCard = $this->getRequestParam($request,'identifier');
        $address = $this->getRequestParam($request,'address');
        $memo = $this->getRequestParam($request,'memo');
        if(!preg_match('/^\w{4,8}$/',$userId)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入4-8位的字符串（字符为A-Za-z0-9_）!','id');
        }
        if(is_null($userId) || empty($userId)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入员工!','id');
        }
        if(is_null($userName) || empty($userName)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入员工姓名!','name');
        }
        if(!is_null($phone) && !empty($phone)){
            $isMob="/^1[3-5,7,8]{1}[0-9]{9}$/";
            $isTel="/^([0-9]{3,4}-)?[0-9]{7,8}$/";
            if(!preg_match($isMob,$phone) && !preg_match($isTel,$phone)){
                return $this->viewReturn(Errors::INVALID_PARAMS,'请输入电话号码!','phone');
            }
        }
        if(!empty($email) && !is_null($email)){
            $pattern = "/^([0-9A-Za-z\\-_\\.]+)@([0-9a-z]+\\.[a-z]{2,4}(\\.[a-z]{2})?)$/i";
            if(!preg_match($pattern,$email)){
                return $this->viewReturn(Errors::INVALID_PARAMS,'请输入正确的邮箱地址!','mail');
            }
        }
        if(is_null($identityCard) || empty($identityCard)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入身份证号码!','identifier');
        }
        if(strlen($identityCard) !== 18){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入18位数字或加字母的身份证号码!','identifier');
        }
        if(!is_null($address) && !empty($address)){
            if(strlen($address) > 255){
                return $this->viewReturn(Errors::INVALID_PARAMS,'地址长度大于255字符!','address');
            }
        }
        if(!is_null($memo) && !empty($memo)){
            if(strlen($memo) > 255){
                return $this->viewReturn(Errors::INVALID_PARAMS,'备注长度大于255字符!','memo');
            }
        }
    }

    /**
     * 过滤id为特殊字符
     * @param $str
     * @return string
     */
    public function strFilter($str){
        $str = str_replace('`', '', $str);
        $str = str_replace('·', '', $str);
        $str = str_replace('~', '', $str);
        $str = str_replace('!', '', $str);
        $str = str_replace('！', '', $str);
        $str = str_replace('@', '', $str);
        $str = str_replace('#', '', $str);
        $str = str_replace('$', '', $str);
        $str = str_replace('￥', '', $str);
        $str = str_replace('%', '', $str);
        $str = str_replace('^', '', $str);
        $str = str_replace('……', '', $str);
        $str = str_replace('&', '', $str);
        $str = str_replace('*', '', $str);
        $str = str_replace('(', '', $str);
        $str = str_replace(')', '', $str);
        $str = str_replace('（', '', $str);
        $str = str_replace('）', '', $str);
        $str = str_replace('-', '', $str);
        $str = str_replace('_', '', $str);
        $str = str_replace('——', '', $str);
        $str = str_replace('+', '', $str);
        $str = str_replace('=', '', $str);
        $str = str_replace('|', '', $str);
        $str = str_replace('\\', '', $str);
        $str = str_replace('[', '', $str);
        $str = str_replace(']', '', $str);
        $str = str_replace('【', '', $str);
        $str = str_replace('】', '', $str);
        $str = str_replace('{', '', $str);
        $str = str_replace('}', '', $str);
        $str = str_replace(';', '', $str);
        $str = str_replace('；', '', $str);
        $str = str_replace(':', '', $str);
        $str = str_replace('：', '', $str);
        $str = str_replace('\'', '', $str);
        $str = str_replace('"', '', $str);
        $str = str_replace('“', '', $str);
        $str = str_replace('”', '', $str);
        $str = str_replace(',', '', $str);
        $str = str_replace('，', '', $str);
        $str = str_replace('<', '', $str);
        $str = str_replace('>', '', $str);
        $str = str_replace('《', '', $str);
        $str = str_replace('》', '', $str);
        $str = str_replace('.', '', $str);
        $str = str_replace('。', '', $str);
        $str = str_replace('/', '', $str);
        $str = str_replace('、', '', $str);
        $str = str_replace('?', '', $str);
        $str = str_replace('？', '', $str);
        return trim($str);
    }
}
