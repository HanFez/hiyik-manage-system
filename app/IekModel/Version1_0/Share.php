<?php

namespace App\IekModel\Version1_0;


class Share extends IekModel
{
    protected $table = 'tblShares';

    public function person(){
        return $this->belongsTo(self::$NAME_SPACE.'\Person',self::ORIGIN_ID,self::ID);
    }

    public function iwall(){
        return $this->belongsTo(self::$NAME_SPACE.'\Iwall',self::ORIGIN_ID,self::ID);
    }

    public function publication(){
        return $this->belongsTo(self::$NAME_SPACE.'\Publication',self::ORIGIN_ID,self::ID);
    }

    public function personShare(){
        return $this->hasMany(self::$NAME_SPACE.'\PersonShare',self::SHARE_ID,self::ID);
    }
}
