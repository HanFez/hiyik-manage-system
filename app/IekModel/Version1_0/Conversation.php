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
class Conversation extends IekModel {
    /**
    * @var  id           INT8
    * @var  message_type INT4
    * @var  created_at   timestamps
    * @var  updated_at   timestamps
    * @var  content      String
    * @var  is_active    Boolean
    * @var  is_removed   Boolean
    * @var  hash         String
    */

    public $primaryKey = 'id';
    protected $table = 'tblConversations';

    public static function getConversations($uid, $skip, $take) {
        $result = null;
        $takeQuery = null;
        $tempSkip = $skip;
        $tempTake = $take;
        if(!is_null($tempTake) && $tempTake > 0) {
            if(is_null($tempSkip)) {
                $tempSkip = 0;
            }
            $takeQuery = sprintf(' offset %d limit %d', $tempSkip, $tempTake);
        }
        $totalQuery = sprintf('select count(*) from (select c.id
                  from "tblConversations" as c 
                  left join
                   (select conversation_id as cid, max(created_at) as n 
                    from "tblChatSessions" group by cid order by n desc) 
                    as s on c.id=s.cid
                  where c.is_active=\'t\' and c.is_removed=\'f\' 
                  and ((c.initiator=%d and c.initiator_removed=\'f\')
                   or (c.customer=%d and c.customer_removed=\'f\' and is_group=\'f\')) 
                  order by s.n desc) as r', $uid, $uid);
        $total = DB::select(DB::raw($totalQuery));
        if(!is_null($total) && !empty($total)) {
            $total = $total[0]->count;
            if($total > 0) {
                $query = sprintf('select c.id, c.is_active, c.is_removed,c.created_at, c.updated_at, s.n as last_at,
                    c.initiator, c.customer, c.initiator_removed, c.customer_removed, c.is_group
                  from "tblConversations" as c 
                  left join
                   (select conversation_id as cid, max(created_at) as n 
                    from "tblChatSessions" group by cid order by n desc) 
                    as s on c.id=s.cid
                  where c.is_active=\'t\' and c.is_removed=\'f\' 
                  and ((c.initiator=%d and c.initiator_removed=\'f\')
                   or (c.customer=%d and c.customer_removed=\'f\' and is_group=\'f\')) 
                  order by s.n desc nulls last', $uid, $uid);
                if (!is_null($takeQuery)) {
                    $query = $query . $takeQuery;
                }
                $result = DB::select(DB::raw($query));
            }
        }
        if(is_null($result) || empty($result)) {
            $result = null;
        }
        $data = new \stdClass();
        $data->{self::TOTAL} = $total;
        $data->{self::SKIP} = $skip;
        $data->{self::TAKE} = $take;
        $data->{self::DATA} = $result;
        return $data;
    }

    public static function deleteConversation($uid, $cid) {
        $conversation = self::find($cid);
        if(!is_null($conversation)) {
            if($conversation->initiator == $uid) {
                $conversation->initiator_removed = true;
            }
            if($conversation->customer == $uid) {
                $conversation->customer_removed = true;
            }
            $conversation->save();
        }
        return $conversation;
    }

    public function chatSession() {
        return $this->hasMany(self::$NAME_SPACE.'\ChatSession', 'conversation_id', self::ID);
    }

    public function initiatorPerson() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', self::INITIATOR , self::ID);
    }

    public function customerPerson() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', self::CUSTOMER , self::ID);
    }
}

?>
