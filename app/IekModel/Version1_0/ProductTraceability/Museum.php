<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/12/17
 * Time: 5:19 PM
 */

namespace App\IekModel\Version1_0\ProductTraceability;


use App\IekModel\Version1_0\IekModel;

class Museum extends IekProductTraceabilityModel
{
    protected $table="tblMuseums";

    public function publication() {
        return $this->hasMany(self::$NAME_SPACE.'\Publication','museum_id', self::ID)
            ->where(self::CONDITION);
    }

    public static function createRecord($params,$id=null){
        if(!is_null($id)){
            $museum = self::where(IekModel::ID,$id)
                ->first();
            if(is_null($museum)){
                return $museum;
            }
        }else {
            $exist = self::checkExist($params);
            if(!is_null($exist)){
                return $exist;
            }
            $museum = new self();
        }
        $museum->name = $params['name'];
        $museum->description = isset($params['description'])?$params['description']:null;
        $museum->lang = isset($params['lang'])?$params['lang']:null;
        $museum->save();
        return $museum;
    }

    public static function checkExist($params){
        $exist = self::where(IekModel::CONDITION)
            ->where('name',$params['name'])
            ->where('description',isset($params['description'])?$params['description']:null)
            ->where('lang',isset($params['lang'])?$params['lang']:null)
            ->first();
        return $exist;
    }

}