<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/10/16
 * Time: 9:49 AM
 */
use App\IekModel\Version1_0\IekModel;
$data = $result->data;
if(!is_null($data)) {
    $data = json_decode(json_encode($data));
    $index = 0;
    $system = [];
    foreach ($data as $key=>$tag) {
        if($tag->level === 0) {
            $system[$index] = $tag;
            $system[$index]->content = [];
            $index ++;
        } else {
            continue;
        }
    }
    foreach ($data as $key => $tag) {
        if($tag->level === 1) {
            $subInx = 0;
            foreach ($system as $inx => $val) {
                if($val->id === $tag->parent_id) {
                    $subInx = $inx;
                }
            }
            array_push($system[$subInx]->content, $tag);
        } else {
            continue;
        }
    }
    //sort
    for ($i = 0; $i < count($system) ; $i ++) {
        for ($k = count($system) - 1; $k > $i ; $k --) {
            if ($system[$k]->id < $system[$k-1]->id) {
                $temp = $system[$k];
                $system[$k] = $system[$k-1];
                $system[$k-1] = $temp;
            }
        }
    }
    $type = 'tags';
}
$transFile = 'Tag';
?>
@include('layout/accordionGroup')
