<?php
namespace App\IekModel\Version1_0;


class PersonMail extends IekModel {

    //
    protected $table = 'tblPersonMails';
    protected $guarded = [];

    public function mailDomain() {
        return $this->belongsTo(self::$NAME_SPACE.'\MailDomain', 'mail_domain_id');
    }

    public static function getMails($uid) {
        $mails = null;
        $relations = self::with('mailDomain.mail')
            ->with('mailDomain.domain')
            ->where(self::CONDITION)
            ->where(self::UID, $uid)
            ->orderBy(self::CREATED, 'desc')
            ->first();
        if($relations == null) {
            return $mails;
        }else{
            $mails = $relations->mailDomain;
        }
        return $mails;
    }

    public static function hasExist($uid, $mailDomainId) {
        $personMail = self::where(self::CONDITION)
            ->where(self::UID, $uid)
            ->where('mail_domain_id', $mailDomainId)
            ->orderBy(self::CREATED, 'desc')
            ->first();
        return $personMail;
    }

    public static function hasBound($mail, $domain) {
        $relation = self::whereHas('mailDomain', function($query) use($mail, $domain) {
                $query->whereHas('mail', function($query) use ($mail) {
                    $query->where('mail', $mail)
                        ->where(self::CONDITION);
                })->whereHas('domain', function($query) use ($domain) {
                    $query->where('domain', $domain)
                        ->where(self::CONDITION);
                });
            })->where(self::CONDITION)
            ->where('is_bind', true)
            ->first();
        if(!is_null($relation)
            && !is_null($relation->mailDomain->mail)
            && !is_null($relation->mailDomain->domain)) {
            return true;
        } else {
            return false;
        }
    }
}
