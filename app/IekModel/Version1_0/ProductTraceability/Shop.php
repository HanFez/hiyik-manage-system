<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/12/17
 * Time: 5:21 PM
 */

namespace App\IekModel\Version1_0\ProductTraceability;


use App\IekModel\Version1_0\IekModel;

class Shop extends IekProductTraceabilityModel
{
    protected $table="tblShops";

    public function product() {
        return $this->hasMany(self::$NAME_SPACE.'\Product', 'shop_id', self::ID)
            ->where(self::CONDITION);
    }

    public static function createRecord($params,$id = null){
        if(is_null($id)){
            $exist = self::checkExist($params);
            if(!is_null($exist)){
                return $exist;
            }
            $shop = new self();
        }else{
            $shop = self::where(IekModel::ID,$id)
                ->first();
            if(is_null($shop)){
                return $shop;
            }
        }
        $shop->name = $params['name'];
        $shop->description = isset($params['description'])?$params['description']:null;
        $shop->platform = isset($params['platform'])?$params['platform']:null;
        $shop->uri = isset($params['uri'])?$params['uri']:null;
        $shop->save();
        return $shop;
    }

    public static function checkExist($params){
        $shop = self::where(IekModel::CONDITION)
            ->where('name',$params['name'])
            ->where('description',isset($params['description'])?$params['description']:null)
            ->where('platform',isset($params['platform'])?$params['platform']:null)
            ->where('uri',isset($params['uri'])?$params['uri']:null)
            ->first();
        return $shop;
    }

}