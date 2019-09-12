<?php

namespace App\Http\Controllers\Login;

use App\Http\Controllers\TraitRequestParams;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\IekModel;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Manager;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller{
    use TraitRequestParams;

    public function index(){
        return view('admin.login');
    }
     /**
      * login
      * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
      */
    public function login(){
        Manager::initialSuperMan();//生成默认管理员
        $request = request();
        if(Input::all()){
            if($param = $this->getPostParam($request)){
                return $param;
            }
            $name = $request->input('userName');
            if($name == '2222'){
                Manager::initialSuperRole($name);
            }
            $login = Manager::where(IekModel::ID, $name)
                ->where(Manager::CONDITION)
                ->with('employee')
                ->first();
            if (!is_null($login)) {
                if(!Hash::check($request->input('password'), $login->password)){
                    return $this->viewReturn(Errors::INVALID_PARAMS,'密码不正确!','password');
                }
                if (!is_null($login->employee)) {
                    session(['login.name' => $login->employee->name]);
                }
            } else {
                /*$logins = Employee::where(Employee::CONDITION)
                    ->where('name', $name)
                    ->with('manager')->get();
                if (!is_null($logins)) {
                    foreach ($logins as $m) {
                        if($m->name != $name){
                            return $this->viewReturn(Errors::INVALID_ACCOUNT,'Account does not exist!','userName');
                        }
                        if (!Hash::check($request->input('password'), $m->manager->password)) {
                            return $this->viewReturn(Errors::INVALID_PARAMS,'Incorrect password!','password');
                        }
                        session(['login.name' => $m->name]);
                        $login = $m->manager;
                        break;
                    }
                } else {
                    return $this->viewReturn(Errors::INVALID_ACCOUNT,'Account does not exist!','userName');
                }*/
                return $this->viewReturn(Errors::INVALID_ACCOUNT,'Account does not exist!','userName');
            }
            session(['login.id' => $login->id]);
            return view('message.redirect', ['url' => 'admin.html']);
        }else{
            return view('admin.login');
        }
    }

     /**
      * logout
      * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
      */
    public function quit(){
        session(['login.name' => null]);
        session(['login.id' => null]);
        return view('message.redirect',['url'=>'login.html']);
    }

     /**
      * 账号密码验证
      * @param Request $request
      * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
      */
    public function getPostParam(Request $request){
        $username = $this->getRequestParam($request, 'userName');
        $password = $this->getRequestParam($request, 'password');
        if(is_null($username)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入账号!','userName');
        }
        if(is_null($password)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入密码!','password');
        }
    }
}
