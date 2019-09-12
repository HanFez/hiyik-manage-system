<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/12/17
 * Time: 5:20 PM
 */

namespace App\IekModel\Version1_0\ProductTraceability;


use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;

class Product extends IekProductTraceabilityModel
{
    protected $table="tblProducts";

    public function productIntroduction() {
        return $this->hasMany(self::$NAME_SPACE.'\ProductIntroduction', 'product_id', self::ID)
            ->where(self::CONDITION);
    }

    public function shop() {
        return $this->hasOne(self::$NAME_SPACE.'\Shop', self::ID, 'shop_id')
            ->where(self::CONDITION);
    }

    public function produceParams() {
        return $this->hasOne(self::$NAME_SPACE.'\ProduceParam', 'product_no', 'no')
            ->where(self::CONDITION);
    }

    public function core() {
        return $this->hasOne(self::$NAME_SPACE.'\Core', self::ID, 'core_id')
            ->where(self::CONDITION);
    }

    public function image() {
        return $this->hasOne(self::$NAME_SPACE.'\Image', self::ID, 'image_id')
            ->where(self::CONDITION);
    }

    public static function createRecord($params,$pid=null){
        $err = new Error();
        if(!is_null($pid)){
            $product = self::where(IekModel::ID,$pid)
                ->where(IekModel::CONDITION)
                ->first();
            if(is_null($product)){
                $err->setError(Errors::NOT_FOUND);
                $err->setData('product');
                return $err;
            }
            ProductIntroduction::where('product_id',$pid)
                ->update([IekModel::REMOVED=>true]);
        }else{
            $exist = self::getProductByNo($params['no']);
            if(!is_null($exist)){
                $err->setError(Errors::EXIST);
                $err->setData('no');
                return $err;
            }
            $product = new self();
        }
        $product->name = $params['name'];
        $product->no = $params['no'];
        $product->image_id = $params['imageId'];
        $product->description = isset($params['description'])?$params['description']:null;
        $product->width = isset($params['width'])?$params['width']:0;
        $product->height = isset($params['height'])?$params['height']:0;
        $product->mount = isset($params['mount'])?$params['mount']:null;
        $product->core_id = isset($params['coreId'])?$params['coreId']:null;
        $product->border = isset($params['border'])?$params['border']:null;
        $product->frame = isset($params['frame'])?$params['frame']:null;
        $product->front = isset($params['front'])?$params['front']:null;
        $product->back = isset($params['back'])?$params['back']:null;
        $product->level = isset($params['level'])?$params['level']:null;
        $product->uri = isset($params['uri'])?$params['uri']:null;
        $product->shop_id = isset($params['shopId'])?$params['shopId']:null;
        $product->is_sell = isset($params['isSell'])?$params['isSell']:null;
//        $product->type = isset($params['type'])?$params['type']:null;
        $product->save();
        $err->setData($product);
        return $err;
    }

    public static function getProductByNo($no){
        $product = self::where('no',$no)
            ->first();
        return $product;
    }

    public static function getProductById($id){
        $product = self::where('id',$id)
            ->first();
        return $product;
    }

    public static function changeSell($id,$sell){
        $err = new Error();
        $product = self::where(IekModel::ID,$id)
            ->where(IekModel::CONDITION)
            ->first();
        if(is_null($product)){
            $err->setError(Errors::NOT_FOUND);
            return $err;
        }
        $product->is_sell = $sell;
        $product->save();
        $err->setData($product);
        return $err;
    }
}