<?php

namespace App\IekModel\Version1_0;

class Texture extends IekModel
{
    public $primaryKey = 'id';
    protected $table = 'tblTextures';

    public function textureImages(){
        return $this->belongsTo(self::$NAME_SPACE.'\TextureImage', self::IID, self::ID);
    }

    public function colors(){
        return $this->belongsTo(self::$NAME_SPACE.'\Color', self::COLOR_ID, self::ID)
            ->where(self::IS_MODIFY,false);
    }

    public function textureSegments(){
        return $this->hasMany(self::$NAME_SPACE.'\TextureSegment', self::TEXTURE_ID, self::ID);
    }

    public function demiBorder(){
        return $this->hasMany(self::$NAME_SPACE.'\DemiBorder',self::TEXTURE_ID,self::ID)
            ->where(self::CONDITION)
            ->where(self::IS_MODIFY,false);
    }

    public function demiBack(){
        return $this->hasMany(self::$NAME_SPACE.'\DemiBack',self::TEXTURE_ID,self::ID)
            ->where(self::CONDITION)
            ->where(self::IS_MODIFY,false);
    }

    public function demiFrame(){
        return $this->hasMany(self::$NAME_SPACE.'\DemiFrame',self::TEXTURE_ID,self::ID)
            ->where(self::CONDITION)
            ->where(self::IS_MODIFY,false);
    }

    public function demiFront(){
        return $this->hasMany(self::$NAME_SPACE.'\DemiFront',self::TEXTURE_ID,self::ID)
            ->where(self::CONDITION)
            ->where(self::IS_MODIFY,false);
    }

    public static function checkExist($name){
        $count = self::where(self::CONDITION)
            ->where(self::NAME,$name)
            ->count();
        return $count>0 ? true : false;
    }
}
