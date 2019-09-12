<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\IekModel\Version1_0;


class PublicationOfficial extends IekModel{
    protected $table = "tblPublicationOfficials";
    protected $guarded = [];
    protected $hidden = [];

    public function publicationForbidden() {
        return $this->hasMany(self::$NAME_SPACE.'\PublicationForbidden', self::PID, self::PID);
    }

    public function manageLog(){
        return $this->hasMany(self::$NAME_SPACE.'\ManageLogs', 'row_id', 'id');
    }

    public function publication(){
        return $this->belongsTo(self::$NAME_SPACE.'\Publication',self::PID,self::ID);
    }

    public static function isOfficial($pid) {
        $condition = [
            [self::PID, '=', $pid],
            [self::ACTIVE, '=', true],
            [self::REMOVED, '=', false],
        ];
        $count = self::where($condition)->count();
        return $count == 0 ? false : true;
    }
    public static function getOfficial($pid) {
        $official = self::where(self::PID, $pid)
            ->where(self::ACTIVE, true)
            ->where(self::REMOVED, false)
            ->orderBy(self::UPDATED_AT, 'desc')
            ->first();
        if(is_null($official)) {
            return null;
        } else {
            return $official;
        }
    }
}