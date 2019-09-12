<?php

/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-3-16
 * Time: 上午9:25
 */

namespace App\Http\Controllers\Table;

use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ImageNorm;
use App\IekModel\Version1_0\Images;
use App\IekModel\Version1_0\PersonImage;
use App\IekModel\Version1_0\PublicationImage;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\Constants\Errors;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\IekModel\Version1_0\Norm;
use App\IekModel\Version1_0\ImageTone;
use Libs\Robot\HslParser;
use Libs\Robot\ToneParser;
use App\IekModel\Version1_0\PersonAccount;
use App\IekModel\Version1_0\PersonAvatar;
use App\IekModel\Version1_0\Avatar;
use App\IekModel\Version1_0\AvatarNorm;
use App\IekModel\Version1_0\AvatarTone;

class FileController extends Controller {
    public static $ImageFolder = 'files';
    public static $NativeFolder = 'natives';
    public static $AvatarNative = 'avatarNatives';
    public static $CoverFolder = 'covers';
    public static $AvatarFolder = 'avatars';
    public static $NormFolder = 'norms';
    public static $SystemFolder = 'systems';
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
        'TIFF' => 'tiff',
    ];

//    use TraitImageSave;
//    use TraitRequestParams;

    /**
     * To save upload file.
     * @param Request $request
     * @param String $subdir The sub-dir in files. subdir should be 'natives', 'covers', 'avatars' currently.
     * @return \Illuminate\Http\JsonResponse
     */
