<?php

/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-5-19
 * Time: 上午11:59
 */

namespace App\Http\Controllers;

use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\Constants\Errors;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Imagick;
use ImagickException;

trait TraitImageSave {

    public static $ImageFolder = 'files';
    public static $NativeFolder = 'natives';
    public static $AvatarNative = 'avatarNatives';
    public static $CoverFolder = 'covers';
    public static $AvatarFolder = 'avatars';
    public static $SystemFolder = 'systems';
    public static $NormFolder = 'norms';
    public static $UploadFile = 'fileName';
    public static $HashType = 'md5';
    public static $Quality = 80;
    public static $Sharpen = 5.0;
    public static $Sigma = 1.0;
    public static $Percent = 20;
    public static $Base = 100;
    public static $ImageFormats = [
        'JPEG' => 'jpeg',
        'JPG' => 'jpg',
        //'PNG' => 'png',
        'BMP' => 'bmp',
        'GIF' => 'gif',
        'ICO' => 'ico',
        'PSD' => 'psd',
//        'TIFF' => 'tiff',
    ];

    public static $MaxFileSize = 50 * 1024 * 1024;
    public function getUploadFile(Request $request) {
        $err = new Error();
        $md5 = $this->getRequestParam($request, self::$HashType);
        if($request->hasFile('fileName')) {
            if($request->file('fileName')->isValid()) {
                $data = new \stdClass();
                $data->name = $request->file(self::$UploadFile)->getClientOriginalName();
                $data->extension = $request->file(self::$UploadFile)->getClientOriginalExtension();
                $data->md5 = $md5;
                $realPath = $request->file(self::$UploadFile)->getRealPath();
                $data->length = $request->file(self::$UploadFile)->getClientSize();
                if($data->length > self::$MaxFileSize) {
                    $err->setError(Errors::FILE_TOO_HUGE);
                    return $err;
                }
                $data->content = file_get_contents($realPath);
                try {
                    $img = new Imagick();
                    $img->readImageBlob($data->content);
                } catch (ImagickException $ex) {
                    unset($data->content);
                    unset($img);
                    $err->setError(Errors::UNKNOWN_IMAGE);
                    return $err;
                }
                if(is_null($data->md5)) {
                    $data->md5 = hash(self::$HashType, $data->content);
                }
                $err->data = $data;
            } else {
                $err->setError(Errors::INVALID_FILE);
            }
        } else {
            $err->setError(Errors::LACK_PARAMS);
        }
        return $err;
    }
    public function saveImageFile(Error $params,$folder = null) {
        $status = new Error();
        if (is_null($folder)) {
            $status->setError(Errors::UNKNOWN_LOCATION);
            return $status;
        }
        if(!$params->isOk() && !$params->isExist()) {
            if(!is_null($params->data) && !is_null($params->data->content)) {
                unset($params->data->content);
            }
            return $params;
        }
        $fileParams = $params->data;
        $md5 = $fileParams->md5;
        if(is_null($md5)) {
            $md5 = hash(self::$HashType, $fileParams->content);
        }
        $ext = strtolower($fileParams->extension);
        if($this->isEndWith($folder,'/')) {
            $destFile = $folder . $md5 . '.' . $ext;
        } else {
            $destFile = $folder . '/' . $md5 . '.' . $ext;
        }
        //We should decide the file type, then run below??????????????????
        if (!is_null($ext) && in_array($ext, self::$ImageFormats)) {
            $img = null;
            try {
                $img = new Imagick();
                $img->readImageBlob($fileParams->content);
                $width = $img->getImageWidth();
                $height = $img->getImageHeight();
            } catch (\Exception $ex) {
                Log::info('image exception: '.$ex->getMessage());
                Log::info('Trace: '.$ex->getTraceAsString());
                $status->setError(Errors::UNKNOWN_IMAGE);
                return $status;
            }
            if (!Storage::exists($destFile)) {
                Storage::put($destFile, $fileParams->content);
            } else {
                $status->setError(Errors::EXIST);
            }
            $native = new \stdClass();
            $native->width = $width;
            $native->height = $height;
            $native->uri = $destFile;
            $native->md5 = $md5;
            $native->name = $fileParams->name;
            $native->length = $fileParams->length;
            $native->content = $img->getImageBlob();
            $native->extension = $ext;
            $status->data = $native;
            if(!is_null($img)) {
                $img->destroy();
            }
        } else {
            $status->setError(Errors::UNKNOWN_IMAGE);
        }
        unset($fileParams->content);
        return $status;
    }
    public function isEndWith($folder, $end) {
        if(!is_null($folder) && !is_null($end)) {
            if (strrpos($folder, $end) == (strlen($folder) - strlen($end))) {
                return true;
            }
        }
        return false;
    }
    public function saveImageNorm($imageContent, $norm, $sharpen, $folder) {
        $err = new Error();
//        if(Storage::exists($uri)) {
            $imagick = new Imagick();
            if($imagick->readImageBlob($imageContent/*Storage::get($uri)*/)) {
                $width = $imagick->getImageWidth();
                $height = $imagick->getImageHeight();
                if($width > $height) {
                    $imagick->thumbnailImage(0, ($norm->height > $height) ? $height : $norm->height);
                } else {
                    $imagick->thumbnailImage(($norm->width > $width) ? $width : $norm->width, 0);
                }
                if($sharpen) {
//                    $imagick->sharpenImage(self::$Sharpen, self::$Sigma);
//                    $imagick->modulateImage(self::$Base, self::$Base + self::$Percent, self::$Base);
                }
                $ext = self::$ImageFormats['JPG'];
                $imagick->setImageFormat($ext);
                $quality = $norm->quality;
                if(is_null($quality) || !isset($quality)) {
                    $quality = self::$Quality;
                }
                $imagick->setImageCompressionQuality($quality);
                $normMd5 = hash(self::$HashType, $imagick->getImageBlob());
                $tmp = new \stdClass();
                $tmp->name = $normMd5.'.'.$ext;
                $tmp->extension = $ext;
                $tmp->md5 = $normMd5;
                $tmp->content = $imagick->getImageBlob();
                $tmp->length = $imagick->getImageLength();
                $status = new Error();
                $status->data = $tmp;
                $err = $this->saveImageFile($status, $folder);
                if(!is_null($imagick)) {
                    $imagick->destroy();
                }
            }
//        } else {
//            $err->setError(Errors::NOT_FOUND);
//        }
        return $err;
    }

