<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\IekModel\Version1_0;

class Description extends IekModel{
    protected $table="tblDescriptions";
    protected $fillable=['is_active', 'is_removed', 'is_publish', 'updated_at','created_at','id','content'];

    public function plainStyle(){
        return $this->belongsTo(self::$NAME_SPACE.'\PlainStyle','id','description_id');
    }

    public function manageLog() {
        return $this->hasMany(self::$NAME_SPACE.'\ManageLogs', 'row_id', 'id');
    }

    public static function checkExist($did){
        $count = self::where(self::ID,$did)
            ->count();
        return $count == 0 ? false : true;
    }
}