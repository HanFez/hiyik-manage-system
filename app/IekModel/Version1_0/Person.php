<?php

namespace App\IekModel\Version1_0;

class Person extends IekModel {

    //
    protected $table = 'tblPersons';

    public function personPhone() {
        return $this->hasMany(self::$NAME_SPACE.'\PersonPhone','person_id', 'id');
    }

    public function personMail() {
        return $this->hasMany(self::$NAME_SPACE.'\PersonMail', 'person_id', 'id');
    }
    public function personAccount() {
        return $this->hasOne(self::$NAME_SPACE.'\PersonAccount', 'person_id', 'id');
    }

    public function personNick() {
        $query = $this->hasMany(self::$NAME_SPACE.'\PersonNick', 'person_id', 'id');
        return $query;
    }

    public function personAvatar() {
        return $this->hasMany(self::$NAME_SPACE.'\PersonAvatar', 'person_id', 'id');
    }

    public function personFamiliar() {
        return $this->hasMany(self::$NAME_SPACE.'\PersonFamiliar', 'person_id', 'id');
    }

    public function personFavor() {
        return $this->hasMany(self::$NAME_SPACE.'\PersonFavor', 'person_id', 'id');
    }

    public function personForbidden() {
        return $this->hasMany(self::$NAME_SPACE.'\PersonForbidden', 'person_id', 'id');
    }

    public function iwalls() {
        return $this->hasMany(self::$NAME_SPACE.'\Iwall', 'person_id', 'id');
    }

    public function personGag() {
        return $this->hasMany(self::$NAME_SPACE.'\PersonGag', 'person_id', 'id');
    }

    public function getNick() {
        return PersonNick::getActiveNick($this->id);
    }


    public function getAvatarUris() {
        return PersonAvatar::getActiveAvatarUris($this->id);
    }

    public function manageLog() {
        return $this->hasMany(self::$NAME_SPACE.'\ManageLogs', 'row_id', 'id');
    }

    public function getPersonProfile() {
        return static::personProfile($this->id);
    }
    public static function personProfile($uid) {
        $person = self::find($uid);
        if(is_null($person)) {
            return null;
        } else {
            $p = new \stdClass();
            $p->{self::ID} = $person->{self::ID};
            $p->{self::NICK} = $person->getNick();
            $p->{self::GENDER} = $person->getGender();
            $p->{self::AVATAR} = $person->getAvatar();
            $p->{self::SIGNATURE} = $person->getSignature();
            $p->{self::BIRTHDAY} = $person->{self::BIRTHDAY};
            $p->city = $person->getCity();
            return $p;
        }
    }

    public function getAvatar() {
        return PersonAvatar::getActiveAvatar($this->id);
    }
    public function getGender() {
        return PersonGender::getActiveGender($this->id);
    }
    public function getSignature() {
        return PersonSignature::getActiveSignature($this->id);
    }

    public function getCity() {
        return PersonAddress::getActiveCity($this->id);
    }

    public function getName() {
        return static::getActiveName($this->id);
    }

    public static function getActiveName($uid) {
        $name = PersonName::where(self::UID,$uid)
            ->with('name')
            ->where(IekModel::CONDITION)
            ->first();
        return $name;
    }
    public function publicationPerson() {
        return $this->hasMany(self::$NAME_SPACE.'\PublicationPerson', 'person_id', 'id');
    }

    public static function checkExist($uid){
        $count = self::where(self::ID,$uid)
            ->count();
        return $count == 0 ? false : true;
    }

    public function personIdentity(){
        return $this->belongsTo(self::$NAME_SPACE.'\PersonIdentity','id','person_id')
            ->where(IekModel::CONDITION);
    }

    public static function wealth($uid){
        $rewarded = self::getRewarded($uid);
        $reward = self::getReward($uid);
        $gain = self::getGain($uid);
        $cash = self::getCash($uid);
        $wealthOffset = self::getPurchasePay($uid);
        $recharge = self::getRecharge($uid);
        $rejectShipFee = self::getRejectShipFee($uid);
        $returnFee = self::getReturnFee($uid);
        $wealth = $reward - $rewarded + $gain - $cash - $wealthOffset + $recharge +$rejectShipFee + $returnFee;
        return $wealth;
    }

    public static function getReward($uid,$start=null,$end=null){
        $we = RewardWealthPay::where('to_id',$uid)
            ->where(IekModel::CONDITION);
        $te = RewardThirdPay::where(IekModel::CONDITION)
            ->where('to_id',$uid)
            ->where('status',true);
        if(!is_null($start) && !is_null($end)){
            $we = $we->whereBetween(IekModel::UPDATED_AT,[$start,$end]);
            $te = $te->whereBetween(IekModel::UPDATED_AT,[$start,$end]);
        }
        $we = $we->get();
        $te = $te->get();
        $fee = 0.0;
        if(!$we->isEmpty()){
            $we->each(function ($item,$k) use (&$fee){
                $fee += $item->fee;
            });
        }
        if(!$te->isEmpty()){
            $te->each(function ($item,$k) use (&$fee){
                $fee += $item->fee;
            });
        }
        return $fee;
    }

