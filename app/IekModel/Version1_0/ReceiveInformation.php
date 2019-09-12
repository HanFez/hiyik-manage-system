<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/7/19
 * Time: 15:47
 */
namespace App\IekModel\Version1_0;

class ReceiveInformation extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblReceiveInformations';

    public function name(){
        return $this->hasMany(self::$NAME_SPACE.'\Name','id','name_id')
            ->where(IekModel::CONDITION);
    }

    public function phone(){
        return $this->hasMany(self::$NAME_SPACE.'\Phone','id','phone_id')
            ->where(IekModel::CONDITION);
    }

    public function address(){
        return $this->hasMany(self::$NAME_SPACE.'\Address','id','address_id')
            ->where(IekModel::CONDITION);
    }
}