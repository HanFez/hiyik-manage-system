<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-7-25
 * Time: 下午12:25
 */

namespace App\IekModel\Version1_0\Statistics;
use App\IekModel\Version1_0\IekModel;
class UserFavor extends IekModel{

    protected $table = 'viewPersonFavors';
    public $incrementing = false;
    public $timestamps = false;
    public $primaryKey = 'pid';

}