    public function saveImageCropFile($uri, $x, $y, $w, $h, $folder) {
        $err = new Error();
        //First crop cover.
        if(Storage::exists($uri)) {
            $imagick = new Imagick();
            $imagick->readImageBlob(Storage::get($uri));
            $imagick->cropImage($w, $h, $x, $y);
            $imagick->setImagePage(0,0,0,0);
            $ext = self::$ImageFormats['JPG'];
            $imagick->setImageFormat($ext);
            $quality = self::$Quality;
            $imagick->setImageCompressionQuality($quality);
            $md5 = hash(self::$HashType, $imagick->getImageBlob());
            //Second save croped image file.
            $tmp = new \stdClass();
            $tmp->name = $md5.'.'.$ext;
            $tmp->extension = $ext;
            $tmp->md5 = $md5;
            $tmp->content = $imagick->getImageBlob();
            $tmp->length = $imagick->getImageLength();
            $err->data = $tmp;
            $err = $this->saveImageFile($err, $folder);
            if(!is_null($imagick)) {
                $imagick->clear();
                $imagick->destroy();
            }
        } else {
            $err->setError(Errors::FILE_NOT_FOUND);
            $err->data = $uri;
        }
        return $err;
    }
    /**
     * @param Request $request
     * @param string $dir
     * @return Error
     */
    public function saveImage(Request $request, $dir) {
        $params = $this->getUploadFile($request);
        if($params->isOk()) {
            $status = $this->saveImageFile($params->data, $dir);
            return $status;
        } else {
            return $params;
        }
    }
    /** To obtain image blob from storage.
     * @param string $path The relative uri path in storage.
     * @return blob|null
     */
    public function getImage($path) {
        if (Storage::exists($path)) {
            return Storage::get($path);
        }
        return null;
    }

}
