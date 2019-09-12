<?php
//
// +------------------------------------------------------------------------+
// | PHP Version 5                                                          |
// +------------------------------------------------------------------------+
// | Copyright (c) All rights reserved.                                     |
// +------------------------------------------------------------------------+
// | File:                                                                  |
// +------------------------------------------------------------------------+
// | Author:                                                                |
// +------------------------------------------------------------------------+
//
// $Id$
//

namespace App\IekModel\Version1_0;


use Illuminate\Support\Facades\DB;
/**
* @author       Rich
*/
class ChatSession extends IekModel
{
    /**
    * @var  id           INT8
    * @var  content_type INT4
    * @var  created_at   timestamps
    * @var  updated_at   timestamps
    * @var  content      String
    * @var  is_active    Boolean
    * @var  is_removed   Boolean
    * @var  hash         String
    */

    public $primaryKey = 'id';
    protected $table = 'tblChatSessions';

    public function from() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', 'from_id', 'id');
    }

    public function to() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', 'to_id', 'id');
    }

    public function conversation() {
        return $this->belongsTo(self::$NAME_SPACE.'\Conversation', 'conversation_id', 'id')
            ->where(self::CONDITION);
    }

    public function message() {
        return $this->belongsTo(self::$NAME_SPACE.'\Message', 'message_id', 'id');
    }

    public static function sendMessage($uid, $cid, $params) {
        $from = $params->from;
        $to = $params->to;
        $message = Message::insertMessage($params->content, $params->hash, $params->hashType);
        $conversation = Conversation::find($cid);
        $session = null;
        if(!is_null($conversation) && !is_null($message)) {
            $session = new self();
            $session->conversation_id = $conversation->id;
            $session->message_id = $message->id;
            if($conversation->{Conversation::INITIATOR_REMOVED}) {
                $conversation->{Conversation::INITIATOR_REMOVED} = false;
            }
            if($conversation->{Conversation::CUSTOMER_REMOVED}) {
                $conversation->{Conversation::CUSTOMER_REMOVED} = false;
            }
//            if($from == $conversation->{Conversation::INITIATOR}) {
//                $session->is_send = true;
//                if($conversation->{Conversation::INITIATOR_REMOVED}) {
//                    $conversation->{Conversation::INITIATOR_REMOVED} = false;
//                }
//            }
//            if($to == $conversation->{Conversation::INITIATOR}) {
//                $session->is_send = false;
//                if($conversation->{Conversation::INITIATOR_REMOVED}) {
//                    $conversation->{Conversation::INITIATOR_REMOVED} = false;
//                }
//            }
//            if($from == $conversation->{Conversation::CUSTOMER}) {
//                $session->is_send = true;
//                if($conversation->{Conversation::CUSTOMER_REMOVED}) {
//                    $conversation->{Conversation::CUSTOMER_REMOVED} = false;
//                }
//            }
//            if($to == $conversation->{Conversation::CUSTOMER}) {
//                $session->is_send = false;
//                if($conversation->{Conversation::CUSTOMER_REMOVED}) {
//                    $conversation->{Conversation::CUSTOMER_REMOVED} = false;
//                }
//            }
            $session->from_id = $from;
            $session->to_id = $to;
            $session->is_delivered = true;
            $session->is_read = false;
            if(!is_null($params->isForward) && is_bool($params->isForward)) {
                $session->is_forward = $params->isForward;
            } else {
                $session->is_forward = false;
            }
            $session->save();
            $conversation->save();
        }
        return $session;
    }
    public static function getMessages($uid, $cid, $skip, $take) {
        $messages = null;
        if(is_null($uid) || is_null($cid)) {
            return $messages;
        }
        //$conversation = Conversation::find($cid);
        //$total = 0;
        $result = null;
        //if(!is_null($conversation)) {
            $countQuery = sprintf('select count(cs.id) 
                      from "tblChatSessions" as cs,"tblMessages" as m 
                      where cs.message_id=m.id and cs.conversation_id = %d 
                      and ((cs.from_id=%d and cs.from_removed=\'f\')
                       or (cs.to_id=%d and cs.to_removed=\'f\')) 
                       ',
                $cid, $uid, $uid);
            $total = DB::select(DB::raw($countQuery));
            if(!is_null($total) && !empty($total)) {
                $total = $total[0]->count;
            } else {
                $total = 0;
            }
            if($total > 0) {
                $query = sprintf('select cs.*, m.content as content, m.content_type as content_type
                      from "tblChatSessions" as cs,"tblMessages" as m 
                      where cs.message_id=m.id and cs.conversation_id = %d 
                      and ((cs.from_id=%d and cs.from_removed=\'f\')
                       or (cs.to_id=%d and cs.to_removed=\'f\')) 
                       order by cs.created_at %s offset %d limit %d',
                    $cid, $uid, $uid, 'desc', $skip, $take);
                $result = DB::select(DB::raw($query));
                self::where('is_read', false)
                    ->where('conversation_id', $cid)
                    ->where('to_id', $uid)
                    ->update(['is_read' => true]);
            }
        //}
        $data = new \stdClass();
        $data->{self::TOTAL} = $total;
        $data->{self::SKIP} = $skip;
        $data->{self::TAKE} = $take;
        $data->{self::DATA} = $result;
        return $data;
    }

    public static function unreadCount($uid, $cid) {
        $unread = 0;
        if(is_null($uid) || is_null($cid)) {
            return $unread;
        }
        $unread = self::unreadTotal($uid, [$cid]);
        return $unread;
    }

    public static function unreadTotal($uid, $cids) {
        $unread = 0;
        if(is_null($uid) || is_null($cids) || empty($cids)) {
            return $unread;
        }
        $unread = self::where(IekModel::CONDITION)
            ->whereIn('conversation_id', $cids)
            ->where('to_id', $uid)
            ->where('is_read', false)
//            ->where(function ($query) use ($uid, $cids) {
//                $query->where(function($query) use ($uid, $cids) {
//                    $query->where('is_send', true)
//                        ->whereHas('conversation', function($query) use ($uid, $cids) {
//                            $query->whereIn(IekModel::ID, $cids)
//                                ->where(IekModel::CUSTOMER, $uid);
//                        });
//                })
//                ->orWhere(function($query) use ($uid, $cids) {
//                    $query->where('is_send', false)
//                        ->whereHas('conversation', function($query) use ($uid, $cids) {
//                            $query->whereIn(IekModel::ID, $cids)
//                                ->where(IekModel::INITIATOR, $uid);
//                        });
//                });
//            })
            ->count();
        return $unread;
    }
    public static function deleteMessage($uid, $sid) {
        $session = self::find($sid);
        if(!is_null($session)) {
            if($uid == $session->from_id) {
                $session->from_removed = true;
            } else if($uid == $session->to_id) {
                $session->to_removed = true;
            }
            $session->save();
        }
        return $session;
    }

    public static function deleteMessages($uid, $cid) {
        $total = 0;
        self::where(self::CONDITION)
            ->where('conversation_id', $cid)
            ->where('from_id', $uid)
            ->where('from_removed', false)
            ->update(['from_removed' => true]);
        self::where(self::CONDITION)
            ->where('conversation_id', $cid)
            ->where('to_id', $uid)
            ->where('to_removed', false)
            ->update(['to_removed' => true]);
        return $total;
    }

    public static function getUnreadMessages($uid, $cid) {
        $msgTable = Message::getDataTable();
        $table = self::getDataTable();
        $messages = self::select($table.'.*', $msgTable.'.'.self::CONTENT, $msgTable.'.content_type')
            ->join($msgTable, 'message_id', '=', $msgTable.'.'.self::ID)
            ->where('conversation_id', $cid)
            ->where('to_id', $uid)
            ->where('is_read', false)
            ->get()
            ->toArray();
        self::where('conversation_id', $cid)
            ->where('to_id', $uid)
            ->where('is_read', false)
            ->update(['is_read' => true]);
        $data = new \stdClass();
        $data->{self::TOTAL} = count($messages);
        if(!is_null($messages) && !empty($messages)) {
            $data->{self::DATA} = $messages;
        } else {
            $data->{self::DATA} = null;
        }
        return $data;
    }

    public static function getFinalMessage($uid, $cid) {
        $session = self::with('message')
            ->where('conversation_id', $cid)
            ->where(function($query) use ($uid) {
                $query->where('from_id', $uid)
                    ->where('from_removed', false);
            })
            ->orWhere(function($query) use ($uid) {
                $query->where('to_id', $uid)
                    ->where('to_removed', false);
            })
            ->orderBy(self::CREATED, 'desc')
            //->toSql();
            ->first();
        return $session;
    }
}

?>
