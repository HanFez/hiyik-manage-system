<?php
/**
 * Created by PhpStorm.
 * User: ticoo
 * Date: 16-3-31
 * Time: 上午10:58
 */

namespace App\IekModel\Version1_0;

use App\IekModel\Version1_0\Constants\ColumnDefine;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use PDO;
class IekModel extends Model implements ColumnDefine {
    public static $NAME_SPACE = 'App\IekModel\Version1_0';

    /*const ACTIVE = 'is_active';
    const REMOVED = 'is_removed';
    const CREATED = 'created_at';
    const UPDATED = 'updated_at';
    const TAKE = 'take';
    const SKIP = 'skip';
    const BEGIN_AT = 'begin_at';
    const END_AT = 'end_at';
    const RESULT = 'result';
    const TOTAL = 'total';
    const COUNT = 'count';
    const DATA = 'data';
    const TIMESTAMP = 'timestamp';
    const ID = 'id';
    const PID = 'publication_id';
    const UID = 'person_id';
    const CID = 'comment_id';
    const FID = 'folder_id';
    const IID = 'image_id';
    const TITLE = 'title';
    const TID = 'title_id';
    const RID = 'range_id';
    const DID = 'description_id';
    const TAG_ID = 'tag_id';
    const OID = 'operator_id';
    const CAN_SELL = 'can_sell';
    const ACTION = 'action';
    const CONTENT = 'content';
    const CONTENT_ID = 'content_id';
    const PUBLISH = 'is_publish';
    const INDEX = 'index';
    const NAME = 'name';
    const PARENT_ID = 'parent_id';
    const NICK = 'nick';
    const NICK_ID = 'nick_id';
    const PHONE = 'phone';
    const PHONE_ID = 'phone_id';
    const BIRTHDAY = 'birthday';
    const SIGNATURE = 'signature';
    const SIGNATURE_ID = 'signature_id';
    const GENDER = 'gender';
    const GENDER_ID = 'gender_id';
    const AVATAR = 'avatar';
    const AVATAR_ID = 'avatar_id';
    const TARGET_TYPE = 'target_type';
    const CODE = 'code';
    const DESC = 'description';
    const URI = 'uri';
    const ORDER = 'order';
    const HASH = 'md5';
    const DATE_FORMAT = 'Y-m-d';
    const TIME_FORMAT = 'H:i:s';
    const FROM = 'from';
    const TO = 'to';
    const INITIATOR = 'initiator';
    const CUSTOMER = 'customer';
    const IS_GROUP = 'is_group';
    const INITIATOR_REMOVED = 'initiator_removed';
    const CUSTOMER_REMOVED = 'customer_removed';
    const TYPE = 'type';
    const FORBIDDEN = 'forbidden';
    const REAID = 'reason_id';
    const AUTHOR_ID = 'author_id';
    const MEMO = 'memo';
    const STATUS = 'status';
    const ANNOUNCE_ID = 'announce_id';
    const CONVERSATION_ID = 'conversation_id';
    const LEVEL = 'level';
    const HITS = 'hits';
    const CODE_LENGTH = 6;
    const OFFICIAL = 'is_official';
    const ROLE_ID = 'role_id';
    const ROLE = 'role';
    const PRIVILEGE_ID = 'privilege_id';
    const TABLE_NAME = 'table_name';
    const BEHAVIOR = 'behavior';
    const MANAGER_ID = 'manager_id';
    const DATETIME_FORMAT  = self::DATE_FORMAT.' '.self::TIME_FORMAT;
    const CONDITION = [[self::ACTIVE, true], [self::REMOVED, false]];*/

    protected $casts = [
        'price' => 'float',
        'total_price' => 'float',
        'start_x' => 'float',
        'start_y' => 'float',
        'width' => 'float',
        'height' => 'float',
        'weight' => 'float',
        'crop_start_x' => 'float',
        'crop_start_y' => 'float',
        'crop_width' => 'float',
        'crop_height' => 'float',
        'figure' => 'float',
        'fee' => 'float',
        'min_fee' => 'float',
        'net_fee' => 'float',
        'platform_fee' => 'float',
        'wealth_offset' => 'float',
    ];
    protected $exclusive = [];
    public static $snakeAttributes = false;
    public $syncPrimaryKey = true;
    public $incrementing = false;
    /**To get schema of model.
     * @return array
     */
    public function getSchemaColumns() {
        $columns = [];
        if(Schema::hasTable($this->table)) {
            $columns = Schema::getColumnListing($this->table);
        }
        return $columns;
    }

    public static function tableSchema() {
        $sql = sprintf('SELECT column_name, column_default, is_nullable, data_type, character_maximum_length
            FROM "viewTableSchemas" WHERE table_name=\'%s\' ORDER BY column_name ASC;',
            static::getDataTable());
        $columns = DB::select(DB::raw($sql));
        return $columns;
    }

    public function exists($extra = []) {
        $condition = $extra;
        foreach ($this->exclusive as $key) {
            $temp = [$key, '=', $this->{$key}];
            array_push($condition, $temp);
        }
        if(empty($condition)) {
            return false;
        }
        $count = static::where($condition)->count();
        if($count > 0) {
            return true;
        } else {
            return false;
        }
    }

