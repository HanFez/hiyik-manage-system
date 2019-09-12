<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-7-25
 * Time: 下午12:25
 */

namespace App\IekModel\Version1_0\Statistics;
use App\IekModel\Version1_0\IekModel;
use Illuminate\Support\Facades\DB;

/**To order publications statistic information from multiple data view.
 * Class PublicationLikeCount
 * @package App\IekModel\Version1_0
 */
class PublicationLikeCount extends IekModel{

    protected $table = 'viewPublicationLikes';
    public $incrementing = false;
    public $timestamps = false;
    public $primaryKey = 'pid';


    /**Order publication according to like count
     * @param array $pubIds The id array of publications
     * @param string $order
     * @return array The ordered id array of publications
     */
    public static function orderByLikes(array $pubIds, $order='desc') {
        if(is_null($pubIds) || empty($pubIds)) {
            return $pubIds;
        }
        $ids = self::whereIn('pid', $pubIds)
            ->orderBy('cnt', $order)
            ->pluck('pid')
            ->toArray();
        return $ids;
    }

}