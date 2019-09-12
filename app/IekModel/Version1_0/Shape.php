<?php

namespace App\IekModel\Version1_0;



class Shape extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblShapes';

    public function shapePath(){
        return $this->hasMany(self::$NAME_SPACE.'\ShapePath',self::SHAPE_ID,self::ID);
    }

    public static function checkExist($name){
        $count = self::where(self::CONDITION)
            ->where(self::NAME,$name)
            ->count();
        return $count>0 ? true : false;
    }

    public function hole(){
        return $this->hasMany(self::$NAME_SPACE.'\Hole',self::SHAPE_ID,self::ID)
            ->where(self::CONDITION)
            ->where(self::IS_MODIFY,false);
    }
}
