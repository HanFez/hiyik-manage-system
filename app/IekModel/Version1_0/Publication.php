<?php

//
// +------------------------------------------------------------------------+
// | PHP Version 5                                                          |
// +------------------------------------------------------------------------+
// | Copyright (c) All rights reserved.                                     |
// +------------------------------------------------------------------------+
// | File:                                                                  |
// +------------------------------------------------------------------------+
// | Author:                                                                |
// +------------------------------------------------------------------------+
//
// $Id$
//

namespace App\IekModel\Version1_0;

USE App\IekModel\Version1_0\PublicationTag;
use App\IekModel\Version1_0\PublicationImage;
use Illuminate\Support\Facades\DB;

/**
 * @author       Rich
 */
class Publication extends IekModel {
    const ORDER_VIEW = 2;
    const ORDER_LIKE = 3;
    const ORDER_COMMENT = 4;
    const ORDER_LAST = 5;
    const ORDER_OFFICIAL = 1;

    /**
     * @var  publication_id      INT4
     * @var  story_id            INT4
     * @var  preface_id          INT4
     * @var  person_id           INT4
     * @var  range_id            INT4
     * @var  created_at          timestamps
     * @var  updated_at          timestamps
     * @var  is_removed          Boolean
     * @var  is_publish          Boolean
     * @var  memo                String
     */
    protected $table = 'tblPublications';
    protected $guarded = [];
//    protected $hidden=['is_active', 'is_removed',];

    public function publicationTags() {
        return $this->hasMany(self::$NAME_SPACE.'\PublicationTag', self::PID, self::ID);
    }

    public function publicationDivider() {
        return $this->hasMany(self::$NAME_SPACE.'\PublicationDivider', self::PID, self::ID)
            ->where(self::CONDITION);
    }

    public function officialReason() {
        return $this->hasMany(self::$NAME_SPACE.'\ManageLogs', 'row_id', self::ID);
    }

    public function publicationBackground() {
        return $this->hasOne(self::$NAME_SPACE.'\PublicationBackground', self::PID, self::ID)
            ->where(self::CONDITION);
    }

    public function publicationPerson() {
        return $this->hasMany(self::$NAME_SPACE.'\PublicationPerson', self::PID, self::ID);
            //->where(self::CONDITION);
    }

    public function publicationOfficial() {
        return $this->hasMany(self::$NAME_SPACE.'\PublicationOfficial', self::PID, self::ID);
    }
    public function publicationTitle() {
        return $this->hasMany(self::$NAME_SPACE.'\PublicationTitle', self::PID, self::ID);
    }

    public function cover() {
        return $this->hasOne(self::$NAME_SPACE.'\PublicationCover', self::PID, self::ID)
            ->where(self::CONDITION)
            ->orderBy(self::CREATED);
    }

    public function images() {
        return $this->hasMany(self::$NAME_SPACE.'\PublicationImage', self::PID, self::ID)
            ->where(self::CONDITION);
    }

    public function contents() {
        return $this->hasMany(self::$NAME_SPACE.'\PublicationContent', self::PID, self::ID);
    }

    public function publicationRange() {
        return $this->hasMany(self::$NAME_SPACE.'\PublicationRange', self::PID, self::ID);
    }
    

    public function viewers() {
        return $this->hasMany(self::$NAME_SPACE.'\PublicationViewer', self::PID, self::ID);
    }

    public function likers() {
        return $this->hasMany(self::$NAME_SPACE.'\PublicationLike', self::PID, self::ID);
    }

    public function comments() {
        return $this->hasMany(self::$NAME_SPACE.'\PublicationComment', self::PID, self::ID);
    }

    public function publicationReport() {
        return $this->hasMany(self::$NAME_SPACE.'\Report', 'target_id', self::ID)
            ->where('target_type','publication');
    }

    public function publicationForbidden() {
        return $this->hasMany(self::$NAME_SPACE.'\ManageLogs', 'row_id', self::ID)
            ->where('table_name',self::getDataTable());
    }

    public function description(){
        return $this->hasMany(self::$NAME_SPACE.'\PublicationDescription', self::PID, self::ID);
    }

    public function tag(){
        return $this->hasMany(self::$NAME_SPACE.'\PublicationTag',self::PID,self::ID);
    }

