<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/19/16
 * Time: 5:08 PM
 */
use App\IekModel\Version1_0\IekModel;

foreach ($field as $key => $value) {
    $transValue               = clone $value;
    $columnName               = $value->column_name;
    $transColumnName          = IekModel::doTrans($transValue, 'column_name', 'table');
    $value->column_name_trans = $transColumnName->column_name;
}
?>