<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-7-25
 * Time: 下午12:25
 */

namespace App\IekModel\Version1_0\Statistics;
use App\IekModel\Version1_0\IekModel;
class PersonLast extends IekModel{

    protected $table = 'viewPersonLast';
    public $incrementing = false;
    public $timestamps = false;
    public $primaryKey = 'person_id';

    public static function orderByLast(array $pubIds, $order='desc') {
        if(is_null($pubIds) || empty($pubIds)) {
            return $pubIds;
        }
        $ids = self::whereIn('person_id', $pubIds)
            ->pluck('person_id')
            ->toArray();
        return $ids;
    }
}