    public function getTags() {
        $tags = [];
        $pubTags = $this->publicationTags;
        foreach ($pubTags as $pubTag) {
            if($pubTag->{self::ACTIVE} && !$pubTag->{self::REMOVED}) {
                $tag = Tag::where(self::ACTIVE, true)
                    ->where(self::REMOVED, false)
                    ->where(self::ID, $pubTag->tag_id)
                    ->first();
                if(!is_null($tag)) {
                    self::doTrans($tag, self::NAME);
                    array_push($tags, $tag);
                }
            }
        }
        return $tags;
    }
    public function getCurrentTags() {
        $tags = [];
        $pubTags = PublicationTag::where(self::PID, $this->id)
            ->where(self::ACTIVE, true)
            ->where(self::REMOVED, false)
            ->get();
        foreach ($pubTags as $pubTag) {
            $tag = Tag::where(self::ACTIVE, true)
                ->where(self::REMOVED, false)
                ->where(self::ID, $pubTag->tag_id)
                ->first();
            if(!is_null($tag)) {
                self::doTrans($tag, self::NAME);
                array_push($tags, $tag);
            }
        }
        return $tags;
    }

    public function checkOwner($uid) {
        $status = false;
        $relation = $this->publicationPerson;
        if(!is_null($relation)) {
            foreach ($relation as $r) {
                if($r->person_id == $uid) {
                    //unset($this->publicationPerson);
                    $status = true;
                }
            }
        } else {
            $status = false;
        }
        unset($this->publicationPerson);
        return $status;
    }

    public function getContentIds($model) {
        return $model::getIds($this->{self::ID});
    }

    public static function getProfile($pid) {
        $profile = new \stdClass();
        $profile->id = $pid;
        $title = PublicationTitle::getTitleString($pid);
        $profile->title = $title;
        $coverId = PublicationCover::getCoverImageId($pid);
        $profile->cover = null;
        if(!is_null($coverId)) {
            $norms = ImageNorm::getImageNorms($coverId);
            $profile->cover = $norms;
        }
        $imageId = PublicationImage::getSellImageId($pid);
        $profile->content = null;
        if(!is_null($imageId)) {
            $norms = ImageNorm::getImageNorms($imageId);
            $profile->content = $norms;
        }
        return $profile;
    }

