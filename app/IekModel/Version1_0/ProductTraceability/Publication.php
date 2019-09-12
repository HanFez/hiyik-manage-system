<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/12/17
 * Time: 5:20 PM
 */

namespace App\IekModel\Version1_0\ProductTraceability;


use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;

class Publication extends IekProductTraceabilityModel
{
    protected $table="tblPublications";

    public function core() {
        return $this->hasMany(self::$NAME_SPACE.'\Core', 'publication_id', self::ID)
            ->where(self::CONDITION);
    }

    public function author() {
        return $this->hasOne(self::$NAME_SPACE.'\Author', 'no', 'author_no')
            ->where(self::CONDITION);
    }

    public function museum() {
        return $this->hasOne(self::$NAME_SPACE.'\Museum', self::ID, 'museum_id')
            ->where(self::CONDITION);
    }

    public function publicationIntroduction() {
        return $this->hasMany(self::$NAME_SPACE.'\PublicationIntroduction', 'publication_id', self::ID)
            ->where(self::CONDITION);
    }

    public static function createPub($params){
        $err = new Error();
        $exist = self::getPublicationByNo($params['no']);
        if(!is_null($exist)){
            $err->setError(Errors::EXIST);
            return $err;
        }
        $err->setData(self::createRecord($params));
        return $err;
    }

    public static function modifyPub($params,$id){
        $err = new Error();
        $pub = self::getPublicationById($id);
        if(is_null($pub)){
            $err->setError(Errors::NOT_FOUND);
            return $err;
        }
        if($pub->no != $params['no']){
//            $noPub = self::getPublicationByNo($params['no']);
//            if(!is_null($noPub)){
                $err->setError(Errors::NOT_ALLOWED);
                $err->setData('no');
                return $err;
//            }
        }
        $err->setData(self::createRecord($params,$id));
        return $err;
    }

    public static function createRecord($params,$id=null){
        if(!is_null($id)){
            $publication=self::getPublicationById($id);
        }else{
            $publication = new self();
        }
        $publication->no = $params['no'];
        $publication->author_no = $params['authorNo'];
        $publication->name = $params['name'];
        $publication->lang = isset($params['lang'])?$params['lang']:null;
        $publication->description = isset($params['description'])?$params['description']:null;
        $publication->width = isset($params['width'])?$params['width']:0;
        $publication->height = isset($params['height'])?$params['height']:0;
        $publication->year = isset($params['year'])?$params['year']:null;
        $publication->museum_id = isset($params['museumId'])?$params['museumId']:null;
        $publication->save();
        return $publication;
    }

    public static function getAuthor($pid){
        $publication = self::where(IekModel::CONDITION)
            ->where(IekModel::ID,$pid)
            ->with('author')
            ->first();
        return is_null($publication)?$publication:$publication->author;
    }

    public static function getPublicationByNo($no){
        $author = self::where('no',$no)
            ->first();
        return $author;
    }

    public static function getPublicationById($id){
        $author = self::where('id',$id)
            ->first();
        return $author;
    }
}