<?php

namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\SystemImageNorm;

class ImageNormsController extends Controller {

    public $error;
    public function __construct() {
        $this->error = new Error();
    }

    public function getNormsByImageId($image_id) {

        $rows = SystemImageNorm::with('norm')
            ->where([IekModel::SYSTEM_IMAGE_ID => $image_id])
            ->get();
        if ($rows->count() == 0) {
            return response()->json($this->error->setError(Errors::FAILED));
        } else {
            $this->error->setError(Errors::OK);
            $data = new \stdClass();
            $urls = [];
            foreach ($rows as $row) {
                $norm = $row->norm;
                $uri = new \stdClass();
                $uri->name = $norm->width . "_" . $norm->height;
                $uri->width = $row->width;
                $uri->height = $row->height;
                if($row->is_removed) {
                    $row->is_forbidden = $row->is_removed;
                    unset($row->uri);
                } else {
                    $row->is_forbidden = false;
                }
                $uri->uri = $row->uri;
                array_push($urls, $uri);
            }
            $data->urls = $urls;
            $this->error->data = $data;
            return response()->json($this->error->setError(Errors::OK));
        }
    }

    /*public function getNormsByPublicationId($publicationId) {
        $rows = PublicationImage::where(['id' => $publicationId])
            ->where(IekModel::CONDITION);
        if ($rows->count() == 0) {
            return response()->json($this->error->setError(Errors::FAILED));
        } else {
            return $this->getNormsByImageId($rows->first()->image_id);
        }
    }*/

}
