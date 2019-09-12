<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 4/4/17
 * Time: 3:12 PM
 */

namespace App\Http\Controllers\ReturnPay;


class AliPayConfig
{
    public static function configData(){
        //↓↓↓↓↓↓↓↓↓↓请在这里配置您的基本信息↓↓↓↓↓↓↓↓↓↓↓↓↓↓↓

        // 合作身份者ID，签约账号，以2088开头由16位纯数字组成的字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm

        $alipay_config = [];
        $alipay_config['partner']= '2088421453038745';

        // 卖家支付宝账号，以2088开头由16位纯数字组成的字符串，一般情况下收款账号就是签约账号
        $alipay_config['seller_user_id']=$alipay_config['partner'];

        // MD5密钥，安全检验码，由数字和字母组成的32位字符串，查看地址：https://b.alipay.com/order/pidAndKey.htm
        $alipay_config['key']= 'n0wazruoerbh520deg8j11xx5n7zm5ch';

        // 服务器异步通知页面路径，需http://格式的完整路径，不能加?id=123这类自定义参数,必须外网可以正常访问
        $alipay_config['notify_url']="http://商户网关网址/refund_fastpay_by_platform_pwd-PHPGBK/notify_url.php";

        // 签名方式
        $alipay_config['sign_type']    = strtoupper('MD5');

        // 退款日期 时间格式 yyyy-MM-dd HH:mm:ss
        //date_default_timezone_set('PRC');//设置当前系统服务器时间为北京时间，PHP5.1以上可使用。
        $alipay_config['refund_date']=date("Y-m-d H:i:s",time());;

        // 调用的接口名，无需修改
        $alipay_config['service']='refund_fastpay_by_platform_pwd';

        //字符编码格式 目前支持 gbk 或 utf-8
        $alipay_config['input_charset']= strtolower('utf-8');

        //ca证书路径地址，用于curl中ssl校验
        //请保证cacert.pem文件在当前文件夹目录中
        $alipay_config['cacert']    = getcwd().'\\cacert.pem';

        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $alipay_config['transport']  = 'http';

        //↑↑↑↑↑↑↑↑↑↑请在这里配置您的基本信息↑↑↑↑↑↑↑↑↑↑↑↑↑↑↑
        return $alipay_config;
    }
}