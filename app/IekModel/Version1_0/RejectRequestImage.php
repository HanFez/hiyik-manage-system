<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/8/17
 * Time: 14:44
 */
namespace App\IekModel\Version1_0;

class RejectRequestImage extends IekModel
{
    protected $table = 'tblRejectRequestImages';
    public $primaryKey = 'id';

    public function images(){
        return $this->hasMany(self::$NAME_SPACE.'\RejectImageNorm','image_id','image_id');
    }
}