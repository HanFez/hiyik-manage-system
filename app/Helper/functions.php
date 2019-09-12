<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/13
 * Time: 10:24
 */
function test(){
    echo "测试帮助函数引入";
}
/**
 * rgba转int
 * param r,g,b,a
 */
function turnInt($R,$G,$B,$A){
    $color = 0;
    if($A>100 || $A<0){
        return  "Please input data between 0-100";
    }
    $color = $color+(($R<<24) | ($G<<16) | ($B<<8)|$A);
    return $color;
}
/**
 * int转rgba
 * param num
 */
function turnRgba($num){
    $arr = [];
    for($i=3;$i>=0;$i--){
        $arr[] = $num>>8*$i & 0xFF;
    }

    if($arr[3]>100){
        $arr[3] = 100;
    }
    return $arr;
}
?>