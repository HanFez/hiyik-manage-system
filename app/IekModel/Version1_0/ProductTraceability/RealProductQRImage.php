<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/20/17
 * Time: 3:28 PM
 */

namespace App\IekModel\Version1_0\ProductTraceability;


class RealProductQRImage extends IekProductTraceabilityModel
{
    protected $table="tblRealProductQRImages";

    public function QRImage() {
        return $this->hasOne(self::$NAME_SPACE.'\QRImage', self::ID, 'image_id')
            ->where(self::CONDITION);
    }

}