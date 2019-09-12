<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Auth\TraitAuthenticate;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Norm;
use App\IekModel\Version1_0\SystemImage;
use App\IekModel\Version1_0\SystemImageNorm;
use App\IekModel\Version1_0\SystemImageTone;
use Libs\Robot\HslParser;
use Libs\Robot\ToneParser;

Trait TraitImageRelation {
    public static $ImageNormModel = \App\IekModel\Version1_0\SystemImageNorm::class;
    public static $ImageModel = \App\IekModel\Version1_0\SystemImage::class;
    public static $NormModel = \App\IekModel\Version1_0\Norm::class;
    use TraitAuthenticate;
    /** To make the norms of image, then save norm image file and insert record into data table.
     * @param IekModel $model The norm relation about image, just be AvatarNorm and ImageNorm currently.
     * @param integer $imageId The native image id.
     * @param blob $imageContent The content of native image.
     * @return Error
     */
    public function makeImageNorms($model, $imageId, $imageContent) {
        $err = new Error();
        $normList = [];
        $norms = Norm::where(IekModel::ACTIVE, true)
            ->where(IekModel::REMOVED, false)
            ->orderBy(IekModel::WIDTH, 'desc')
            ->get();
        $folder = self::$ImageFolder.'/'.static::$NormFolder;
        foreach ($norms as $norm) {
            if($norm->width == 1024) {
                $sharpen = false;
            } else {
                $sharpen = true;
            }
            if($this->isEndWith($folder, '/')) {
                $subFolder = $folder.$norm->width.'_'.$norm->height;
            } else {
                $subFolder = $folder.'/'.$norm->width.'_'.$norm->height;
            }
            $content = $imageContent;
            $status = $this->saveImageNorm($content, $norm, $sharpen, $subFolder);
            if(!$status->isOk() && !$status->isExist()) {
//                unset($status->data->content);
                return $status;
            } else {
                unset($content);
                //$content = $status->data->content;
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
    public function insertImageNorm($model, $imageId, $normId, $params) {
        $temp = $model::where(IekModel::IID, $imageId)
            ->where(IekModel::NORM_ID, $normId)
            ->where(IekModel::HASH_MD5, $params->md5)
            ->where($model::ACTIVE, true)
            ->where($model::REMOVED, false)
            ->first();
        if(is_null($temp)) {
            $norm = new $model();
            $norm->{IekModel::IID} = $imageId;
            $norm->norm_id = $normId;
            $norm->uri = $params->uri;
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
        $img = $model::where(IekModel::HASH_MD5, $params->md5)
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
                'uri' => $params->uri,
            ];
            $img = $model::create($data);
        }
        return $img->id;
    }
    public function insertImageRelation($model, $data) {
        $image = new $model;
        $image->uri = $data->uri;
        $image->length = $data->length;
        $image->width = $data->width;
        $image->height = $data->height;
        $image->md5 = $data->md5;
        $image->extension = $data->extension;
        $image->file_name = $data->name;
        $image->is_removed = property_exists($data, IekModel::REMOVED) ? $data->is_removed : false;
        $image->save();
        return $image;
    }

    /** To insert a record into relation PersonImage
     * @param $imageId
     */
//    public function insertPersonImage($imageId) {
//        $personId = $this->getLoginPersonId();
//        if (!is_null($personId)) {
//            if(!is_null($personId) && !is_null($imageId)) {
//                $pi = PersonImage::where(IekModel::CONDITION)
//                    ->where(IekModel::UID, $personId)
//                    ->where(IekModel::IID, $imageId)
//                    ->first();
//                if(is_null($pi)) {
//                    $pi->image_id = $imageId;
//                    $pi->person_id = $personId;
//                    $pi->save();
//                }
//            }
//        }
//    }
    /** To insert a record into relation ImageTone.
     * @param IekModel $model The relation model about Image, just be ImageTone and AvatarTone currently.
     * @param integer $imageId The image id.
     * @param array $tones The tones of image.
     * @return ImageTone|null
     */
    public function insertImageTone(IekModel $model, $imageId, $tones) {
        $imgTone = $model::where(IekModel::SYSTEM_IMAGE_ID, $imageId)->first();
        if(is_null($imgTone)) {
            $temp = new $model();
            $temp->system_image_id = $imageId;
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

    /** To save publication image and cover relations.
     * @param $params
     * @return Error
     */
    public function saveImageRelation($params) {
        $err = new Error();
        if($params->isOk() || $params->isExist()) {
            $md5 = $params->data->md5;
            $image = null;
            $norms = null;
            if(!SystemImage::isHashExist($md5)) {
                $data = $params->data;
                $image = $this->insertImageRelation(static::$ImageModel,$data);
                $norms = $this->makeImageNorms(new static::$ImageNormModel(), $image->id, $data->content);
                $norms = $norms->data;
                if(!isset(static::$IsTone) || static::$IsTone){
                    $tones = $this->makeImageTone($data->content);
                    $row = $this->insertImageTone(new SystemImageTone(), $image->id, $tones);
                }
            } else {
                $image = SystemImage::getImageByHash($md5);
                //dd($image);
            }
            if(!is_null($image)) {
                if(is_null($norms)) {
                    if (SystemImageNorm::isImageExists($image->id)) {
                        $norms = $image->norms()
                            ->where(IekModel::CONDITION)
                            ->get()->each(function($item, $key) {
                                $norm = $item->norm;
                                unset($item->norm);
                                $item->name = $norm->width.'_'.$norm->height;
                            });
                    } else {
                        $content = $this->getImage($image->uri);
                        if (!is_null($content)) {
                            $norms = $this->makeImageNorms(new static::$ImageNormModel(), $image->id, $content);
                            $norms = $norms->data;
                            unset($content);
                        } else {
                            $norms = null;
                        }
                    }
                }
                $image->norms = $norms;
                $err->data = $image;
                if(!isset(static::$IsTone) || static::$IsTone) {
                    if(!SystemImageTone::hasImageTone($image->id)) {
                        $content = $this->getImage($image->uri);
                        if(!is_null($content)) {
                            $tones = $this->makeImageTone($content);
                            $row = $this->insertImageTone(new SystemImageTone(), $image->id, $tones);
                            unset($content);
                            unset($row);
                        }
                    }
                }
            } else {
                $err->setError(Errors::NOT_FOUND);
            }
        } else {
            $err->setError(Errors::FAILED);
        }
        return $err;
    }

    /** To check image file according to hash value.
     *  And check the norms and tone of image, if not exist, we make them.
     * @param $hash
     * @return Error
     */
    public function  getImageByHash($hash) {
        $err = new Error();
        $md5 = $hash;
        $image = null;
        if(SystemImage::isHashExist($md5)) {
            $image = SystemImage::getImageByHash($md5);
        }
        if(!is_null($image)) {
            if (SystemImageNorm::isImageExists($image->id)) {
                $norms = $image->norms()
                    ->where(IekModel::CONDITION)
                    ->get()->each(function($item, $key) {
                        $norm = $item->norm;
                        unset($item->norm);
                        $item->name = $norm->width.'_'.$norm->height;
                    });
            } else {
                $content = $this->getImage($image->uri);
                if (!is_null($content)) {
                    $norms = $this->makeImageNorms(new static::$ImageNormModel(), $image->id, $content);
                    $norms = $norms->data;
                } else {
                    $norms = null;
                }
            }
            $image->norms = $norms;
            $err->data = $image;
            $err->setError(Errors::EXIST);
            if(!SystemImageTone::hasImageTone($image->id)) {
                $content = $this->getImage($image->uri);
                if(!is_null($content)) {
                    $tones = $this->makeImageTone($content);
                    $row = $this->insertImageTone(new SystemImageTone(), $image->id, $tones);
                    unset($content);
                    unset($row);
                }
            }
        } else {
            $err->setError(Errors::FILE_NOT_FOUND);
        }
        return $err;
    }
    /** To check avatar file according to hash value.
     *  And check the norms and tone of image, if not exist, we make them.
     * @param $hash
     * @return Error
     */
    public function  getAvatarByHash($hash) {
        $err = new Error();
        $md5 = $hash;
        $image = null;
        if(Avatar::isHashExist($md5)) {
            $image = Avatar::getImageByHash($md5);
        }
        if(!is_null($image)) {
            if (AvatarNorm::isImageExists($image->id)) {
                $norms = $image->norms()
                    ->where(Avatar::CONDITION)
                    ->get()->each(function($item, $key) {
                        $norm = $item->norm;
                        unset($item->norm);
                        $item->name = $norm->width.'_'.$norm->height;
                    });
            } else {
                $content = $this->getImage($image->uri);
                if (!is_null($content)) {
                    $norms = $this->makeImageNorms(new AvatarNorm(), $image->id, $content);
                    $norms = $norms->data;
                    unset($norms->content);
                } else {
                    $norms = null;
                }
            }
            $image->norms = $norms;
            $err->data = $image;
            $err->setError(Errors::EXIST);
        } else {
            $err->setError(Errors::FILE_NOT_FOUND);
        }
        return $err;
    }
    /** To save publication image and cover relations.
     * @param $params
     * @return Error
     */
    public function saveAvatarRelation($params) {
        $err = new Error();
        if($params->isOk() || $params->isExist()) {
            $md5 = $params->data->md5;
            $image = null;
            $norms = null;
            if(!Avatar::isHashExist($md5)) {

                $data = $params->data;
                $image = $this->insertImageRelation(Avatar::class,$data);
                $norms = $this->makeImageNorms(new AvatarNorm(), $image->id, $data->content);
                $norms = $norms->data;
            } else {
                $image = Avatar::getImageByHash($md5);
            }
            if(!is_null($image)) {
                if(is_null($norms)) {
                    if (AvatarNorm::isImageExists($image->id)) {
                        $norms = $image->norms()
                            ->where(Avatar::CONDITION)
                            ->get()->each(function($item, $key) {
                                $norm = $item->norm;
                                unset($item->norm);
                                $item->name = $norm->width.'_'.$norm->height;
                            });
                    } else {
                        $content = $this->getImage($image->uri);
                        if (!is_null($content)) {
                            $norms = $this->makeImageNorms(new AvatarNorm(), $image->id, $content);
                            $norms = $norms->data;
                            unset($content);
                        } else {
                            $norms = null;
                        }
                    }
                }
                $image->norms = $norms;
                $err->data = $image;
            } else {
                $err->setError(Errors::NOT_FOUND);
            }
        } else {
            $err->setError(Errors::FAILED);
        }
        return $err;
    }

    /** To insert a record into relation PersonImage.
     * @param integer $uid The person id.
     * @param integer $imageId The image id.
     * @return PersonImage|null
     */
    public function insertPersonImage($uid, $imageId) {
        $relation = PersonImage::where(IekModel::UID, $uid)
            ->where(IekModel::IID, $imageId)
            ->where(PersonImage::CONDITION)
            ->first();
        if(is_null($relation)) {
            $new = new PersonImage();
            $new->{PersonImage::UID} = $uid;
            $new->{PersonImage::IID} = $imageId;
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
}