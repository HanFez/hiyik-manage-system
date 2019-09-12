<?php

namespace App\IekModel\Version1_0;


class Iwall extends IekModel
{
    protected $table = 'tblIwalls';
    protected $primaryKey = 'id';

    public function iwallTitle(){
        return $this->hasOne(self::$NAME_SPACE.'\IwallTitle',IekModel::WID,IekModel::ID)
            ->where(IekModel::CONDITION);
    }

    public function iwallDescriptions(){
        return $this->hasMany(self::$NAME_SPACE.'\IwallDescription',IekModel::WID,IekModel::ID);
    }

    public function iwallTags(){
        return $this->hasMany(self::$NAME_SPACE.'\IwallTag',IekModel::WID,IekModel::ID);
    }

    public function iwallCover(){
        return $this->hasOne(self::$NAME_SPACE.'\IwallCover',IekModel::WID,IekModel::ID)
            ->where(IekModel::CONDITION);
    }

    public function official(){
        return $this->hasMany(self::$NAME_SPACE.'\IwallOfficial',IekModel::WID,IekModel::ID);
    }

    public function officialReason() {
        return $this->hasMany(self::$NAME_SPACE.'\ManageLogs', IekModel::ROW_ID, self::ID)
            ->where('table_name',IwallOfficial::getDataTable());
    }

    public function iwallProducts(){
        return $this->hasMany(self::$NAME_SPACE.'\IwallProduct','iwall_id','id');
    }

    public function iwallScene(){
        return $this->hasMany(self::$NAME_SPACE.'\IwallScene',IekModel::WID,IekModel::ID);
    }

    public function iwallCrowd(){
        return $this->hasMany(self::$NAME_SPACE.'\IwallCrowd',IekModel::WID,IekModel::ID);
    }

    public function iwallSex(){
        return $this->hasOne(self::$NAME_SPACE.'\IwallSex',IekModel::WID,IekModel::ID);
    }

    public function iwallPerson(){
        return $this->hasOne(self::$NAME_SPACE.'\IwallPerson',IekModel::WID,IekModel::ID);
    }

    public function iwallForbidden() {
        return $this->hasMany(self::$NAME_SPACE.'\ManageLogs', IekModel::ROW_ID, self::ID)
            ->where('table_name',self::getDataTable());
    }

    public function wall(){
        return $this->belongsTo(self::$NAME_SPACE.'\IwallWall',IekModel::ID,IekModel::WID);
    }

    public static function checkIwall($iid){
        $count = self::where(self::ID,$iid)
            ->count();
        return $count == 0 ? false : true;
    }
}
