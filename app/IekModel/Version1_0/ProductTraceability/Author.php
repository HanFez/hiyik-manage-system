<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/12/17
 * Time: 5:16 PM
 */

namespace App\IekModel\Version1_0\ProductTraceability;


use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;

class Author extends IekProductTraceabilityModel
{
    protected $table="tblAuthors";

    public function publication() {
        return $this->hasMany(self::$NAME_SPACE.'\Publication', 'author_no', 'no')
            ->where(self::CONDITION);
    }

    public function authorIntroduction() {
        return $this->hasMany(self::$NAME_SPACE.'\AuthorIntroduction', 'author_id', self::ID)
            ->where(self::CONDITION);
    }

    public static function createAuthor($params){
        $err = new Error();
        $exist = self::getAuthorByNo($params['no']);
        if(!is_null($exist)){
            $err->setError(Errors::EXIST);
            $err->setData('no');
            return $err;
        }
        $err->setData(self::createRecord($params));
        return $err;
    }

    public static function modifyAuthor($params,$id){
        $err = new Error();
        $author = self::getAuthorById($id);
        if(is_null($author)){
            $err->setError(Errors::NOT_FOUND);
            return $err;
        }
        if($author->no != $params['no']){

//            $noAuthor = self::getAuthorByNo($params['no']);
//            if(!is_null($noAuthor)){
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
            $author=self::getAuthorById($id);
        }else{
            $author = new self();
        }
        $author->no = $params['no'];
        $author->name = $params['name'];
        $author->lang = isset($params['lang'])?$params['lang']:null;
        $author->description = isset($params['description'])?$params['description']:null;
        $author->introduction = isset($params['introduction'])?$params['introduction']:null;
        $author->nationality = isset($params['nationality'])?$params['nationality']:null;
        $author->saying = isset($params['saying'])?$params['saying']:null;
        $author->feature = isset($params['feature'])?$params['feature']:null;
        $author->save();
        return $author;
    }

    public static function getAuthorByNo($no){
        $author = self::where('no',$no)
            ->first();
        return $author;
    }

    public static function getAuthorById($id){
        $author = self::where('id',$id)
            ->first();
        return $author;
    }

}