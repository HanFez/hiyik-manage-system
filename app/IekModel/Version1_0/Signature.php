<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\IekModel\Version1_0;

class Signature extends IekModel {

    protected $table = "tblSignatures";
    protected $guarded = [];
//    protected $hidden = ['is_active', 'is_removed', 'updated_at', 'created_at'];

    public function forbidden() {
        return $this->hasMany(self::$NAME_SPACE.'\SignatureForbidden', self::SIGNATURE_ID,self::ID);
    }

    public function manageLog() {
        return $this->hasMany(self::$NAME_SPACE.'\ManageLogs', self::ROW_ID,self::ID);
    }

    public static function hasExist($content) {
        $signature = self::where(self::CONDITION)
            ->where(self::SIGNATURE, $content)
            ->orderBy(self::CREATED, 'desc')
            ->first();
        return $signature;
    }

    public static function insertSignature($params) {
        $signature = self::hasExist($params->{self::SIGNATURE});
        if(is_null($signature)) {
            $signature = new self();
            $signature->{self::SIGNATURE} = $params->{self::SIGNATURE};
            $result = $signature->save();
            if ($result) {
                return $signature;
            } else {
                return null;
            }
        } else {
            return $signature;
        }
    }

    public static function checkExist($sid){
        $count = self::where(self::ID,$sid)
            ->count();
        return $count == 0 ? false : true;
    }
}
