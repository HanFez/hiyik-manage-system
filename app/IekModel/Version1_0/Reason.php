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

/**
 * @author       Rich
 */
class Reason extends IekModel
{
    /**
     * @var  role_id             INT4
     * @var  name                String
     * @var  description         String
     * @var  created_at          timestamps
     * @var  updated_at          timestamps
     * @var  is_actived          Boolean
     * @var  is_removed          Boolean
     * @var  memo                String
     */

    public $primaryKey = 'id';
    protected $table = 'tblReasons';
    protected $fillable = array('reason', 'type');

    //查重
    public static function existReason($reason,$type,$open){
        $res = self::where(self::REASON,$reason)
            ->where(self::TYPE,$type)
            ->where(self::OPEN_REASON,$open)
            ->where(IekModel::CONDITION)
            ->count();
        return $res == 0 ? true : false;
    }

    //查出条件下的已有内容
    public static function queryReason($reason,$type,$open){
        $res = self::where(self::REASON,$reason)
            ->where(self::TYPE,$type)
            ->where(self::OPEN_REASON,$open)
            ->where(IekModel::CONDITION)
            ->first();
        return $res ;
    }

    public static function insertReason($content,$type,$openContent){
        $result = self::firstOrCreate(['reason'=>$content,'type'=>$type,'open_reason'=>$openContent]);
//        $result = new self;
//        $result ->reason = $content;
//        $result ->type = $type;
//        $result ->save();
        return $result ->id;
    }

    public static function checkReason($id){
        $count = self::where(self::ID,$id)
            ->where(self::CONDITION)
            ->count();
        return $count == 0 ? false : true;
    }

    public static function getReasonByType($type){
        $reason = self::where(self::CONDITION)
            ->where(self::TYPE,$type)
            ->get();
        return $reason;
    }

    public static function getReason(){
        $reason = self::where(self::CONDITION)
            ->get();
        return $reason;
    }
}

?>
