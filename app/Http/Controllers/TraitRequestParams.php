<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-6-23
 * Time: 上午10:33
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\IekModel\Version1_0\Manager;
use Illuminate\Support\Facades\Log;

trait TraitRequestParams
{
    public static $STUDLY_CASE = 'studly';
    public static $CAMEL_CASE = 'camel';
    public static $SNAKE_CASE = 'snake';
    public static $CASE_SUFFIX = '_case';

    /**To obtain params from request, we can accept three format name rule.
     * @param Request $request
     * @param $paramName
     * @param null $namedType
     * @return array|null|string
     */
    public function getRequestParam(Request $request, $paramName, $namedType = null) {
        $result = null;
        if(is_null($namedType)) {
            $studly = studly_case($paramName);
            $camel = camel_case($paramName);
            $snake = snake_case($paramName);
            if($request->has($paramName)) {
                $result = $request->input($paramName);
            } else if($request->has($studly)) {
                $result = $request->input($studly);
            } else if($request->has($camel)){
                $result = $request->input($camel);
            } else if($request->has($snake)) {
                $result = $request->input($snake);
            }
        } else {
            $param = call_user_func($namedType.self::$CASE_SUFFIX, array($paramName));
            if(!is_null($param) && !is_bool($param) && $request->has($param)) {
                $result = $request->input($param);
            }
        }
        return $result;
    }

    /**To obtain current logined user.
     * @return Model|null
     */
    public function getLoginPerson() {
        $person = null;
        if(!is_null(Auth::user())) {
            return Auth::user();
        }
        return $person;
    }


    /**To obtain current logined user's id.
     * @return int|null
     */
    public function getLoginPersonId() {
        $person = $this->getLoginPerson();
        if(!is_null($person)) {
            return $person->id;
        }
        return null;
    }

    /**To check login status.
     * @param int|null $uid
     * @return bool
     */
    public function checkLogin($uid = null) {
        $person = $this->getLoginPerson();
        if(is_null($person)) {
            return false;
        }
        if(!is_null($uid) && ($uid == $person->id)) {
            return true;
        }
        return false;
    }

    public function getRelation($model, $id, array $relationNames = []) {
        $entity = null;
        try {
            if (!is_null($id)) {
                $entity = $model::findOrFail($id);
            }
            if(!is_null($entity)) {
                foreach($relationNames as $relationName) {
                    $child = $entity->{$relationName};
                    if(!is_null($child)) {
                        $entity = $child;
                    } else {
                        $entity = null;
                        break;
                    }
                }
            }
        } catch (\Exception $ex) {
            $entity = null;
        }
        return $entity;
    }

}