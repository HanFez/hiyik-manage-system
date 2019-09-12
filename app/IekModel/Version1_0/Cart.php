<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/8/14
 * Time: 16:32
 */
namespace App\IekModel\Version1_0;

class Cart extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblCarts';

    public function person(){
        return $this->belongsTo(self::$NAME_SPACE.'\Person','person_id','id');
    }
}