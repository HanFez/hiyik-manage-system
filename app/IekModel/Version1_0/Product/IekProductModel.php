<?php

namespace App\IekModel\Version1_0\Product;

use App\IekModel\Version1_0\IekModel;
use Illuminate\Support\Facades\DB;

class IekProductModel extends IekModel
{
    public  static $NAME_SPACE = 'App\IekModel\Version1_0\Product';

    public static function tableSchema() {
        $sql = sprintf('SELECT column_name, column_default, is_nullable, data_type, character_maximum_length
            FROM "viewTableSchemas" WHERE table_name=\'%s\' ORDER BY column_name ASC;',
            static::getDataTable());
        $columns = DB::select(DB::raw($sql));
        return $columns;
    }
    public static function getDataTable() {
        $temp = new static;
        return $temp->getTableName();
    }
}
