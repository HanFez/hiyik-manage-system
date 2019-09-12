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
 * Class PublicationStatistic
 * @package App\IekModel\Version1_0
 */
class PublicationStatistic extends IekModel{

    protected $table = 'viewPublicationStatistics';
    public $incrementing = false;
    public $timestamps = false;
    public $primaryKey = 'pid';

    /**To obtain count statistic information of publication(s),
     * include view count, like count, comment count
     * @param array|integer $pubIds The id array or id of publication(s)
     * @return null
     */
    public static function getStatistics($pubIds) {
        $result = null;
        if(is_null($pubIds)) {
            return $result;
        }
        if(is_array($pubIds)) {
            if(!empty($pubIds)) {
                $result = self::whereIn('pid', $pubIds)->get();
            }
        } else {
            $result = self::where('pid', $pubIds)->first();
        }
        return $result;
    }
}