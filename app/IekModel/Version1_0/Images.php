<?php

namespace App\IekModel\Version1_0;


class Images extends IekModel {

    //
    protected $table = 'tblImages';
    protected $fillable = [
        'file_name', 'extension', 'width', 'height', 'md5',
        'created_at', 'updated_at', 'is_active', 'is_removed',
        'uri',
    ];
    protected $hidden = ['file_name', 'extension', 'width', 'height', 'md5','uri',];

    public function norms() {
        return $this->hasMany(self::$NAME_SPACE.'\ImageNorm', 'image_id', 'id');
    }

    public function tone() {
        return $this->hasOne(self::$NAME_SPACE.'\ImageTone', 'image_id', 'id');
    }

    public function publicationCover(){
        return $this->belongsTo(self::$NAME_SPACE.'\PublicationCover','id','image_id')
            ->where(IekModel::CONDITION);
    }

    public function publicationImage(){
        return $this->belongsTo(self::$NAME_SPACE.'\PublicationImage','id','image_id')
            ->where(IekModel::CONDITION);
    }

    public function iwallCover(){
        return $this->belongsTo(self::$NAME_SPACE.'\IwallCover','id','image_id')
            ->where(IekModel::CONDITION);
    }

    public function manageLog() {
        return $this->hasMany(self::$NAME_SPACE.'\ManageLogs', 'row_id', 'id');
    }

    public static function isHashExist($hash) {
        if(is_null($hash)) {
            return false;
        }
        $count = self::where(self::HASH, $hash)->count();
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function getImageByHash($hash) {
        if(is_null($hash)) {
            return null;
        }
        return self::where(self::HASH, $hash)
            ->where(self::CONDITION)
            ->orderBy(self::CREATED, 'desc')
            ->first();
    }

    public static function getNorms($iid) {
        $condition = [
            [self::ACTIVE, '=', true],
            [self::REMOVED, '=', false],
            [self::IID, '=', $iid],
        ];
        $imageNorms = ImageNorm::where($condition)->get();
        $norms = [];
        if(!is_null($imageNorms)) {
            foreach ($imageNorms as $imageNorm) {
                $norm = $imageNorm->norm;
                if(!is_null($norm) && $norm->is_active && !$norm->is_removed) {
                    $name = $norm->width.'_'.$norm->height;
                    unset($imageNorm->norm);
                    $imageNorm->name = $name;
                    array_push($norms, $imageNorm);
                } else {
                    continue;
                }
            }
            return $norms;
        } else {
            return null;
        }
    }

    public static function checkExist($iid){
        $count = self::where(self::ID,$iid)
            ->count();
        return $count == 0 ? false : true;
    }
}
