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
use Gregwar\Captcha\PhraseBuilder;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Exception\NotFoundException;

/**
* The person account on our web site.
* @author       Rich
*/
class Account extends IekModel implements Authenticatable
{
    

    protected $table = 'tblAccounts';
    public $primaryKey = 'id';
    public $incrementing = false;
    protected $guarded = [];

    /**
     * To generator account id.
     * @return int
     */
    static public function generator() {
        // ATTENTION: Our account table set the account column minLength = maxLength = 9,
        // this function may get exception when plus too many. We should change the table's' constraint.
        $account = 100000000 + Account::all()->count();
        while(Account::find($account)) {
            $account = $account + 1;
        }
        return $account;
    }

    /**
     * To query database, obtain the register mail according to account.
     * @param $accountId
     * @return array|bool
     */
    static public function getRegisterMail($accountId)
    {
        if ($accountId !== null) {
            try {
                $person = Person::where(self::ACCOUNT, $accountId)->get()->first();
                if($person !== null) {
                    $mail = MailDomain::where(self::UID, $person->person_id)->get();
                    if($mail !== null) {
                        $mails = [];
                        foreach($mail as $m) {
                            if($m->is_active && (!$m->is_removed)) {
                                $mails->push($m);
                            }
                        }
                        return $mails;
                    }
                }
            } catch (NotFoundException $ex) {
                Log::error($ex->getMessage());
                return false;
            }
        }
        return false;
    }

    /**
     * To obtain current account's bound mails.
     * @return array|bool
     */
    public function getMail() {
        return self::getRegisterMail($this->account);
    }

    /**
     * To generator a active link for account used into the active mail.
     * @return mixed
     */
    public function makeActiveLink() {
        $activeCode = hash(self::HASH_MD5, $this->created_at);
        $linkData = [self::ID => $this->account, 'activeCode' => $activeCode];
        $link = action('Restful\AccountController@activeAccount', $linkData);
        return $link;
    }

    public function makeRegisterCode($length=6) {
        $builder = new PhraseBuilder();
        return $builder->build($length);
    }

    public function doActive($activeCode) {
        if($this->is_active) {
            return true;
        }
        if(hash('md5', $this->created_at) == $activeCode) {
            $this->is_active = true;
            try {
                $this->saveOrFail();
                return true;
            } catch (\Throwable $ex) {
                Log::error($ex->getMessage());
                return false;
            }
        }
        return true;
    }

    /**
     * Get the name of the unique identifier for the user.
     *
     * @return string
     */
    public function getAuthIdentifierName()
    {
        return $this->primaryKey;
    }

    /**
     * Get the unique identifier for the user.
     *
     * @return mixed
     */
    public function getAuthIdentifier()
    {
        // TODO: Implement getAuthIdentifier() method.
    }

    /**
     * Get the password for the user.
     *
     * @return string
     */
    public function getAuthPassword()
    {
        return $this->password;
    }

    /**
     * Get the token value for the "remember me" session.
     *
     * @return string
     */
    public function getRememberToken()
    {
        // TODO: Implement getRememberToken() method.
    }

    /**
     * Set the token value for the "remember me" session.
     *
     * @param  string $value
     * @return void
     */
    public function setRememberToken($value)
    {
        // TODO: Implement setRememberToken() method.
    }

    /**
     * Get the column name for the "remember me" token.
     *
     * @return string
     */
    public function getRememberTokenName()
    {
        // TODO: Implement getRememberTokenName() method.
    }

    public function personAccount() {
        return $this->hasOne(self::$NAME_SPACE.'\PersonAccount', self::ACCOUNT, self::ID);
    }
}


?>
