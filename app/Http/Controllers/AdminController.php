<?php

namespace App\Http\Controllers;


class AdminController extends Controller
{
    public function index(){
        if(is_null(session('login.id')) || is_null(session('login.name'))){
            return view('message.redirect',['url'=>'login.html']);
        }else{
            return view('message.redirect',['url'=>'admin.html']);
        }
    }
}
