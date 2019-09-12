<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/4/17
 * Time: 16:02
 */
namespace App\IekModel\Version1_0;

class Wallet extends IekModel
{
    protected $table = 'tblWallets';

    public function person(){
        return $this->belongsTo(self::$NAME_SPACE.'\Person',self::UID,self::ID);
    }
}
?>