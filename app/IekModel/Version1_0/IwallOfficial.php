<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/3/15
 * Time: 10:38
 */
namespace App\IekModel\Version1_0;

class IwallOfficial extends IekModel
{
    protected $table = 'tblIwallOfficials';

    public function manageLog() {
        return $this->hasMany(self::$NAME_SPACE.'\ManageLogs', 'row_id', self::ID);
    }

    public function iwallPerson(){
        return $this->belongsTo(self::$NAME_SPACE.'\IwallPerson',self::WID,self::WID);
    }

    public static function isOfficial($iid) {
        $condition = [
            [self::WID, '=', $iid],
            [self::ACTIVE, '=', true],
            [self::REMOVED, '=', false],
        ];
        $count = self::where($condition)->count();
        return $count == 0 ? false : true;
    }
}