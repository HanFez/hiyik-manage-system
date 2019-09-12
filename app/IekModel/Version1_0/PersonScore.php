<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-7-19
 * Time: 上午11:01
 */

namespace App\IekModel\Version1_0;
use App\IekModel\Version1_0\Constants\PersonAction;
use Carbon\Carbon;
use Faker\Provider\DateTime;
use Illuminate\Support\Facades\DB;
use App\IekModel\Version1_0\PublicationViewer;


/**
 * Class PersonScore
 * @package App\IekModel\Version1_0
 * The model is to handle the computing of person score and level.
 * Because the score is constructed via many action table records.
 */
class PersonScore extends IekModel {

    const DELTA = 10000;
    const LEVEL_BASE = 1;
    const VIEW = 'view';
    const LIKE = 'like';
    const COMMENT = 'comment';
    const RECOMMEND = 'recommend';
    const SHARE = 'share';
    const FORBIDDEN = 'forbidden';
    const OFFICIAL = 'official';
    const PUBLICATION_VIEW = 'PublicationViewer';
    const PUBLICATION_LIKE = 'PublicationLike';
    const PUBLICATION_COMMENT = 'PublicationComment';
    const PUBLICATION_RECOMMEND = 'PublicationRecommend';
    const PUBLICATION_OFFICIAL = 'PublicationOfficial';
    const PUBLICATION_SHARE = 'PublicationShare';
    const PUBLICATION_FORBIDDEN = 'PublicationForbidden';
    protected $table = "tblPersonScores";

    public static function getActiveScore($uid, $action = null, $begin = null, $end = null) {
        $score = 0;
        if(is_null($uid)) {
            return $score;
        }
        //$userRecord = self::where(self::UID, $uid)->first();
        //if(!is_null($userRecord)) {
            //$scores = json_decode($userRecord->score);
            $typeId = ScoreType::getIdByName(ScoreType::LIVELY);
            $rules = [];
            if(!is_null($action)) {
                $rule = ScoreRule::getRule($action, $typeId);
                array_push($rules, $rule);
            } else {
                $rules = ScoreRule::getRules($typeId);
            }
            foreach ($rules as $rule) {
                $count = self::getActionCount($uid, $rule->action, $begin, $end);
                $score += $rule->score * $rule->factor * $count;
            }
        //}
        return $score;
    }

    public static function getCharmScore($uid) {
        //TODO
    }

    public static function getCreativeScore($uid, $action = null, $begin = null, $end = null) {
        $score = 0;
        $person = Person::where(Person::CONDITION)
            ->where(Person::ID, $uid)
            ->orderBy(Person::CREATED, 'desc')
            ->first();
        //dd($person);
        if(!is_null($person)) {
            //$personPublications = $person->publicationPerson;
            $scoreTypeId  = ScoreType::getIdByName(ScoreType::CREATIVE);
            $rules = [];
            if(!is_null($action)) {
                $rule = ScoreRule::getRule($action, $scoreTypeId);
                array_push($rules, $rule);
            } else {
                $rules = ScoreRule::getRules($scoreTypeId);
            }
            $clz = null;
            foreach ($rules as $rule) {
                try {
                    switch ($rule->action) {
                        case self::VIEW:
                            $clz = IekModel::$NAME_SPACE . '\\' . self::PUBLICATION_VIEW;
                            break;
                        case self::LIKE:
                            $clz = IekModel::$NAME_SPACE . '\\' . self::PUBLICATION_LIKE;
                            break;
                        case self::COMMENT:
                            $clz = IekModel::$NAME_SPACE . '\\' . self::PUBLICATION_COMMENT;
                            break;
                        case self::RECOMMEND:
                            break;
                        case self::OFFICIAL:
                            $clz = IekModel::$NAME_SPACE . '\\' . self::PUBLICATION_OFFICIAL;
                            break;
                        case self::FORBIDDEN:
                            $clz = IekModel::$NAME_SPACE . '\\' . self::PUBLICATION_FORBIDDEN;
                            break;
                    }
                    if(!is_null($clz)) {
                        $s = self::getCreativeItemScore($uid,
                            $clz::getDataTable(), null, null);
                        $score = $score + $s;
                    }
                } catch(\Exception $ex) {
                    continue;
                }
//                foreach ($personPublications as $relation) {
//                    if($relation->is_active && !$relation->is_removed) {
//                        $publication = $relation->publication;
//                        if(is_null($publication)
//                            || !$publication->is_publish
//                            /*|| PublicationForbidden::isForbidden($publication->id)*/) {
//                            continue;
//                        }
//                        $method = 'getPublication'.studly_case($rule->action).'Score';
//                        if(method_exists(self::class, $method)) {
//                            $s = call_user_func_array(self::class.'::'.$method, [$publication->id, $rule, $begin, $end]);
//                            if(is_numeric($s)) {
//                                $score += $s;
//                            }
//                        }
//                    }
//                }
            }
        }
        //dd($score);
        return $score;
    }

