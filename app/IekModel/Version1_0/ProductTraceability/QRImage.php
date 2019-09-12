<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/20/17
 * Time: 3:20 PM
 */

namespace App\IekModel\Version1_0\ProductTraceability;


class QRImage extends IekProductTraceabilityModel
{
    protected $table="tblQRImages";

    public static function createRecord($params){
        $qr = new self();
        $qr->file_name = $params['file_name'];
        $qr->extension = $params['extension'];
        $qr->width = $params['width'];
        $qr->height = $params['height'];
        $qr->md5 = $params['md5'];
        $qr->uri = $params['uri'];
        $qr->length = $params['length'];
        $qr->save();
        return $qr;
    }

}