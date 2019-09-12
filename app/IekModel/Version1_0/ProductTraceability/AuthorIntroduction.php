<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/12/17
 * Time: 5:16 PM
 */

namespace App\IekModel\Version1_0\ProductTraceability;


use App\IekModel\Version1_0\IekModel;

class AuthorIntroduction extends IekProductTraceabilityModel
{
    protected $table="tblAuthorIntroductions";

    public function author() {
        return $this->hasOne(self::$NAME_SPACE.'\Author', self::ID, 'author_id')
            ->where(self::CONDITION);
    }

    public function introduction() {
        return $this->hasOne(self::$NAME_SPACE.'\Introduction', self::ID, 'introduction_id')
            ->where(self::CONDITION);
    }

    public static function createRecord($params){
        $exist = self::exist($params);
        if(!is_null($exist)){
            return $exist;
        }
        $introduction = new self();
        $introduction->author_id = $params['authorId'];
        $introduction->introduction_id = $params['introductionId'];
        $introduction->save();
        return $introduction;
    }

    public static function exist($params){
        $data = self::where(IekModel::CONDITION)
            ->where('author_id',$params['authorId'])
            ->where('introduction_id',$params['introductionId'])
            ->first();
        if(is_null($data)) {
            return null;
        }
        return $data;
    }

}