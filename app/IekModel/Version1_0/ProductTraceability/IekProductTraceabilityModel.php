<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/12/17
 * Time: 5:11 PM
 */

namespace App\IekModel\Version1_0\ProductTraceability;


use App\IekModel\Version1_0\Constants\ColumnDefine;
use App\IekModel\Version1_0\IekModel;
use Illuminate\Support\Facades\DB;

class IekProductTraceabilityModel extends IekModel
{
    public static $NAME_SPACE = 'App\IekModel\Version1_0\ProductTraceability';
    protected $connection = 'pgsql_product_traceability';

    public function getTableName() {
        return $this->table;
    }

    public static function getDataTable() {
        $temp = new static;
        return $temp->getTableName();
    }

    public static function tableSchema() {
        $sql = sprintf('SELECT column_name, column_default, is_nullable, data_type, character_maximum_length
            FROM "viewTableSchemas" WHERE table_name=\'%s\';', //ORDER BY column_name ASC
            static::getDataTable());
        $columns = DB::connection('pgsql_product_traceability')->select(DB::raw($sql));
        return $columns;
    }

}