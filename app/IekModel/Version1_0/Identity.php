<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/12/8
 * Time: 14:55
 */
namespace App\IekModel\Version1_0;

class Identity extends IekModel
{
    protected $table = 'tblIdentities';

    public function phone(){
        return $this->belongsTo(self::$NAME_SPACE.'\Phone',self::PHONE_ID,self::ID);
    }
}
?>