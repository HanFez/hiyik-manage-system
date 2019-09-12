<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/22
 * Time: 17:03
 */
namespace App\IekModel\Version1_0;

class OrderReceiveInformation extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblOrderReceiveInformations';

    public function receiveInformation(){
        return $this->belongsTo(self::$NAME_SPACE.'\ReceiveInformation','receive_information_id','id');
    }
}