    public static function getPublicationViewScore($pid, $rule, $begin, $end) {
        $count = PublicationViewer::getViewIntervalCount($pid, $begin, $end);
//        $rule = ScoreRule::getRule(PersonAction::VIEW, ScoreType::CREATIVE);

        return self::computeScore($rule, $count);
    }

    public static function getPublicationFanScore($pid, $rule, $begin, $end) {
        $count = PublicationFan::getFansCount($pid, $begin, $end);
//        $rule = ScoreRule::getRule(PersonAction::FAN, ScoreType::CREATIVE);
        return self::computeScore($rule, $count);
    }

    public static function getPublicationLikeScore($pid, $rule, $begin, $end) {
        $count = PublicationLike::getLikeCount($pid, $begin, $end);
//        $rule = ScoreRule::getRule(PersonAction::LIKE, ScoreType::CREATIVE);
        return self::computeScore($rule, $count);
    }

    public static function getPublicationCommentScore($pid, $rule, $begin, $end) {
        $count = PublicationComment::getCommentCount($pid, $begin, $end);
//        $rule = ScoreRule::getRule(PersonAction::COMMENT, ScoreType::CREATIVE);
        return self::computeScore($rule, $count);
    }

    public static function getPublicationOfficialScore($pid, $rule, $begin, $end) {
        if(PublicationOfficial::isOfficial($pid)) {
            return self::computeScore($rule, 1);
        }
        return 0;
    }

    public static function getPublicationForbiddenScore($pid, $rule, $begin, $end) {
        if(PublicationForbidden::checkForbidden($pid)) {
            return self::computeScore($rule, 1);
        }
        return 0;
    }

    public static function getPublicationRecommendScore($pid, $rule, $begin, $end) {
        return 0;
    }

    public static function getPublicationShareScore($pid, $rule, $begin, $end) {
        return 0;
    }

    public static function getPublicationBuyScore($pid, $rule, $begin, $end) {
        return 0;
    }
    public static function getCommentLikeScore($cid, $rule, $begin, $end) {
        return 0;
    }
    public static function computeScore($rule, $count) {
        if(!is_null($rule)) {
            return $count * $rule->score * $rule->factor;
        } else {
            return 0;
        }
    }
    public static function getBuyScore($uid) {
        //TODO
    }

    public static function getActionCount($uid, $action, $begin, $end) {
        $countQuery = <<<"COUNT_QUERY"
SELECT count(*) FROM jsonb_array_elements((
SELECT score FROM "tblPersonScores" WHERE person_id=$uid)) AS jrecord 
COUNT_QUERY;
        if(!is_null($action)) {
            $actionWhere = sprintf('WHERE jrecord::jsonb->>\'action\'=\'%s\'', $action);
            $countQuery = $countQuery.$actionWhere;
            if(!is_null($begin)) {
                $beginWhere = sprintf('AND date(jrecord::jsonb->>\'created_at\')>=\'%s\'', $begin);
                $countQuery = $countQuery.$beginWhere;
            }
            if(!is_null($end)) {
                $endWhere = sprintf('AND date(jrecord::jsonb->>\'created_at\')<\'%s\'', $end);
                $countQuery = $countQuery.$endWhere;
            }
        } else {
            if(!is_null($begin)) {
                $beginWhere = sprintf('WHERE date(jrecord::jsonb->>\'created_at\')>=\'%s\'', $begin);
                $countQuery = $countQuery.$beginWhere;
                if(!is_null($end)) {
                    $endWhere = sprintf('AND date(jrecord::jsonb->>\'created_at\')<\'%s\'', $end);
                    $countQuery = $countQuery.$endWhere;
                }
            } else {
                if(!is_null($end)) {
                    $endWhere = sprintf('WHERE date(jrecord::jsonb->>\'created_at\')<\'%s\'', $end);
                    $countQuery = $countQuery.$endWhere;
                }
            }
        }
        $count = DB::select($countQuery);
        if(is_null($count) || count($count) == 0) {
            return 0;
        } else {
            return $count[0]->count;
        }
//        return self::select($countQuery)->where($where)->count();
    }
    public static function activeScore($uid, $action, $scoreType) {
        $typeId = ScoreType::getIdByName($scoreType);
        $rule = ScoreRule::getRule($action, $typeId);
        $personScore = self::where(self::UID, $uid)
            ->first();
        $today = Carbon::now()->format(self::DATETIME_FORMAT);
        $scoreItem = new \stdClass();
        $scoreItem->action = $action;
        $scoreItem->rule_id = $rule->id;
        $scoreItem->created_at = $today;
        $scoreItem->updated_at = $scoreItem->created_at;
        if(is_null($personScore)) {
            $personScore = new PersonScore();
            $personScore->{self::UID} = $uid;
            $personScore->{self::ACTIVE} = true;
            $personScore->{self::REMOVED} = false;
            $score = json_encode([$scoreItem]);
            $personScore->score = $score;
            $personScore->save();
        } else {
            $score = json_decode($personScore->score);
//            $countList = array_filter($score, function($item) use ($action) {
//                $temp = new Carbon($item->{self::CREATED_AT});
//                if($temp->isToday() && $item->action == $action) {
//                    return true;
//                }
//                return false;
//            });
            $beginDate = Carbon::now()->setTime(0,0,0)->format(self::DATE_FORMAT);
            $endDate = Carbon::now()->addDay()->setTime(0,0,0)->format(self::DATE_FORMAT);
            $count = self::getActionCount($uid, $action, $beginDate, $endDate);
            if($count < $rule->threshold) {
                array_push($score, $scoreItem);
                $personScore->score = json_encode($score);
                $personScore->save();
            }
        }
    }

