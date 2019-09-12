<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/10/16
 * Time: 9:49 AM
 */
use App\IekModel\Version1_0\IekModel;
$system = $result->data;
$type = 'settings';
foreach ($system as $content) {
    $content = IekModel::doTrans($content, 'name', 'SystemSetting');
    foreach ($content->content as $value) {
        if(isset($value->name)) {
            $value = IekModel::doTrans($value, 'name', 'SystemSetting');
        }
    }
}
$transFile = 'SystemSetting';
?>
@include('layout/accordionGroup')