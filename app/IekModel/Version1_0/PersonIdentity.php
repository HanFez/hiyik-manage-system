<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/12/8
 * Time: 14:50
 */
namespace App\IekModel\Version1_0;

class PersonIdentity extends IekModel
{
    protected $table = 'tblPersonIdentities';

    public function identity(){
        return $this->belongsTo(self::$NAME_SPACE.'\Identity','identity_id','id');
    }
}
?>