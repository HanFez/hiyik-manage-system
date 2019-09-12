<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/5/17
 * Time: 15:35
 */
namespace App\Http\Controllers;

use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Layout;
use App\IekModel\Version1_0\SystemImageNorm;

trait TraitLayout{
    public function getNormsByImageId($image_id) {

        $rows = SystemImageNorm::where([IekModel::SYSTEM_IMAGE_ID => $image_id]);
        if ($rows->count() == 0) {
            return null;
        } else {
            $norms = [];
            foreach ($rows->select("width", "height", "uri")->get() as $key => $row) {
                $norms[] = [
                    'norm' => $row->height . "_" . $row->width,
                    'width' => $row->width,
                    'height' => $row->height,
                    'uri' => $row->uri,
                ];
            }
            return $norms;
        }
    }

    public function getNormsByLayoutId($layoutId) {
        $rows = Layout::where([IekModel::ID => $layoutId]);
        if ($rows->count() == 0) {
            return null;
        } else {
            return $this->getNormsByImageId($rows->first()->image_id);
        }
    }
}