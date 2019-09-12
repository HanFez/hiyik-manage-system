<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/12/8
 * Time: 11:32
 */

namespace App\Http\Controllers\AliPayTransfer;


class TransferConfig
{
    public static function configData(){
        //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓
        //合作身份者id，以2088开头的16位纯数字
        $alipay_config['partner']		= '2088421453038745';

        //安全检验码，以数字和字母组成的32位字符
        $alipay_config['key']			= 'n0wazruoerbh520deg8j11xx5n7zm5ch';

        //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑

        //签名方式 不需修改
        $alipay_config['sign_type']    = strtoupper('MD5');

        //字符编码格式 目前支持 gbk 或 utf-8
        $alipay_config['input_charset']= strtolower('utf-8');

        //ca证书路径地址，用于curl中ssl校验
        //请保证cacert.pem文件在当前文件夹目录中
        $alipay_config['cacert']    = getcwd().'\\cacert.pem';

        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport']    = 'http';

        //服务器异步通知页面路径
        $alipay_config['notify_url'] = "http://iekmanage.com/transferNotify";
        //需http://格式的完整路径，不允许加?id=123这类自定义参数

        //付款账号
        $alipay_config['email'] = 'tianpei@hiyik.com';
        //必填

        //付款账户名
        $alipay_config['account_name'] = '田沛';
        //必填，个人支付宝账号是真实姓名公司支付宝账号是公司名称

        return $alipay_config;
    }
}