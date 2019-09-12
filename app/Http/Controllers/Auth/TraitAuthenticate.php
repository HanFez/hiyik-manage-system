<?php
/**
 * Created by PhpStorm.
 * User: hunter
 * Date: 16-10-3
 * Time: 上午12:05
 */

namespace App\Http\Controllers\Auth;
use App\IekModel\Version1_0\Constants\PersonAction;
use App\IekModel\Version1_0\Person;
use App\IekModel\Version1_0\PersonSocial;
use App\IekModel\Version1_0\ShareFeedback;
use App\IekModel\Version1_0\ShareFeedbackNotify;
use App\IekModel\Version1_0\SocialAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use App\IekModel\Version1_0\Account;
use App\IekModel\Version1_0\AccountType;
use App\IekModel\Version1_0\Constants\AuthenticateType;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\PersonMail;
use App\IekModel\Version1_0\PersonPhone;
use App\IekModel\Version1_0\PersonAccount;
use App\IekModel\Version1_0\FilterKeyword;
use App\IekModel\Version1_0\Nick;
use App\IekModel\Version1_0\PersonNick;
use App\IekModel\Version1_0\Phone;
use App\IekModel\Version1_0\Mail;
use App\IekModel\Version1_0\Domain;
use App\IekModel\Version1_0\MailDomain;
use App\IekModel\Version1_0\Range;

trait TraitAuthenticate {
    static $MAIL_REGEX = '/^[a-z0-9]+([._\\-]*[a-z0-9])*@([a-z0-9]+[-a-z0-9]*[a-z0-9]+.){1,63}[a-z0-9]+$/i';
    static $PHONE_REGEX = '/^1[3|4|5|7|8]\d{9}$/';
    public function getAccountByPhone($phoneNum) {
        $account = null;
        $account = Account::whereHas('personAccount.person.personPhone',
            function($query) use ($phoneNum) {
            $query->where('is_bind', true)
                ->whereHas('phone', function ($query) use ($phoneNum) {
                    $query->where('phone', $phoneNum);
                });
            })->where(Account::CONDITION)
            ->orderBy(IekModel::CREATED, 'desc')
            ->first();
        return $account;
    }

    public function getAccountByMail($mail, $domain) {
        $account = null;
        $account = Account::where(Account::CONDITION)
            ->whereHas('personAccount.person.personMail', function($query) use ($mail, $domain) {
                $query->where(IekModel::CONDITION)
                    ->where('is_bind', true)
                    ->whereHas('mailDomain.mail', function($query) use ($mail) {
                        $query->where('mail', $mail);
                    })->whereHas('mailDomain.domain', function($query) use ($domain){
                        $query->where('domain', $domain);
                    });
            })->orderBy(IekModel::CREATED, 'desc')
            ->first();
        return $account;
    }

