<?php

namespace App\Http\Controllers;

use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\Product\Material;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /**
     * 验证表单字段专用
     * @param $error
     * @param null $message
     * @param null $fieldName
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function viewReturn($error,$message,$fieldName){
        $err = new Error();
        $err->setError($error);
        if(!is_null($message)) {
            $err->setMessage($message);
        }
        $err->setData($fieldName);
        return view('message.formResult',['result'=>$err]);
    }

    /**
     * 针对增、改返回信息
     * @param $error1
     * @param $error2
     * @param $message1
     * @param $message2
     * @param $re
     * @param null $model
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function curd($error1,$error2,$message1,$message2,$re,$model=null){
        $err = new Error();
        if($re){
            $err->setError($error1);
            $err->setMessage($message1);
        }else{
            $err->setError($error2);
            $err->setMessage($message2);
        }
        $err->data = $model;
        return view('message.formResult',['result'=>$err]);
    }

    /**
     * @param $model
     * @param $id
     * @return mixed
     * check material relation isn't intact
     */
    public static function limitBorder($model,$id){
        $param = $model::whereHas('texture')
            ->whereHas('section')
            ->find($id);
        return $param;
    }
    public static function limitOther($model,$id){
        $param = $model::whereHas('texture')
            ->find($id);
        return $param;
    }
}
