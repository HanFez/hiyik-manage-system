<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 10/23/17
 * Time: 2:24 PM
 */

namespace App\IekModel\Version1_0\Notify;



use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Person;

class IekNotifyModel extends IekModel
{
    protected $connection = 'pgsql_notify';

    public static function makeNotify($params) {
        if(is_null($params -> action)
            || is_null($params -> fromId)
            || is_null($params -> toId)
            || is_null($params -> lang)
            || is_null ($params -> targetId)) {
            return;
        }
        //foreach($params->toIds as $toId){
            $notify = new static();
            $notify->action = $params->action;
            $notify->lang = $params->lang;
            $notify->from_id = $params->fromId;
            $notify->to_id = $params ->toId;
            $notify->target_id = $params->targetId;
            if(property_exists($params, 'reasonId')) {
                $notify->reason_id = $params->reasonId;
            }
            $result = $notify->save();
        //}
    }

}