<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 10/29/16
 * Time: 4:11 PM
 */

namespace App\Http\Controllers;
use App\Http\Requests;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Manager;
use Illuminate\Http\Request;

class LayoutController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function userNav()
    {
        $manager = $this->checkUser();
        if(!is_null(session('login.id')) || !is_null(session('login.name'))) {
            return view('layout.userNav',compact('manager'));
        } else {
            return view('message.redirect', ['url' => 'login.html']);
        }
    }

    /**
     * @return mixed
     */
    public function subMenu()
    {
        $manager = $this->checkUser();
        if(!is_null($manager)){
            return view('layout.subMenu',compact('manager'));
        }else{
            $err = new Error();
            $err->setError(Errors::NOT_ALLOWED);
            $err->setMessage('你还没有后台管理系统使用权限，请联系超级管理员给你授权');
            return view('message.formResult',['result'=>$err]);
        }
    }

    /**
     * @return mixed
     */
    public function content()
    {
        return view('layout.content');
    }
    /**
     * @return mixed
     */
    public function allTables()
    {
        return view('tableData.all');
    }
    /**
     * @return mixed
     */
    public function errors(Request $request, $status)
    {
        $err = new Error();
        if(!isset($status)) {
            $status = $request -> input('status');
        }
        if(is_null($status)) {
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('invalid params status');
            return view('message.formResult', ['result'=>$err]);
        } else {
            $status = (int)$status;
            /*switch ($status) {
                case 403: return view('errors.403');
                case 404: return view('errors.404');
                case 405: return view('errors.405');
                case 500: return view('errors.500');
                default: return view('message.messageAlert', ['message'=>'error', 'type'=>'error']);
            }*/
            return view('errors.error', ['status' => $status]);
        }
    }

    /**
     * @return mixed
     * access manage
     * check manager of role
     */
    public function checkUser(){
        $id = session('login.id');
        $manager = Manager::whereHas('managerRole.roles')
            ->with('managerRole.roles')
            ->find($id);
        return $manager;
    }
}