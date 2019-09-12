<?php

namespace App\Http\Controllers\Admin;

use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Manager;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class PassController extends Controller
{
    /**
     * 显示修改密码页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(){
        return view('admin.modifyPassword');
    }
    /**
     * 修改密码
     * @param Request $request
     * @return array
     */
    public function modifyPass(Request $request){
        $err = new Error();
        $uid = session('login.id');
        $input = $request->except('_token');
        if($this->checkPassword()){
            return $this->checkPassword();
        }
        DB::beginTransaction();
        try{
            $re = Manager::where(IekModel::ID,$uid)
                ->update([
                    'password' => Hash::make($input['newPassword'])
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
     * check password
     */
    public function checkPassword(){
        $uid = session('login.id');
        $old = request()->input('oldPassword');
        $new = request()->input('newPassword');
        $confirm = request()->input('confirmPassword');
        $manager = Manager::where(IekModel::ID,$uid)->first();
        $old_pass = Hash::check($old,$manager->password);
        if(is_null($old)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入旧密码','oldPassword');
        }
        if(!$old_pass){
            return $this->viewReturn(Errors::INVALID_PARAMS,'旧密码输入错误','oldPassword');
        }
        if(is_null($new)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入新密码','newPassword');
        }
        if($old === $new){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请确保新密码与旧密码不同','newPassword');
        }
        if($new !== $confirm){
            return $this->viewReturn(Errors::INVALID_PARAMS,'确认密码与新输入密码不一致','confirmPassword');
        }
    }

}
