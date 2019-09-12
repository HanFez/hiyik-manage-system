<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/12/17
 * Time: 5:20 PM
 */

namespace App\IekModel\Version1_0\ProductTraceability;


use App\IekModel\Version1_0\IekModel;

class PublicationIntroduction extends IekProductTraceabilityModel
{
    protected $table="tblPublicationIntroductions";

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
        $introduction->introduction_id = $params['introductionId'];
        $introduction->publication_id = $params['publicationId'];
        $introduction->save();
        return $introduction;
    }

    public static function exist($params){
        $data = self::where(IekModel::CONDITION)
            ->where('publication_id',$params['publicationId'])
            ->where('introduction_id',$params['introductionId'])
            ->first();
        if(is_null($data)) {
            return null;
        }
        return $data;
    }

}