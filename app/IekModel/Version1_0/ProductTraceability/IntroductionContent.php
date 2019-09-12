<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/12/17
 * Time: 5:18 PM
 */

namespace App\IekModel\Version1_0\ProductTraceability;


use App\IekModel\Version1_0\IekModel;

class IntroductionContent extends IekProductTraceabilityModel
{
    protected $table="tblIntroductionContents";

    public function introduction() {
        return $this->hasOne(self::$NAME_SPACE.'\Introduction', self::ID, 'introduction_id')
            ->where(self::CONDITION);
    }

    public function image() {
        return $this->hasOne(self::$NAME_SPACE.'\Image', self::ID, 'image_id')
            ->where(self::CONDITION);
    }

    public static function createRecord($params){
        $exist = self::exist($params);
        if(!is_null($exist)){
            return $exist;
        }
        $introduction = new self();
        $introduction->introduction_id = $params['introductionId'];
        $introduction->content = isset($params['content'])?$params['content']:null;
        $introduction->image_id = isset($params['imageId'])?$params['imageId']:null;
        $introduction->index = isset($params['index'])?$params['index']:0;
        $introduction->save();
        return $introduction;
    }

    public static function exist($params){
        $introduction = self::where(IekModel::CONDITION)
            ->where('introduction_id',$params['introductionId']);
        if(isset($params['content'])){
            $introduction->where('content',$params['content']);
        }
        if(isset($params['imageId'])){
            $introduction->where('image_id',$params['imageId']);
        }
        if(isset($params['index'])){
            $introduction->where('index',$params['index']);
        }
        $introduction = $introduction->first();
        return $introduction;
    }
}