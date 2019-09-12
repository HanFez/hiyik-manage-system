<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-7-25
 * Time: 下午12:25
 */

namespace App\IekModel\Version1_0\Statistics;
use App\IekModel\Version1_0\IekModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

/**To order publications statistic information from multiple data view.
 * Class PublicationProfile
 * @package App\IekModel\Version1_0
 */
class PublicationProfile extends IekModel{

    protected $table = 'viewPublicationProfiles';
    public $incrementing = false;
    public $timestamps = false;
    public $primaryKey = 'id';

    public function statistics() {
        return $this->hasOne(self::$NAME_SPACE.'\Statistics\PublicationStatistic', 'pid', 'id');
    }

    public function tags() {
        return $this->hasMany(self::$NAME_SPACE.'\PublicationTag', self::PID, self::ID)
            ->where(self::CONDITION);
    }

    public function authors() {
        return $this->hasMany(self::$NAME_SPACE.'\PublicationPerson', self::PID, self::ID)
            ->where(self::CONDITION);
    }

    public function official() {
        return $this->hasOne(self::$NAME_SPACE.'\PublicationOfficial', self::PID, self::ID)
            ->where(self::CONDITION);
    }

    /** To obtain the profile information of publication.
     *  The profile include: title, cover, content(image), author(nick),
     *  statistics(view, like, comment, like_hot, comment_hot),
     *  official and relative forbidden status.
     * @param array $pubIds The publication id array.
     * @return null|Collection The profile of publication.
     */
    public static function getProfiles(array $pubIds) {
        $query = self::with('statistics')
            ->with(['tags' => function($query) {
                $query->with('tag');
            }])
            ->with('official')
            ->with('authors.person.personNick.nick');
        if(is_null($pubIds) || empty($pubIds)) {
            $result = null;
        } else {
            $pubIdStr = IekModel::implodeUuid(',', $pubIds);
            $orderSql = sprintf('array_position(ARRAY[%s]::UUID[],%s)', $pubIdStr, self::ID);
            $result = $query->whereIn(self::ID, $pubIds)
                ->orderByRaw($orderSql)
                ->get();
        }
        if(!is_null($result) && !empty($result)) {
            foreach ($result as $item) {
                $item->cover = json_decode($item->cover);
                $item->content = json_decode($item->content);
                foreach ($item->tags as $tagItem) {
                    if($tagItem->tag->is_removed) {
                        $tagItem->tag->is_forbidden = true;
                    } else {
                        $tagItem->tag->is_forbidden = false;
                        $tagItem->tag = self::doTrans($tagItem->tag, self::NAME);
                    }
                }
                $persons = null;
                foreach ($item->authors as $author) {
                    $id = $author->person_id;
                    $nick = null;
                    if(!is_null($author->person)) {
                        if(!is_null($author->person->personNick)) {
                            if(!is_null($author->person->personNick->nick)) {
                                $nick = $author->person->personNick->nick->nick;
                                $isForbidden = $author->person->personNick->nick->is_removed;
                            }
                        }
                        $author = new \stdClass();
                        $author->id = $id;
                        if($isForbidden) {
                            $author->nick = null;
                        } else {
                            $author->nick = $nick;
                        }
                        $author->isForbidden = $isForbidden;
                        $persons[] = $author;
                    }
                }
                unset($item->authors);
                $item->persons = $persons;
            }
        }
        return $result;
    }
}