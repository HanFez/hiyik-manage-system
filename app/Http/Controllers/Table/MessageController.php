<?php

namespace App\Http\Controllers\Table;

use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Message;

class MessageController extends Controller
{
    public function messageList(Request $request){
        $err = new Error();
        $isForbidden = $request->input('isForbidden');
        if($isForbidden != 'true'){
            $isForbidden = false;
        }
        if($isForbidden){
            $message = Message::where(IekModel::REMOVED,true)
                ->get();
        }else{
            $message = Message::where(IekModel::REMOVED,false)
                ->get();
        }
        $err->setData($message);
        return response()->json($err);
    }
}
