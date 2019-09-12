<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/12/17
 * Time: 5:16 PM
 */

namespace App\IekModel\Version1_0\ProductTraceability;


use App\IekModel\Version1_0\IekModel;

class Core extends IekProductTraceabilityModel
{
    protected $table="tblCores";

    public function publication() {
        return $this->hasOne(self::$NAME_SPACE.'\Publication', self::ID, 'publication_id')
            ->where(self::CONDITION);
    }

    public function product() {
        return $this->hasOne(self::$NAME_SPACE.'\Product', 'core_id', self::ID)
            ->where(self::CONDITION);
    }

    public static function createRecord($params){
        $exist = self::checkExist($params);
        if(!is_null($exist)){
            return $exist;
        }
        $core = new self();
//        $core->product_id = $params['productId'];
        $core->publication_id = $params['publicationId'];
        $core->material = isset($params['material'])?$params['material']:null;
        $core->pattern = isset($params['pattern'])?$params['pattern']:null;
        $core->ink = isset($params['ink'])?$params['ink']:null;
        $core->height = isset($params['height'])?$params['height']:0;
        $core->width = isset($params['width'])?$params['width']:0;
        $core->save();
        return $core;
    }

    public static function checkExist($params){
        $exist = self::where(IekModel::CONDITION)
            ->where('publication_id',$params['publicationId'])
            ->where('material',isset($params['material'])?$params['material']:null)
            ->where('pattern', isset($params['pattern'])?$params['pattern']:null)
            ->where('ink',isset($params['ink'])?$params['ink']:null)
            ->where('height',isset($params['height'])?$params['height']:0)
            ->where('width',isset($params['width'])?$params['width']:0)
            ->first();
        return $exist;
    }

}