    public static function getRewarded($uid,$start=null,$end=null){
        $we = RewardWealthPay::where(IekModel::UID,$uid)
            ->where(IekModel::CONDITION);
        if(!is_null($start) && !is_null($end)){
            $we = $we->whereBetween(IekModel::UPDATED_AT,[$start,$end]);
        }
        $we = $we->get();
        $fee = 0.0;
        if(!$we->isEmpty()){
            $we->each(function ($item,$k) use (&$fee){
                $fee += $item->fee;
            });
        }
        return $fee;
    }

    public static function getGain($uid,$start=null,$end=null){
        $gain = GainPay::where(IekModel::UID,$uid)
            ->where(IekModel::CONDITION)
            ->where(IekModel::UID,$uid);
        if(!is_null($start) && !is_null($end)){
            $gain = $gain->whereBetween(IekModel::UPDATED_AT,[$start,$end]);
        }
        $gain = $gain->get();
        $fee = 0.0;
        if(!$gain->isEmpty()){
            $gain->each(function ($item,$k) use (&$fee){
                $fee += $item->fee;
            });
        }
        return $fee;
    }

    public static function getRecharge($uid,$start=null,$end=null){
        $recharge = RechargePay::where(IekModel::UID,$uid)
            ->where(IekModel::CONDITION)
            ->where('status',true)
            ->where(IekModel::UID,$uid);
        if(!is_null($start) && !is_null($end)){
            $recharge = $recharge->whereBetween(IekModel::UPDATED_AT,[$start,$end]);
        }
        $recharge = $recharge->get();
        $fee = 0.0;
        if(!$recharge->isEmpty()){
            $recharge->each(function ($item,$k) use (&$fee){
                $fee += $item->fee;
            });
        }
        return $fee;
    }

    public static function getCash($uid,$start=null,$end=null){
        $cash = CashRequest::where(IekModel::UID,$uid)
            ->where(IekModel::CONDITION);
        if(!is_null($start) && !is_null($end)){
            $cash = $cash->whereBetween(IekModel::UPDATED_AT,[$start,$end]);
        }
        $cash = $cash->where(function ($q){
            $q->where(function($q){
                $q->where('cash_audit',0)
                    ->where('pay_audit',0);
            })
                ->orWhere(function ($q){
                    $q->where('cash_audit',1)
                        ->where('pay_audit','!=',2);
                });
        })
            ->sum('fee');
        return (float)$cash;
    }

    public static function getPurchasePay($uid,$start=null,$end=null){
        $purchasePay = PurchaseWealthPay::where(IekModel::UID,$uid)
            ->where(IekModel::CONDITION)
            ->where(IekModel::UID,$uid);
        if(!is_null($start) && !is_null($end)){
            $purchasePay = $purchasePay->whereBetween(IekModel::UPDATED_AT,[$start,$end]);
        }
        $purchasePay = $purchasePay->get();
        $fee = 0.0;
        if(!$purchasePay->isEmpty()){
            $purchasePay->each(function ($item,$k) use (&$fee){
                $fee += $item->fee;
            });
        }
        return $fee;
    }

    public static function getRejectShipFee($uid,$start=null,$end=null){
        $rejectShipFee = ShipFeeReturnPay::where(IekModel::UID,$uid)
            ->where(IekModel::CONDITION)
            ->where(IekModel::UID,$uid);
        if(!is_null($start) && !is_null($end)){
            $rejectShipFee = $rejectShipFee->whereBetween(IekModel::UPDATED_AT,[$start,$end]);
        }
        $rejectShipFee = $rejectShipFee->get();
        $fee = 0.0;
        if(!$rejectShipFee->isEmpty()){
            $rejectShipFee->each(function ($item,$k) use (&$fee){
                $fee += $item->fee;
            });
        }
        return $fee;
    }

    public static function getReturnFee($uid,$start=null,$end=null){
        $returnFee = OrderReturnWealthPay::where(IekModel::UID,$uid)
            ->where(IekModel::CONDITION)
            ->where(IekModel::UID,$uid);
        if(!is_null($start) && !is_null($end)){
            $returnFee = $returnFee->whereBetween(IekModel::UPDATED_AT,[$start,$end]);
        }
        $returnFee = $returnFee->get();
        $fee = 0.0;
        if(!$returnFee->isEmpty()){
            $returnFee->each(function ($item,$k) use (&$fee){
                $fee += $item->fee;
            });
        }
        return $fee;
    }
}