    public static function getProfiles(array $pubIds) {
        $idsString = implode(',', $pubIds);
        $postgreDateFormat = "'YYYY-MM-DD HH24:MI:SS'";
        $profileQuery = sprintf('
            select pub_titles.id, pub_titles.title, pub_covers.cover, pub_contents.content from
            (select "tblPublicationTitles".publication_id as id, "tblDescriptions".content as title
             from "tblPublicationTitles" inner join "tblDescriptions"
              on "tblPublicationTitles".content_id="tblDescriptions".id 
              where "tblDescriptions".is_active=\'t\' 
              and "tblDescriptions".is_removed=\'f\' 
              and "tblPublicationTitles".is_active=\'t\' 
              and "tblPublicationTitles".is_removed=\'f\') as pub_titles
            ,
            (select "tblPublicationCovers".publication_id as id, json_agg(norms.*) as cover
             from "tblPublicationCovers", 
              (select concat("tblNorms".width, \'_\', "tblNorms".height) as name,
                "tblImageNorms".id, "tblImageNorms".is_active, "tblImageNorms".is_removed, "tblImageNorms".uri,
                "tblImageNorms".image_id, "tblImageNorms".norm_id, "tblImageNorms".width, "tblImageNorms".height,
                "tblImageNorms".md5, "tblImageNorms".length, 
                to_char("tblImageNorms".created_at, %s) as created_at,
                to_char("tblImageNorms".updated_at, %s) as updated_at
                from "tblImageNorms", "tblNorms" where "tblImageNorms".norm_id="tblNorms".id) as norms
                where "tblPublicationCovers".image_id=norms.image_id 
                and norms.is_active=\'t\' 
                and norms.is_removed=\'f\' 
                and "tblPublicationCovers".is_active=\'t\' 
                and "tblPublicationCovers".is_removed=\'f\'
                and "tblPublicationCovers".publication_id in (%s)
                group by "tblPublicationCovers".publication_id) as pub_covers
            ,
            (select "tblPublicationImages".publication_id as id, json_agg(norms.*) as content
             from "tblPublicationImages", 
              (select concat("tblNorms".width, \'_\', "tblNorms".height) as name, 
                "tblImageNorms".id, "tblImageNorms".is_active, "tblImageNorms".is_removed, "tblImageNorms".uri,
                "tblImageNorms".image_id, "tblImageNorms".norm_id, "tblImageNorms".width, "tblImageNorms".height,
                "tblImageNorms".md5, "tblImageNorms".length, 
                to_char("tblImageNorms".created_at, %s) as created_at,
                to_char("tblImageNorms".updated_at, %s) as updated_at
                from "tblImageNorms", "tblNorms" where "tblImageNorms".norm_id="tblNorms".id) as norms
             where "tblPublicationImages".image_id=norms.image_id and norms.is_active=\'t\' and norms.is_removed=\'f\'
             and "tblPublicationImages".is_active=\'t\'
             and "tblPublicationImages".is_removed=\'f\'
             and "tblPublicationImages".can_sell=\'t\'
             and "tblPublicationImages".publication_id in (%s)
             group by "tblPublicationImages".publication_id) as pub_contents
             where pub_titles.id=pub_covers.id and pub_covers.id=pub_contents.id',
            $postgreDateFormat, $postgreDateFormat, $idsString,
            $postgreDateFormat, $postgreDateFormat, $idsString);
        $result = DB::select(DB::raw($profileQuery));
        //$profiles = null;
        if(!is_null($result) && !empty($result)) {
            //$profiles = array();
            foreach ($result as $item) {
                $item->cover = json_decode($item->cover);
                $item->content = json_decode($item->content);
            }
        }
        return $result;
    }
    public static function orderPersonPublications($pubIds, $order) {
        if(is_null($pubIds)) {
            return null;
        }
        $ordered = null;
        $orderIds = null;
        $orderQuery = 'select pid, case when count("%s".id)>0 then count("%s".id) else 0 end as cnt 
                  from (select unnest(array[%s]) as pid) as pubs
                  left join "%s" on pid=publication_id and is_active=\'t\' and is_removed=\'f\'
                  group by pid order by cnt desc';
        $idsStr = implode(',',$pubIds);
        switch ($order) {
            case self::ORDER_VIEW:
                $orderIds = array();
                $table = PublicationViewer::getDataTable();
                $query = sprintf($orderQuery,
                    $table, $table, $idsStr, $table);
                $ordered = DB::select(DB::raw($query));
                foreach ($ordered as $item) {
                    $orderIds[] = $item->pid;
                }
                break;
            case self::ORDER_LIKE:
                $orderIds = array();
                $table = PublicationLike::getDataTable();
                $query = sprintf($orderQuery,
                    $table, $table, $idsStr, $table);
                $ordered = DB::select(DB::raw($query));
                foreach ($ordered as $item) {
                    $orderIds[] = $item->pid;
                }
                break;
            case self::ORDER_COMMENT:
                $orderIds = array();
                $table = PublicationComment::getDataTable();
                $query = sprintf($orderQuery,
                    $table, $table, $idsStr, $table);
                $ordered = DB::select(DB::raw($query));
                foreach ($ordered as $item) {
                    $orderIds[] = $item->pid;
                }
                break;
            case self::ORDER_LAST:
                $orderIds = Publication::where(IekModel::CONDITION)
                    ->whereIn(IekModel::ID, $pubIds)
                    ->orderBy(IekModel::CREATED, 'desc')
                    ->pluck(IekModel::ID)
                    ->toArray();
                break;
            case self::ORDER_OFFICIAL:
                $orderIds = PublicationOfficial::where(IekModel::CONDITION)
                    ->whereIn(IekModel::PID, $pubIds)
                    ->orderBy(IekModel::UPDATED, 'desc')
                    ->pluck(IekModel::PID)
                    ->toArray();
        }
        return $orderIds;
    }

    public static function checkPublication($pid){
        $count = self::where(self::ID,$pid)
            ->count();
        return $count == 0 ? false : true;
    }

    public static function getOwner($pid){
        $owner = PublicationPerson::where(self::PID,$pid)
            ->with(['person.personNick'=>function($query){
                $query->where(self::CONDITION)
                    ->with('nick');
            }])
            ->get();
        return $owner;
    }
}

?>