    public static function computeLevel($score, $threshold, $level) {
        if($score < $threshold) {
            return $level;
        } else {
            $level += 1;
            $d = self::DELTA + self::DELTA * (0.01) * $level;
            $s = $threshold + $d;
            return self::computeLevel($score, $s, $level);
        }
        //return intval($score/$delta);
    }

    public static function getPersonScore($uid) {
        $activeScore = self::getActiveScore($uid);

        $score = new \stdClass();
        $score->active = $activeScore;
        $score->charm = 0;
        $score->creative = self::getCreativeScore($uid);
        $score->buy = 0;
        $total = $score->active + $score->charm + $score->creative + $score->buy;
        $score->level = self::computeLevel($total, self::DELTA, self::LEVEL_BASE);
        return $score;
    }

    public static function getCreativeItemScore($uid, $itemTable, $begin, $end) {
        if(is_null($uid) || is_null($itemTable)) {
            return 0;
        }
        $tempBegin = $begin;
        $tempEnd = $end;
        if(is_null($tempEnd)) {
            $tempEnd = date("Y-m-d G:i:s", strtotime('23:59:59'));
        }
        if(!is_null($begin) && !is_null($end)) {
            $interval = sprintf(' and pa.created_at between \'%s\' and \'%s\' ',$tempBegin, $tempEnd);
        } else {
            $interval = sprintf(' and pa.created_at <= \'%s\'', $tempEnd);
        }
        $query = sprintf('select 
            (select sum(pub_total.cnt) from 
              (select pid, pub_count.cnt
               from unnest(person_pubs.pids) as pid
                left join
                 (select pa.publication_id, count(pa.publication_id) as cnt
                  from "%s" as pa
                   where pa.is_active=\'t\' and pa.is_removed=\'f\'
                   %s
                    group by pa.publication_id) as pub_count
                on pid=pub_count.publication_id 
                order by pub_count.cnt) as pub_total
          ) as total_count
          from
            (select uid, case 
             when count(pp.publication_id)>0 
             then array_agg(pp.publication_id) 
             else null end as pids
             from unnest(array[%s]) as uid 
             left join (select pu.publication_id, pu.person_id
			 from "tblPublicationPersons" as pu, "tblPublications" as p 
			 where p.is_publish=\'t\' 
			 and p.is_active=\'t\' 
			 and p.is_removed=\'f\' 
			 and pu.is_active=\'t\' 
			 and pu.is_removed=\'f\' 
			 and pu.publication_id = p.id) as pp 
             on uid=pp.person_id 
             group by uid) 
            as person_pubs',
            $itemTable,
            $interval,
            $uid
            );
        $result = DB::select(DB::raw($query));
        $total = 0;
        if(!is_null($result) && !empty($result)) {
            foreach ($result as $item) {
                if(!is_null($item->total_count)) {
                    $total = $total + $item->total_count;
                }
            }
        }
        //dd($itemTable.':'.$total);
        return $total;
    }
}