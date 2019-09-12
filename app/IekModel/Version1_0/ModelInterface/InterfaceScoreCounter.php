<?php

/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-7-22
 * Time: 下午2:05
 */
namespace App\IekModel\Version1_0\ModelInterface;
/**
 * Interface InterfaceScoreCounter
 * @package App\IekModel\Version1_0\ModelInterface
 *
 * To computer the count for score.
 */
interface InterfaceScoreCounter {
    /** Obtain total action count of a person.
     * @param integer $uid The person id.
     * @return integer The total count of person actions.
     */
    public static function total($uid);

    /** Obtain action count of a person in a date interval.
     * @param integer $uid The person id.
     * @param string $begin The begin date string like 'yyyy-mm-dd hh:mm:ss'.
     * @param string $end The end date string like 'yyyy-mm-dd hh:mm:ss'.
     * @return integer The interval count of person actions.
     */
    public static function interval($uid, $begin, $end);

    /** Obtain the target's actions count.
     * @param integer $targetId Target object.
     * @return integer The count.
     */
    public static function passive($targetId);

    /**
     * @param integer $targetId The target object id.
     * @param string $begin The begin date string like 'yyyy-mm-dd hh:mm:ss'.
     * @param string $end The end date string like 'yyyy-mm-dd hh:mm:ss'.
     * @return integer The count.
     */
    public static function passiveInterval($targetId, $begin, $end);
}