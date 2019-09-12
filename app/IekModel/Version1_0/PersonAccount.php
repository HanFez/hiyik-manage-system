<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-6-23
 * Time: 下午5:53
 */

namespace App\IekModel\Version1_0;


class PersonAccount extends IekModel
{
    protected $table = 'tblPersonAccounts';
    protected $fillable = [
        'person_id', 'account','is_active',
    ];

    public static function getPersonId($accountId) {
        $personAccount = self::where('account', $accountId)->first();
        if(is_null($personAccount)) {
            return null;
        } else {
            return $personAccount->person_id;
        }
    }

    public static function getAccount($personId) {
        $personAccount = self::where('person_id', $personId)->first();
        if(is_null($personAccount)) {
            return null;
        } else {
            return $personAccount->account;
        }
    }
    public function person() {
        return $this->belongsTo(self::$NAME_SPACE.'\Person', 'person_id', 'id');
    }

    public function account() {
        return $this->belongsTo(self::$NAME_SPACE.'\Account', 'account_id', 'id');
    }

}