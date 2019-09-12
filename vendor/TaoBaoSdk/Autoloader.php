<?php

class Autoloader{

  /**
     * 类库自动加载，写死路径，确保不加载其他文件。
     * @param string $class 对象类名
     * @return void
     */
    public static function autoload($class) {
//    if (!defined("TOP_SDK_WORK_DIR"))
//    {
//        define("TOP_SDK_WORK_DIR", "/tmp/");
//    }
//    if (!defined("TOP_SDK_DEV_MODE"))
//    {
//        define("TOP_SDK_DEV_MODE", true);
//    }
//
//    if (!defined("TOP_AUTOLOADER_PATH"))
//    {
//        define("TOP_AUTOLOADER_PATH", dirname(__FILE__));
//    }
        $name = $class;
        if(false !== strpos($name,'\\')){
          $name = strstr($class, '\\', true);
        }

        $filename = TOP_AUTOLOADER_PATH."/top/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }

        $filename = TOP_AUTOLOADER_PATH."/top/request/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }

        $filename = TOP_AUTOLOADER_PATH."/top/domain/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }

        $filename = TOP_AUTOLOADER_PATH."/aliyun/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }

        $filename = TOP_AUTOLOADER_PATH."/aliyun/request/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }

        $filename = TOP_AUTOLOADER_PATH."/aliyun/domain/".$name.".php";
        if(is_file($filename)) {
            include $filename;
            return;
        }
    }
}

spl_autoload_register('Autoloader::autoload');
?>