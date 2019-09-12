<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/12/17
 * Time: 5:19 PM
 */

namespace App\IekModel\Version1_0\ProductTraceability;


class Norm extends IekProductTraceabilityModel
{
    protected $table="tblNorms";
    const NORMS = [
        '128x128' => ['width' => 128, 'height' => 128, 'is_active' => true, 'is_removed' => false],
        '256x256' => ['width' => 256, 'height' => 256, 'is_active' => true, 'is_removed' => false],
        '400x300' => ['width' => 400, 'height' => 300, 'is_active' => true, 'is_removed' => false],
        '512x512' => ['width' => 512, 'height' => 512, 'is_active' => true, 'is_removed' => false],
        '1024x1024' => ['width' => 1024, 'height' => 1024, 'is_active' => true, 'is_removed' => false],
    ];
    const NORM_1024X1024 = ['width' => 1024, 'height' => 1024];
    const NORM_512X512 = ['width' => 512, 'height' => 512];
    const NORM_400X300 = ['width' => 400, 'height' => 400];
    const NORM_256X256 = ['width' => 256, 'height' => 256];
    const NORM_128X128 = ['width' => 128, 'height' => 128];
    const NORM_NAME = ['128_128', '256_256', '400_300','512_512', '1024_1024'];
    public $primaryKey = 'id';

    public static function getIdByNorm($norm) {
        $norm = self::where('width', $norm['width'])
            ->where(self::CONDITION)
            ->orderBy(self::CREATED_AT, 'desc')
            ->first();
        if(!is_null($norm)) {
            return $norm->id;
        }
        return null;
    }

    public static function getNormString($normId) {
        $norm = self::where(self::ID, $normId)
            ->first();
        return $norm->width.'_'.$norm->height;
    }

}