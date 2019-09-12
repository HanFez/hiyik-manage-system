<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/12/17
 * Time: 5:10 PM
 */

namespace App\IekModel\Version1_0\ProductTraceability;


class Image extends IekProductTraceabilityModel
{
    protected $table="tblImages";
    protected $fillable = [
        'file_name', 'extension', 'width', 'height', 'md5',
        'created_at', 'updated_at', 'is_active', 'is_removed',
        'uri',
    ];
    protected $hidden = ['file_name', 'extension', 'width', 'height', 'md5',];

    public function norms() {
        return $this->hasMany(self::$NAME_SPACE.'\ImageNorm', self::IID, self::ID)
            ->where(self::CONDITION);
    }

    public static function isHashExist($hash) {
        if(is_null($hash)) {
            return false;
        }
        $count = self::where(self::HASH_MD5, $hash)->count();
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
        return self::where(self::HASH_MD5, $hash)
//            ->where(self::CONDITION)
            ->orderBy(self::CREATED_AT, 'desc')
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
                if(!is_null($norm) && $norm->is_active && !$norm->is_forbidden) {
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

    public static function isForbidden($iid) {
        $model = self::find($iid);
        if(is_null($model)) {
            return false;
        } else {
            return $model->is_removed;
        }
    }

    public static function isExist($iid){
        $image = self::where(IekModel::ID,$iid)
            ->where(IekModel::ACTIVE,true)
            ->first();
        return is_null($image) ? false : $image;
    }

}