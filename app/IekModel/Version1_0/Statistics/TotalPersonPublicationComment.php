<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-7-25
 * Time: 下午12:25
 */

namespace App\IekModel\Version1_0\Statistics;
use App\IekModel\Version1_0\IekModel;

/**To order total view of all publication of a person.
 * Class TotalPersonPublicationComment
 * @package App\IekModel\Version1_0
 */
class TotalPersonPublicationComment extends IekModel{

    protected $table = 'viewTotalPersonPublicationComments';
    public $incrementing = false;
    public $timestamps = false;
    public $primaryKey = 'uid';

    /**Order person according to total publication comment count
     * @param array $personIds The id array of person
     * @param string $order
     * @return array The ordered id array of persons
     */
    public static function orderByComments(array $personIds, $order='desc') {
        if(is_null($personIds) || empty($personIds)) {
            return $personIds;
        }
        $ids = self::whereIn('uid', $personIds)
            ->orderBy('total', $order)
            ->pluck('uid')
            ->toArray();
        return $ids;
    }

}