//    public function saveFile(Request $request, $subdir = null) {
//
//        $err = new Error();
//        $pid = $this->getRequestParam($request, 'publicationId');
//        $uid = $this->getRequestParam($request, 'personId');
//        $act = $this->getRequestParam($request, 'action');
//        $canSell = $this->getRequestParam($request, 'canSell');
//        if(is_null($canSell)) {
//            $canSell = false;
//        }
//        try {
//            if ($subdir == 'covers') {
//                $relationId = $this->getRequestParam($request, 'nativeImageId');
//                $oldRelativeId = $this->getRequestParam($request, 'publicationImageId');
//                $x = $this->getRequestParam($request, 'x');
//                $y = $this->getRequestParam($request, 'y');
//                $w = $this->getRequestParam($request, 'w');
//                $h = $this->getRequestParam($request, 'h');
//                $coverFolder = self::$ImageFolder.'/'.$subdir;
//                //***************no relation id when create.
//                $status = $this->saveCover($relationId, $x, $y, $w, $h, $coverFolder);
//                if ($act == 'update') {
//                    $result = $this->deleteRelationImage(new PublicationImage(), $oldRelativeId);
//                    if(!$result) {
//                        $err->setError(Errors::FAILED);
//                        return response()->json($err);
//                    }
//                }
//                $err = $this->savePublicationRelations($pid, $status, true, false);
//            } else if ($subdir == 'avatars') {
//                //TODO: to save person avatars
//                $relativeId = $this->getRequestParam($request, 'nativeImageId');
//                $oldRelativeId = $this->getRequestParam($request, 'personAvatarId');
//                $x = $this->getRequestParam($request, 'x');
//                $y = $this->getRequestParam($request, 'y');
//                $w = $this->getRequestParam($request, 'w');
//                $h = $this->getRequestParam($request, 'h');
//                $avatarFolder = self::$ImageFolder.'/'.$subdir;
//                $status = $this->saveAvatar($relativeId, $x, $y, $w, $h, $avatarFolder);
//                if ($act == 'update') {
//                    $result = $this->deleteRelationImage(new PersonAvatar(), $oldRelativeId);
//                    if(!$result) {
//                        $err->setError(Errors::FAILED);
//                        return response()->json($err);
//                    }
//                }
//                $err = $this->savePersonAvatar($uid, $status);
//            } else {
//                if ($act == 'delete' || $act == 'update') {
//                    $oldRelationId = $this->getRequestParam($request, 'publicationImageId');
//                    $result = $this->deleteRelationImage(new PublicationImage(), $oldRelationId);
//                    if(!$result) {
//                        $err->setError(Errors::FAILED);
//                        return response()->json($err);
//                    }
//                    if ($act == 'delete') {
//                        return response()->json($err);
//                    }
//                }
//                $folder = null;
//                if (is_null($folder)) {
//                    $folder = self::$ImageFolder . '/' . self::$NativeFolder;
//                }
//                $status = $this->saveImage($request, $folder);
//                $err = $this->savePublicationRelations($pid, $status, false, $canSell);
//            }
//        } catch (\Exception $ex) {
//            $err->exception($ex);
//        }
//        return response()->json($err);
//    }

    /**
     * To get sub-folder image resource.
     * @param $dir
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function subImage($dir, $id) {
        $ext = pathinfo($id, PATHINFO_EXTENSION);
        $path = self::$ImageFolder . '/' . $dir . '/' . $id;
        $img = $this->getImage($path);
        if (is_null($img)) {
            $img = new Error();
            $img->setError(Errors::NOT_FOUND);
            return response()->json($img);
        }
        return response($img)->header('Content-Type', 'image/jpeg');
    }

    /**
     * To get root folder image resource.
     * @param $id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\JsonResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function imageFile($id) {
        $ext = pathinfo($id, PATHINFO_EXTENSION);
        $path = self::$ImageFolder . '/' . $id;
        $img = $this->getImage($path);
        if (is_null($img)) {
            $img = new Error();
            $img->setError(Errors::NOT_FOUND);
            return response()->json($img);
        }
        return response($img)->header('Content-Type', 'image/jpeg');
    }

    /**
     * To get sub-folder norm image resource.
     * @param $subdir String
     * @param $norm String
     * @param $id String Image file name
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    public function normImage($products = null, $subdir = null, $norm = null, $id = null) {
        $ext = pathinfo($id, PATHINFO_EXTENSION);
        $path = self::$ImageFolder;
        if (!is_null($products) && is_string($products) && !empty($products)) {
            $path = $path . '/' . $products;
            $fileType = explode('.',$products);
        }
        if (!is_null($subdir) && is_string($subdir) && !empty($subdir)) {
            $path = $path . '/' . $subdir;
            $fileType = explode('.',$subdir);
        }
        if (!is_null($norm) && is_string($norm) && !empty($norm)) {
            $path = $path . '/' . $norm;
            $fileType = explode('.',$norm);
        }
        if (!is_null($id) && is_string($id) && !empty($id)) {
            $path = $path . '/' . $id;
            $fileType = explode('.',$id);
        }
        if(str_contains($path, '/native/')) {
            $err = new Error();
            $err->setError(Errors::NOT_FOUND);
            return response()->json($err);
        }
        $img = $this->getImage($path);
        if (is_null($img)) {
            $img = new Error();
            $img->setError(Errors::NOT_FOUND);
            return response()->json($img);
        }
        if($fileType[1] == 'svg'){
            return response($img)->header('Content-Type', 'text/xml');
        }else{
            return response($img)->header('Content-Type', 'image/jpeg');
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

    /** To return define of image formats we supported currently.
     * @return \Illuminate\Http\JsonResponse
     */
    public function getSupportedImageFormats() {
        $err = new Error();
        $err->{IekModel::DATA} = array_values(self::$ImageFormats);
        return response()->json($err);
    }

    /** To save publication relations, include relation Image, PersonImage, PublicationImage, ImageNorm, ImageTone.
     * @param integer $pid The publication id.
     * @param \stdClass $status The information of a image belong to publication.
     * @return Error
     */
    public function savePublicationRelations($pid, $status, $isCover, $canSell) {
        $err = new Error();
        if ($status->isOk() || $status->isExist()) {
            if (isset($status->data)) {
                $imageId = $this->insertImage(new Images(), $status->data);
                if (Auth::user()) {
                    $personId = PersonAccount::getPersonId(Auth::user()->id);
                    if(!is_null($personId)) {
                        $this->insertPersonImage($personId, $imageId);
                    }
                }
                $pubImage = $this->insertRelationImage(new PublicationImage(), $pid, $imageId, $isCover, $canSell);
                $norms = $this->makeImageNorms(new ImageNorm(), $imageId, $status->data->content);
                $tones = $this->makeImageTone($status->data->content);
                $rec = $this->insertImageTone(new ImageTone(), $imageId, $tones);
                $data = new \stdClass();
                $data->publication_image_id = $pubImage->id;
                $data->publication_id = $pid;
                $data->urls = $norms->data;
                $err->data = $data;
            }

        } else {
            unset($status->content);
            $err = $status;
        }
        return $err;
    }

    /** To save person relations, include relation Image, PersonImage, PersonAvatar, ImageNorm, ImageTone.
     * @param integer $uid The person id.
     * @param Error $status The information of a image belong to person.
     * @return Error
     */
    public function savePersonAvatar($uid, Error $status) {
        $err = new Error();
        if ($status->isOk() || $status->isExist()) {
            if (isset($status->data)) {
                $imageId = $this->insertImage(new Avatar(), $status->data);
                if (Auth::user()) {
                    $personId = PersonAccount::getPersonId(Auth::user()->id);
                    $this->insertPersonImage($personId, $imageId);
                }
                $personAvatar = $this->insertRelationImage(new PersonAvatar(), $uid, $imageId);
                $norms = $this->makeImageNorms(new AvatarNorm(), $imageId, $status->data->content);
                $tones = $this->makeImageTone($status->data->content);
                $rec = $this->insertImageTone(new AvatarTone(), $imageId, $tones);
                $data = new \stdClass();
                $data->publication_image_id = $personAvatar->id;
                $data->person_id = $uid;
                $data->urls = $norms->data;
                $err->data = $data;
            }

        } else {
            unset($status->content);
            $err = $status;
        }
        return $err;
    }
    /** To make the norms of image, then save norm image file and insert record into data table.
     * @param IekModel $model The norm relation about image, just be AvatarNorm and ImageNorm currently.
     * @param integer $imageId The native image id.
     * @param blob $imageContent The content of native image.
     * @return Error
     */
    public function makeImageNorms(IekModel $model, $imageId, &$imageContent) {
        $err = new Error();
        $content = $imageContent;
        $normList = [];
        $norms = Norm::where(Norm::ACTIVE, true)
                ->where(Norm::REMOVED, false)
                ->orderBy('width', 'desc')
                ->get();
        $folder = self::$ImageFolder.'/'.self::$NormFolder;
        foreach ($norms as $norm) {
            if($norm->width == Norm::NORMS['1024x1024']['width']) {
                $sharpen = false;
            } else {
                $sharpen = true;
            }
            if($this->isEndWith($folder, '/')) {
                $subFolder = $folder.$norm->width.'_'.$norm->height;
            } else {
                $subFolder = $folder.'/'.$norm->width.'_'.$norm->height;
            }
            $status = $this->saveImageNorm($content, $norm, $sharpen, $subFolder);
            if(!$status->isOk() && $status->isExist()) {
                return $status;
            } else {
                unset($content);
                $content = $status->data->content;
            }
            $temp = $this->insertImageNorm($model, $imageId, $norm->id, $status->data);
            if(!is_null($temp)) {
                $tmpNorm = new \stdClass();
                $tmpNorm->name = $norm->width.'_'.$norm->height;
                $tmpNorm->md5 = $temp->md5;
                $tmpNorm->uri = $temp->uri;
                $tmpNorm->width = $temp->width;
                $tmpNorm->height = $temp->height;
                $tmpNorm->length = $temp->length;
                array_push($normList, $tmpNorm);
            }
        }
        $err->data = $normList;
        return $err;
    }

    /** To insert a record into relation ImageNorm
     * @param IekModel $model The norm relation about image, just be AvatarNorm and ImageNorm currently.
     * @param integer $imageId The image id.
     * @param integer $normId The norm id.
     * @param \stdClass $params The information of image.
     * @return ImageNorm|null
     */
    public function insertImageNorm(IekModel $model, $imageId, $normId, $params) {
        $temp = $model::where('image_id', $imageId)
            ->where('norm_id', $normId)
            ->where('md5', $params->md5)
            ->where($model::ACTIVE, true)
            ->where($model::REMOVED, false)
            ->first();
        if(is_null($temp)) {
            $norm = new $model();
            $norm->image_id = $imageId;
            $norm->norm_id = $normId;
            $norm->uri = $params->path;
            $norm->width = $params->width;
            $norm->height = $params->height;
            $norm->length = $params->length;
            $norm->md5 = $params->md5;
            if($norm->save()) {
                $temp = $norm;
            } else {
                $temp = null;
            }
        }
        return $temp;
    }

    /** To insert a record into relation Image.
     * @param IekModel $model The image model, just be Image and Avatar currently.
     * @param \stdClass $params The information of image.
     * @return mixed
     */
    public function insertImage(IekModel $model, $params) {
        $img = $model::where('md5', $params->md5)
            ->where($model::ACTIVE, true)
            ->where($model::REMOVED, false)
            ->first();
        if(is_null($img)) {
            $data = [
                'file_name' => $params->name,
                'extension' => $params->extension,
                'width' => $params->width,
                'height' => $params->height,
                'md5' => $params->md5,
                $model::ACTIVE => true,
                $model::REMOVED => false,
                'uri' => $params->path,
            ];
            $img = $model::create($data);
        }
        return $img->id;
    }

    /** To insert a record into relation ImageTone.
     * @param IekModel $model The relation model about Image, just be ImageTone and AvatarTone currently.
     * @param integer $imageId The image id.
     * @param array $tones The tones of image.
     * @return ImageTone|null
     */
    public function insertImageTone(IekModel $model, $imageId, $tones) {
        $imgTone = $model::where('image_id', $imageId)->first();
        if(is_null($imgTone)) {
            $temp = new $model();
            $temp->image_id = $imageId;
            $temp->tone = json_encode($tones);
            $temp->{$model::ACTIVE} = true;
            $temp->{$model::REMOVED} = false;
            $imgTone = $temp;
        } else if(is_null($imgTone->tone)) {
            $imgTone->tone = json_encode($tones);
        } else {
            return $imgTone;
        }
        if(!$imgTone->save()) {
            $imgTone = null;
        }
        return $imgTone;
    }

    /** To parse tone of image.
     * @param  blob $contents The reference image content.
     * @return array|null The tone information array.
     */
    public function makeImageTone(&$contents) {
        $tones = null;
        $parser = new HslParser();
        $toneParser = new ToneParser($parser);
        if($toneParser->setImageBlob($contents)) {
            $tones = $toneParser->getMainTones();
        }
        return $tones;
    }

    /** To insert a record into relation PublicationImage.
     * @param IekModel $model The relation model about Image.
     * Just be PublicationImage and PersonAvatar currently.
     * @param integer $pubId The publication id.
     * @param integer $imageId The image id.
     * @param bool $isCover Whether be cover.
     * @param bool $canSell Whether can sell.
     * @return PublicationImage|null
     */
    public function insertRelationImage(IekModel $model, $pubId, $imageId, $isCover=false, $canSell=false) {
        if($model instanceof PersonAvatar) {
            $idName = IekModel::UID;
        } else {
            $idName = IekModel::PID;
        }
        $relation = $model::where($idName, $pubId)
            ->where('image_id', $imageId)
            ->where($model::ACTIVE, true)
            ->where($model::REMOVED, false)
            ->first();
        if(is_null($relation)) {
            $new = new $model();
            $new->{$idName} = $pubId;
            $new->image_id = $imageId;
            $new->{$model::ACTIVE} = true;
            $new->{$model::REMOVED} = false;
            if($model instanceof PublicationImage) {
                $new->is_cover = $isCover;
                $new->can_sell = $canSell;
            }
            if($new->save()) {
                $relation = $new;
            } else {
                $relation = null;
            }
        }
        return $relation;
    }

    /** To delete a record of relation PublicationImage.
     * @param IekModel $model The relation model about Image,
     * just be PublicationImage and PersonAvatar currently.
     * @param integer $pubImageId The relation id.
     * @return bool
     */
    public function deleteRelationImage(IekModel $model, $pubImageId) {
        $relation = $model::where($model::ACTIVE, true)
            ->where($model::REMOVED, false)
            ->where($model::ID, $pubImageId)
            ->first();
        if(!is_null($relation)) {
            $relation->{$model::REMOVED} = true;
            return($relation->save());
        } else {
            return true;
        }
    }

    /** To insert a record into relation PersonImage.
     * @param integer $uid The person id.
     * @param integer $imageId The image id.
     * @return PersonImage|null
     */
    public function insertPersonImage($uid, $imageId) {
        $relation = PersonImage::where('person_id', $uid)
                ->where('image_id', $imageId)
                ->where(PersonImage::ACTIVE, true)
                ->where(PersonImage::REMOVED, false)
                ->first();
        if(is_null($relation)) {
            $new = new PersonImage();
            $new->{IekModel::UID} = $uid;
            $new->image_id = $imageId;
            $new->{PersonImage::ACTIVE} = true;
            $new->{PersonImage::REMOVED} = false;
            if($new->save()) {
                $relation = $new;
            } else {
                $relation = null;
            }
        }
        return $relation;
    }

    /**To save the publication cover file according to the crop location.
     * @param integer $relationId  The native image relation id.
     * @param integer $x The left point start to crop.
     * @param integer $y The top point start to crop.
     * @param integer $w The width to crop.
     * @param integer $h The height to crop.
     * @param string $dir The folder location to save crop image file
     * @return Error The result include image file information or error status.
     */
    public function saveCover($relationId, $x, $y, $w, $h, $dir = 'covers') {
        $nativeImage = PublicationImage::where('id', $relationId)->first();
        $status = new Error();
        if(!is_null($nativeImage) && !is_bool($nativeImage)) {
            if(!$nativeImage->can_sell) {
                $nativeImage->can_sell = true;
                $nativeImage->save();
            }
            $normId = Norm::getIdByNorm(Norm::NORM_1024X1024);
            if(!is_null($normId)) {
                $imageNorm = ImageNorm::where('image_id', $nativeImage->image_id)
                    ->where('norm_id', $normId)->first();

                if (!is_null($imageNorm) && !is_bool($imageNorm)) {

                    // Whether we should used the max norm to crop cover coherent???
                    if (!is_null($imageNorm->uri)) {
                        $status = $this->saveImageCropFile($imageNorm->uri, $x, $y, $w, $h, $dir);
                    } else {
                        $status->setError(Errors::NOT_FOUND);
                    }
                } else {
                    $status->setError(Errors::NOT_FOUND);
                }
            } else {
                $status->setError(Errors::NOT_FOUND);
            }
        } else {
            $status->setError(Errors::NOT_FOUND);
        }
        return $status;
    }
    /**To save the person avatar file according to the crop location.
     * @param integer $relationId  The native image relation id.
     * @param integer $x The left point start to crop.
     * @param integer $y The top point start to crop.
     * @param integer $w The width to crop.
     * @param integer $h The height to crop.
     * @param string $dir The folder location to save crop image file
     * @return Error The result include image file information or error status.
     */
    public function saveAvatar($relationId, $x, $y, $w, $h, $dir = 'avatars') {
        $nativeImage = PersonAvatar::where('id', $relationId)->first();
        $status = new Error();
        if(!is_null($nativeImage) && !is_bool($nativeImage)) {
            $normId = Norm::getIdByNorm(Norm::NORM_1024X1024);
            $image = AvatarNorm::where('image_id', $nativeImage->image_id)
                ->where('norm_id', $normId)->first();
            if(!is_null($image) && !is_bool($image)) {
                if(!is_null($image->uri)) {
                    $status = $this->saveImageCropFile($image->uri, $x, $y, $w, $h, $dir);
                }else {
                    $status->setError(Errors::NOT_FOUND);
                }
            } else {
                $status->setError(Errors::NOT_FOUND);
            }
        } else {
            $status->setError(Errors::NOT_FOUND);
        }
        return $status;
    }
}
