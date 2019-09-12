<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/18/17
 * Time: 9:30 AM
 */

namespace App\IekModel\Version1_0\ProductTraceability;


use App\IekModel\Version1_0\IekModel;

class RealProduct extends IekProductTraceabilityModel
{
    protected $table="tblRealProducts";

    public function product() {
        return $this->hasOne(self::$NAME_SPACE.'\Product', self::ID, 'product_id')
            ->where(self::CONDITION);
    }

    public function QRImage() {
        return $this->hasMany(self::$NAME_SPACE.'\RealProductQRImage', 'real_product_id', self::ID)
            ->where(self::CONDITION);
    }

    public function produced(){
        return $this->hasOne(self::$NAME_SPACE.'\RealProduct', 'from_no', 'no')
            ->where(self::CONDITION);
    }

    public function orderRealProduct(){
        return $this->hasOne(self::$NAME_SPACE.'\TBOrderRealProduct', 'real_product_no', 'user_no')
            ->where(self::CONDITION);
    }

    public static function createRecord($params){
        $record = new self();
        $record->product_id = $params['productId'];
        $record->no = $params['no'];
        $record->user_no = $params['userNo'];
        $record->status = $params['status'];
        $record->from_no = isset($params['from_no'])?$params['from_no']:null;
        $record->save();
        return $record;
    }

    public static function getProductByNo($type,$no){
        $product = self::where(IekModel::CONDITION)
            ->where($type,$no)
            ->first();
        return $product;
    }

    public static function getNo($pNo){
        $end = self::getNoEnd();
        $no = date("Ymd").'-'.$pNo.'-'.$end;
        $userNo = $end.substr(date("Ymd"),2,6);
        $exist = self::getProductByNo('no',$no);
        if(!is_null($exist)){
            $no = self::getNo($pNo);
        }
        $exist = self::getProductByNo('user_no',$userNo);
        if(!is_null($exist)){
            $no = self::getNo($pNo);
        }
        $data = new \stdClass();
        $data->no = $no;
        $data->userNo = $userNo;
        return $data;
    }

    public static function getNoEnd($length = 5, $charset = 'abcdefghijklmnpqrstuvwxyz123456789')
    {
        $phrase = '';
        $chars = str_split($charset);

        for ($i = 0; $i < $length; $i++) {
            $phrase .= $chars[array_rand($chars)];
        }

        return strtoupper($phrase);
    }

}