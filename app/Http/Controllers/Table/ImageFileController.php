<?php
/**
 * Image file controller to save image into storage.
 * This controller just handles files and systems and norms data relation.
 * Folder structure like below:
 *  files
 *  |---systems
 *  |---norms
 *      |----1024x1024
 *      |----512x512
 *      |----400x300
 *      |----256x256
 *      |----128x128
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\Http\Controllers\TraitImageRelation;
use App\Http\Controllers\TraitRequestParams;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Norm;
use App\IekModel\Version1_0\SystemImage;
use App\IekModel\Version1_0\SystemImageNorm;
use Illuminate\Http\Request;
use App\Http\Controllers\TraitImageSave;

class ImageFileController extends Controller {
    use TraitImageSave;
    use TraitImageRelation;
    use TraitRequestParams;

    public function handleImageFile(Request $request) {
        $dir = self::$ImageFolder.'/'.self::$SystemFolder;
        $params = $this->getUploadFile($request);
        if(!($params->isOk() || $params->isExist())) {
            return response()->json($params);
        }
        $result = $this->saveImageFile($params, $dir);
        $err = $this->saveImageRelation($result);
        return response()->json($err);
    }

    public function handleImageFileSave(Request $request) {
        $dir = static::$ImageFolder.'/'.static::$SystemFolder;
        $params = $this->getUploadFile($request);
        if(!($params->isOk() || $params->isExist())) {
            return $params;
        }
        $result = $this->saveImageFile($params, $dir);
        $err = $this->saveImageRelation($result);
        return $err;
    }

    public function handleCropImage(Request $request, $imageId) {
        $err = new Error();
        $folder = self::$ImageFolder.'/'.self::$CoverFolder;
        $params = $this->getPostParams($request);
        if(!$params->isOk()) {
            return response()->json($err);
        }
        $data = $params->data;
        $isForbidden = false;
        $norm = Norm::where(IekModel::WIDTH, 1024)
            ->where(IekModel::ACTIVE, true)
            ->where(IekModel::REMOVED, false)
            ->orderBy(IekModel::UPDATED_AT, 'desc')
            ->first();
        $imageNorm = SystemImageNorm::where(IekModel::IID, $imageId)
            ->where(IekModel::NORM_ID, $norm->id)
            ->where(IekModel::ACTIVE, true)
            ->where(IekModel::REMOVED, false)
            ->orderBy(IekModel::UPDATED_AT, 'desc')
            ->first();
        if(is_null($imageNorm)) {
            $imageNorm = SystemImage::where(IekModel::ID, $imageId)
//                ->where(IekModel::ACTIVE, true)
//                ->where(IekModel::REMOVED, false)
                ->orderBy(IekModel::UPDATED_AT, 'desc')
                ->first();
        } else {
            $isForbidden = SystemImage::isForbidden($imageId);
        }
        if(is_null($imageNorm)) {
            $err->setError(Errors::NOT_FOUND);
            return response()->json($err);
        }
        try {
            $status = $this->saveImageCropFile($imageNorm->uri, $data->x, $data->y, $data->w, $data->h, $folder);
            $status->data->is_removed = $isForbidden;
            $err = $this->saveImageRelation($status);
        } catch (\Exception $ex) {
            $err->exception($ex);
        }
        return response()->json($err);
    }
    public function getPostParams(Request $request) {
        $err = new Error();
        $param = new \stdClass();
        //The target is identify 'avatar' or 'cover'
        $param->target = $this->getRequestParam($request, 'target');
        $param->x = $this->getRequestParam($request, 'x');
        $param->y = $this->getRequestParam($request, 'y');
        $param->w = $this->getRequestParam($request, 'w');
        $param->h = $this->getRequestParam($request, 'h');
        $err->data = $param;
        if(is_null($param->x)
            || is_null($param->y)
            || is_null($param->w)
            || is_null($param->h)
            ) {
            $err->setError(Errors::LACK_PARAMS);
        }
        return $err;
    }

    public function checkImageByHash() {
        $hash = $this->getRequestParam(request(), IekModel::HASH);
        $result = $this->getImageByHash($hash);
        return response()->json($result);
    }
}