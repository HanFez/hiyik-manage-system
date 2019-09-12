<?php

namespace App\Http\Controllers\Table;

use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\ChatSession;

class ChatSessionController extends Controller
{
    public function conversationSession($id){
        $err = new Error();
        $session = ChatSession::where(IekModel::CONVERSATION_ID,$id)
            ->with(['from.personNick'=>function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('nick');
            }])
            ->with(['to.personNick'=>function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('nick');
            }])
            ->with('message')
            ->orderBy(IekModel::CREATED)
            ->get();
        $err->setData($session);
        return response()->json($err);
    }
}
