<?php

namespace App\Http\Controllers\Table;

use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Comment;

class CommentController extends Controller
{
    public static $model = Comment::class;

    public function commentList(Request $request){
        $err = new Error();
        $isForbidden = $request->input('isForbidden');
        if($isForbidden != 'true'){
            $isForbidden = false;
        }
        if($isForbidden){
            $comment = Comment::where(IekModel::REMOVED,true)->get();
        }else{
            $comment = Comment::where(IekModel::REMOVED,false)->get();
        }
        $err->setData($comment);
        return response()->json($err);
    }
}
