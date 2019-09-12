<?php

namespace App\IekModel\Version1_0;



/**
 * @author       Rich
 */
class ImageNorm extends IekModel
{
    /**
     * @var  id           INT8
     * @var  created_at   timestamps
     * @var  updated_at   timestamps
     * @var  image_id     INT8
     * @var  norm_id      INT8
     * @var  is_active    Boolean
     * @var  is_removed   Boolean
     * @var  md5          String
     * @var  uri          String
     */

    protected $table = 'tblImageNorms';
    protected $fillable = [
        'image_id', 'norm_id', 'width', 'height', 'md5',
        'created_at', 'updated_at', 'is_active', 'is_removed',
        'uri'
    ];
    protected $hidden=['created_at', 'updated_at', 'is_active', 'is_removed',];
    protected $primaryKey = 'id';

    public function norm() {
        return $this->belongsTo(self::$NAME_SPACE.'\Norm', 'norm_id', 'id');
    }

    public function image() {
        return $this->belongsTo(self::$NAME_SPACE.'\Images', 'image_id', 'id');
    }

    /** To check whether a image has norms
     * @param $image_id
     * @return bool
     */
    public static function isImageExists($image_id) {
        if(is_null($image_id)) {
            return false;
        }
        $count = self::where('image_id', $image_id)
            ->where(self::CONDITION)
            ->count();
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function getImageNorms($imageId, $normId = null) {
        $condition = self::CONDITION;
        array_push($condition, [self::IID, '=', $imageId]);
        if(!is_null($normId)) {
            array_push($condition, ['norm_id', '=', $normId]);
            $norm = self::with('norm')
                ->where($condition)
                ->orderBy(self::CREATED, 'desc')
                ->first();
            if(!is_null($norm->norm)) {
                $norm->name = $norm->norm->width.'_'.$norm->norm->height;
                unset($norm->norm);
            }
            return $norm;
        }
        $norms = self::with('norm')
            ->where($condition)
            ->get()->each(function($item, $key){
                if(!is_null($item->norm)) {
                    $item->name = $item->norm->width . '_' . $item->norm->height;
                    unset($item->norm);
                }
            });
        return $norms;
    }
}