    public static function getTables() {
        $sql = 'SELECT tablename FROM "viewTables";';
        $result = DB::select(DB::raw($sql));
        if(!is_null($result) && !empty($result)) {
            return $result;
        }
        return null;
    }

    public function checkTable($tableName){
        $tables = $this::getTables();
        foreach($tables as $table){
            if($table->tablename == $tableName){
                return true;
            }
        }
        return false;
    }

    public function getAllRecord($tableName){
        $tableCheck = $this::checkTable($tableName);
        if(!$tableCheck){
            return false;
        }
        $record = DB::table($tableName);
        return $record;
    }

    public static function getDataTable() {
        $temp = new static;
        return $temp->getTableName();
    }
    /**To get total count of records.
     * @return mixed
     */
    public static function getTotal() {
        return static::count();
    }

    /**Get count of records that satisfied condition.
     * @param array|null $condition
     * @return mixed
     */
    public static function getCount(array $condition = null) {
        if(is_null($condition)) {
            return 0;
        }
        return static::where($condition)->count();
    }

    /**To query records that satisfied condition.
     * @param array $condition
     * @return mixed
     */
    public static function getRecords(array $condition = array(['*']), $skip = null, $take = null) {
        if(!is_null($skip) && !is_null($take)) {
            $records = static::where($condition)
                ->skip($skip)
                ->take($take)
                ->get();
        } else {
            $records = static::where($condition)->get();
        }
        return $records;
    }

    /** To check exists according to the id.
     * @param $id
     * @return bool
     */
    public static function isExists($id) {
        $m = static::find($id);
        if(is_null($m)) {
            return false;
        } else {
            return true;
        }
    }

    public static function isDuplicated($condition) {
        $result = static::where($condition)->count();
        if($result > 0) {
            return true;
        }
        return false;
    }

    public function getTableName() {
        return $this->table;
    }
    /** To get primary key of model.
     * @return string
     */
    public function getPrimaryKey() {
        return $this->primaryKey;
    }
    /**
     * To translate local language for model that has property 'name'.
     * We must defile language file name same with model file name.
     * @param $item
     * @param $key
     * @return mixed
     */
    public static function doNameTrans($item, $key) {
        return self::doTrans($item, 'name');
    }

    /**
     * To translate local language for model that has property 'description'.
     * We must defile language file name same with model file name.
     * @param $item
     * @param $key
     * @return mixed
     */

    public static function doDescriptionTrans($item, $key) {
        return self::doTrans($item, 'description');
    }

    /**
     * Translate the class property value.
     * @param $item
     * @param $propertyName
     * @param $file
     * @param array $params The format params for translator usage.
     * @return mixed
     */
    public static function doTrans($item, $propertyName, $file = null, array $params=null) {
        if(is_null($file)) {
            $file = class_basename($item);
        }
        $tempParams = $params;
        if(!is_null($tempParams) && !empty($params)) {
            foreach ($tempParams as $key => $val) {
                $tempParams[$key] = trans($file.'.'.camel_case($val));
            }
            $item->{$propertyName} = trans($file.'.'.camel_case($item->{$propertyName}), $tempParams);
        } else {
            $item->{$propertyName} = trans($file.'.'.camel_case($item->{$propertyName}));
        }
        // No language string, output the original string.
        $item->{$propertyName} = self::splitFailedTrans($item->{$propertyName});
        return $item;
    }


    /**
     * @param $name
     * @param null $file The specified transform file, if null then use default trans function,
     * if specified, it should be 'folderName.fileName'.
     * @return array|string|\Symfony\Component\Translation\TranslatorInterface
     */
    public static function strTrans($name, $file = null) {
        if(!is_null($file)) {
            $file = str_replace('/', '.', $file);
            if(is_array($name)) {
                foreach ($name as $key => $val) {
                    $name[$key] = trans($file.'.'.camel_case($name[$key]));
                    $name[$key] = self::splitFailedTrans($name[$key]);
                }
            } else if (is_string($name)) {
                $name = trans($file . '.' . camel_case($name));
                $name = self::splitFailedTrans($name);
            }
        } else {
            if(is_array($name)) {
                foreach ($name as $key => $val) {
                    $name[$key] = trans(camel_case($name[$key]));
                    $name[$key] = self::splitFailedTrans($name[$key]);
                }
            } else if (is_string($name)) {
                $name = trans(camel_case($name));
                $name = self::splitFailedTrans($name);
            }
        }
        return $name;
    }
    /**If we can not get translated string of language, so we just output original string.
     * @param $trans
     * @return string
     */
    public static function splitFailedTrans($trans) {
        if(str_contains($trans, '.')) {
            $trans = substr($trans, strrpos($trans, '.') + 1);
        }
        return $trans;
    }
    
    public static function runSqlScript($path, $connect=null) {
        SqlScript::runScript($path);
    }

    public static function deleteList($model , $ids=[]){
        $result = $model::whereIn(IekModel::ID,$ids)
            ->update([
                IekModel::REMOVED => true
            ]);
        return $result;
    }

    public static function recoverList($model , $ids=[]){
        $result = $model::whereIn(IekModel::ID,$ids)
            ->update([
                IekModel::REMOVED => false
            ]);
        return $result;
    }

    public static function getAll($model){
        $list = $model::where(IekModel::CONDITION)
            ->get();
        return $list->isEmpty() ? null : $list;
    }
}