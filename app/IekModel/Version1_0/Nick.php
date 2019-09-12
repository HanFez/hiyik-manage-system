<?php

namespace App\IekModel\Version1_0;

class Nick extends IekModel {

    protected $table = "tblNicks";
    protected $guarded = [];
//    protected $hidden = ['is_active', 'is_removed', 'updated_at', 'created_at'];

    public function personNick() {
        return $this->hasMany(self::$NAME_SPACE.'\PersonNick', 'nick_id', 'id');
    }

    public function nickForbidden() {
        return $this->hasMany(self::$NAME_SPACE.'\NickForbidden', 'nick_id', 'id');
    }

    public static function hasExist($content) {
        $nick = self::where(self::CONDITION)
            ->where(self::NICK, $content)
            ->orderBy(self::CREATED, 'desc')
            ->first();
        return $nick;
    }

    public static function insertNick($params) {
        $nick = self::hasExist($params->{self::NICK});
        if(is_null($nick)) {
            $nick = new self();
            $nick->{self::NICK} = $params->{self::NICK};
            $result = $nick->save();
            if ($result) {
                return $nick;
            } else {
                return null;
            }
        } else {
            return $nick;
        }
    }

    public static function checkExist($nid){
        $count = self::where(self::ID,$nid)
            ->count();
        return $count == 0 ? false : true;
    }

    public function manageLog() {
        return $this->hasMany(self::$NAME_SPACE.'\ManageLogs', 'row_id', 'id');
    }
}