    public function getAccountByNick($nick) {
        //
    }
    public function getPersonIdByAccountId($accountId) {
        $person = null;
        $person = PersonAccount::where('account', $accountId)
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED, 'desc')
            ->first();
        if(!is_null($person)) {
            return $person->person_id;
        } else {
            return null;
        }
    }
    public function checkLoginParams($params) {
        $err = new Error();
        $account = $params->account;
        if(preg_match(static::$MAIL_REGEX, $account)){
            $accountType = AccountType::getByName(AuthenticateType::MAIL);
            if(!is_null($accountType)) {
                $params->type = $accountType;
            } else {
                $err->setError(Errors::INVALID_DATA);
                return $err;
            }
            $temp = explode('@', $account);
            $mail = $temp[0];
            $domain = $temp[1];
            $params->mail = $mail;
            $params->domain = $domain;
            if(!PersonMail::hasBound($mail, $domain)) {
                $err->setError(Errors::INVALID_ACCOUNT);
                return $err;
            } else {
                $err->setData($params);
                return $err;
            }
        } else if(preg_match(static::$PHONE_REGEX, $account)){
            $accountType = AccountType::getByName(AuthenticateType::PHONE);
            if(!is_null($accountType)) {
                $params->type = $accountType;
            } else {
                $err->setError(Errors::INVALID_DATA);
                return $err;
            }
            $params->phone = $account;
            if(!PersonPhone::hasBound($account)) {
                $err->setError(Errors::INVALID_ACCOUNT);
                return $err;
            } else {
                $err->setData($params);
                return $err;
            }
        } else{
            $err->setError(Errors::INVALID_ACCOUNT);
            return $err;
        }
    }

    public function checkRegisterParams(&$params) {
        $err = new Error();
        $account = $params->account;
        if(!$this->checkRegisterCode($account, $params->registerCode)) {
            $err->setError(Errors::INVALID_VERIFY);
            return $err;
        }
        if($params->password != $params->confirm) {
            $err->setError(Errors::INVALID_VERIFY);
            return $err;
        }
        $filter = FilterKeyword::filter($params->nick);
        if(!$filter->isPassed) {
            $err->setError(Errors::FILTER_FAILED);
            return $err;
        }
        $hasExist = Nick::hasExist($params->nick);
        if(!is_null($hasExist)) {
            if(PersonNick::hasUsed($hasExist->id)) {
                $err->setError(Errors::EXIST);
                return $err;
            }
        }
        if(preg_match(static::$MAIL_REGEX, $account)){
            $accountType = AccountType::getByName(AuthenticateType::MAIL);
            if(!is_null($accountType)) {
                $params->type = $accountType;
            } else {
                $err->setError(Errors::INVALID_DATA);
                return $err;
            }
            $temp = explode('@', $account);
            $mail = $temp[0];
            $domain = $temp[1];
            $params->mail = $mail;
            $params->domain = $domain;
            if(PersonMail::hasBound($mail, $domain)) {
                $err->setError(Errors::EXIST);
                return $err;
            } else {
                $err->setData($params);
                return $err;
            }
        } else if(preg_match(static::$PHONE_REGEX, $account)){
            $accountType = AccountType::getByName(AuthenticateType::PHONE);
            if(!is_null($accountType)) {
                $params->type = $accountType;
            } else {
                $err->setError(Errors::INVALID_DATA);
                return $err;
            }
            if(PersonPhone::hasBound($account)) {
                $err->setError(Errors::EXIST);
                return $err;
            } else {
                $err->setData($params);
                return $err;
            }
        } else{
            $err->setError(Errors::INVALID_DATA);
            return $err;
        }
    }
    function checkRegisterCode($account, $code) {

        if (!Cache::has($account)) {
            return false;
        }
        $registerCode = Cache::get($account);
        if ($registerCode == $code) {
            return true;
        }
        return false;
    }

    public function registerAccount($params) {
        $err = new Error();
        try {
            DB::beginTransaction();
            $person = new Person();
            $person->is_active = true;
            $person->is_removed = false;
            $person->range_id = Range::getIdByName(Range::ALL);
            $result = $person->save();
            $account = new Account();
            $account->id = Account::generator();
            $account->password = bcrypt($params->password);
            $account->register_type = $params->type->id;
            $account->save();
            $personAccount = new PersonAccount();
            $personAccount->person_id = $person->id;
            $personAccount->account = $account->id;
            $personAccount->save();
            $nick = Nick::hasExist($params->nick);
            if(is_null($nick)) {
                $nick = new Nick();
                $nick->nick = $params->nick;
                $nick->save();
            }
            $personNick = new PersonNick();
            $personNick->person_id = $person->id;
            $personNick->nick_id = $nick->id;
            $personNick->save();
            $rangeSelf = Range::where(IekModel::NAME, 'self')
                ->where(IekModel::CONDITION)
                ->first();
            switch($params->type->name) {
                case AuthenticateType::MAIL:
                    $mail = Mail::hasExist($params->mail);
                    if(is_null($mail)) {
                        $mail = new Mail();
                        $mail->mail = $params->mail;
                        $mail->save();
                    }
                    $domain = Domain::hasExist($params->domain);
                    if(is_null($domain)) {
                        $domain = new Domain();
                        $domain->domain = $params->domain;
                        $domain->save();
                    }
                    $mailDomain = MailDomain::hasExist($mail->id, $domain->id);
                    if(is_null($mailDomain)) {
                        $mailDomain = new MailDomain();
                        $mailDomain->mail_id = $mail->id;
                        $mailDomain->domain_id = $domain->id;
                        $mailDomain->save();
                    }
                    $personMail = PersonMail::hasExist($person->id, $mailDomain->id);
                    if(is_null($personMail) || !$personMail->is_bind) {
                        $personMail = new PersonMail();
                        $personMail->person_id = $person->id;
                        $personMail->mail_domain_id = $mailDomain->id;
                        $personMail->is_bind = true;
                        $personMail->range_id = $rangeSelf->id;
                        $personMail->save();
                        $data = new \stdClass();
                        $data->person_id = $person->id;
                        $data->nick = $nick->nick;
                        $err->setData($data);
                    } else {
                        $err->setError(Errors::EXIST);
                    }
                    break;
                case AuthenticateType::PHONE:
                    $phone = Phone::hasExist($params->account);
                    if(is_null($phone)) {
                        $phone = new Phone();
                        $phone->phone = $params->account;
                        $phone->save();
                    }
                    $personPhone = PersonPhone::hasBound($params->account);
                    if($personPhone) {
                        $err->setError(Errors::EXIST);
                    } else {
                        $personPhone = new PersonPhone();
                        $personPhone->person_id = $person->id;
                        $personPhone->phone_id = $phone->id;
                        $personPhone->is_bind = true;
                        $personPhone->range_id = $rangeSelf->id;
                        $personPhone->save();
                        $data = new \stdClass();
                        $data->person_id = $person->id;
                        $data->nick = $nick->nick;
                        $err->setData($data);
                    }
                    break;
                case AuthenticateType::SOCIAL:
                    $socialAccount = SocialAccount::isExist($params->account,$params->socialPlatform ->id);
                    if(is_null($socialAccount)){
                        $socialAccount = new SocialAccount();
                        $socialAccount->account = $params->account;
                        $socialAccount->social_platform_id = $params->socialPlatform ->id;
                        $socialAccount->save();
                    }
                    $personSocial = new PersonSocial();
                    $personSocial->person_id = $person->id;
                    $personSocial->social_account_id = $socialAccount->id;
                    $personSocial->range_id = $rangeSelf->id;
                    $personSocial->save();
                    $data = new \stdClass();
                    $data->person_id = $person->id;
                    $data->nick = $nick->nick;
                    $data->account = $account->id;
                    $err->setData($data);
                    break;
            }
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            $err->exception($ex);
        }
        if(session()->has('feedbackId')){
            $shareFeedbackId = session('feedbackId');
            session()->forget('feedbackId');
            ShareFeedback::where(IekModel::ID,$shareFeedbackId)
                ->where(IekModel::CONDITION)
                ->update([IekModel::UID=>$person->id]);
            $sharePerson = ShareFeedback::getSharePerson($shareFeedbackId);
            if(!is_null($sharePerson) && !is_null($sharePerson->person_id)){
                $params = new \stdClass();
                $params->action = PersonAction::FEEDBACK_REGISTER;
                $params->fromId = $person->id;
                $params->toId = $sharePerson->person_id;
                $params->targetId = $sharePerson->person_id;
                $params->originId = $sharePerson->share_id;
                $args = new NotifyEventArguments(null, ShareFeedbackNotify::class, $params);
                event(new NotifyEvent($args));
            }
        }
        return $err;
    }
}