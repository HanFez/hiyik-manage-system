<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/12/17
 * Time: 5:18 PM
 */

namespace App\IekModel\Version1_0\ProductTraceability;


use App\IekModel\Version1_0\IekModel;

class Introduction extends IekProductTraceabilityModel
{
    protected $table="tblIntroductions";

    public function authorIntroduction() {
        return $this->hasMany(self::$NAME_SPACE.'\AuthorIntroduction', 'introduction_id', self::ID)
            ->where(self::CONDITION);
    }

    public function productIntroduction() {
        return $this->hasMany(self::$NAME_SPACE.'\ProductIntroduction', 'introduction_id', self::ID)
            ->where(self::CONDITION);
    }

    public function publicationIntroduction() {
        return $this->hasMany(self::$NAME_SPACE.'\PublicationIntroduction', 'introduction_id', self::ID)
            ->where(self::CONDITION);
    }

    public function introductionContent() {
        return $this->hasMany(self::$NAME_SPACE.'\IntroductionContent', 'introduction_id', self::ID)
            ->where(self::CONDITION);
    }

    public static function createRecord($params){
        $exist = self::checkExist($params);
        if(!is_null($exist)){
            return $exist;
        }
        $introduction = new self();
        $introduction->name = isset($params['name'])?$params['name']:null;
        $introduction->description = isset($params['description'])?$params['description']:null;
        $introduction->type = isset($params['type'])?$params['type']:null;
        $introduction->save();
        foreach ($params['contents'] as $content){
            $content['introductionId'] = $introduction->{IekModel::ID};
            IntroductionContent::createRecord($content);
        }
        return $introduction;
    }

    public static function checkExist($params){
        $exist = self::where(IekModel::CONDITION)
            ->where('name',isset($params['name'])?$params['name']:null)
            ->where('description',isset($params['description'])?$params['description']:null)
            ->where('type',isset($params['type'])?$params['type']:null);
            foreach ($params['contents'] as $content){
                $exist = $exist->whereHas('introductionContent',function($q) use ($content){
                    $q->where('content',isset($content['content'])?$content['content']:null)
                        ->where('image_id',isset($content['imageId'])?$content['imageId']:null)
                        ->where('index',isset($content['index'])?$content['index']:0);
                });
            }
        $exist = $exist->with('introductionContent')->withCount('introductionContent')->first();
        if(!is_null($exist) && $exist->introduction_content_count == count($params['contents'])){
            return $exist;
        }
        return null;
    }

}