<?php

namespace App\IekModel\Version1_0;


class PersonShare extends IekModel
{
    protected $table = 'tblPersonShares';

    public function share(){
        return $this->belongsTo(self::$NAME_SPACE.'\Share','share_id','id');
    }

    public function person(){
        return $this->belongsTo(self::$NAME_SPACE.'\Person','person_id','id');
    }

    public function platform(){
        return $this->belongsTo(self::$NAME_SPACE.'\SocialPlatform','platform_id','id');
    }

    public function ip(){
        return $this->belongsTo(self::$NAME_SPACE.'\Ip','ip_id','id');
    }
}
