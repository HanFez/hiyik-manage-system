<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-7-25
 * Time: 下午12:25
 */

namespace App\IekModel\Version1_0\Statistics;
use App\IekModel\Version1_0\IekModel;
class PersonProfile extends IekModel{

    protected $table = 'viewPersonProfiles';
    public $incrementing = false;
    public $timestamps = false;
    public $primaryKey = 'id';

    public static function getProfiles(array $personIds) {
        if(is_null($personIds) || empty($personIds)) {
            return null;
        }
        $personIdStr = IekModel::implodeUuid(',', $personIds);
        $orderSql = sprintf('array_position(ARRAY[%s]::UUID[],%s)', $personIdStr, self::ID);
        $profiles = self::whereIn('id', $personIds)
            ->orderByRaw($orderSql)
            ->get()->each(function($item, $key){
                if($item->avatar_forbidden) {
                    unset($item->avatar);
                    unset($item->relation_id);
                    unset($item->image_id);
                } else {
                    if (!is_null($item->avatar)) {
                        $avatar = json_decode($item->avatar);
                        unset($item->avatar);
                        $temp = new \stdClass();
                        $temp->id = $item->relation_id;
                        $temp->image_id = $item->image_id;
                        $temp->person_id = $item->id;
                        $temp->norms = $avatar;
                        $item->avatar = $temp;
                        unset($item->relation_id);
                        unset($item->image_id);
                    }
                }
            });
        return $profiles;
    }
}