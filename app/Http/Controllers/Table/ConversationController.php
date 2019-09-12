<?php

namespace App\Http\Controllers\Table;

use App\IekModel\Version1_0\IekModel;
use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Conversation;

class ConversationController extends Controller
{
    public function conversationList($id){
        $conversation = Conversation::where(IekModel::INITIATOR,$id)
            ->orWhere(IekModel::CUSTOMER,$id)
            ->with(['initiatorPerson.personNick'=>function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('nick');
            }])
            ->with(['customerPerson.personNick'=>function($query){
                $query->where(IekModel::ACTIVE,true)
                    ->with('nick');
            }])
            ->get();
        return response()->json($conversation);
    }
}
