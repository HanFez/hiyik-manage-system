<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/1/10
 * Time: 16:18
 */
namespace App\IekModel\Version1_0;

class CashAudit extends IekModel
{
    protected $table = 'tblCashAudits';

    public function reason(){
        return $this->belongsTo(self::$NAME_SPACE.'\Reason',self::REAID,self::ID);
    }
}
?>