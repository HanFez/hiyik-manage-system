<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/4/21
 * Time: 12:27
 */
namespace App\IekModel\Utils;

use TaoBaoSdk\Top\TopClient;
use TaoBaoSdk\Top\AlidayuRequest\AlibabaAliqinFcSmsNumSendRequest;
use Illuminate\Support\Facades\Log;

class AlidayuSms implements SmsSender {
    const CONFIG = 'alidayu';
    const APPKEY = '23405694';
    const SECRET = '17ad515a3d93c88d710b99bcf6f2aa26';
    const PRODUCT_WEB = '海艺客网站[www.hiyik.com]';
    const ALI_TEMPLATES = [
        VerifyAction::ACT_IDENTIFY =>'SMS_12170910',
        VerifyAction::ACT_TEST => 'SMS_12170909',
        VerifyAction::ACT_LOGIN_CONFIRM => 'SMS_12170908',
        VerifyAction::ACT_LOGIN_WARN => 'SMS_12170907',
        VerifyAction::ACT_REGISTER => 'SMS_12170906',
        VerifyAction::ACT_ACTIVITY => 'SMS_12170905',
        VerifyAction::ACT_CHANGE_PASSWORD => 'SMS_12170904',
        VerifyAction::ACT_CHANGE_INFO => 'SMS_12170903',
    ];
    const PARAMS = [
        'SMS_12170910' => ['code', 'product',],
        'SMS_12170909' => ['customer',],
        'SMS_12170908' => ['code', 'product',],
        'SMS_12170907' => ['code', 'product',],
        'SMS_12170906' => ['code', 'product',],
        'SMS_12170905' => ['code', 'product', 'item',],
        'SMS_12170904' => ['code', 'product',],
        'SMS_12170903' => ['code', 'product',],
    ];
    const ALI_SIGNS = [
        VerifyAction::ACT_TEST => '大鱼测试',
        VerifyAction::ACT_ACTIVITY => '活动验证',
        VerifyAction::ACT_CHANGE => '变更验证',
        VerifyAction::ACT_LOGIN => '登录验证',
        VerifyAction::ACT_REGISTER => '注册验证',
        VerifyAction::ACT_IDENTIFY => '身份验证',
        VerifyAction::ACT_CHANGE_PASSWORD => '变更验证',
        VerifyAction::ACT_CHANGE_INFO => '变更验证',

    ];

    const FORMAL_TEMPLATES = [
        'SMS_' => [],
    ];
    private $appKey = null;
    private $secret = null;
    private $sessionKey = null;
    public $template = null;
    public $extend = null;
    public $type = 'normal';
    public $sign = null;
    private $client = null;

    public function __construct() {
        $this->appKey = config('sms.'.static::CONFIG, static::APPKEY);
        $this->secret = config('sms.'.static::CONFIG, static::SECRET);
        $this->template = static::ALI_TEMPLATES[VerifyAction::ACT_REGISTER];
        $this->sign = static::ALI_SIGNS[VerifyAction::ACT_REGISTER];
        $this->initialInterface($this->appKey, $this->secret);
    }

    public function send($to, $content)
    {
        $req = new AlibabaAliqinFcSmsNumSendRequest;
        $req->setExtend($this->extend);
        $req->setSmsType($this->type);
        $req->setSmsFreeSignName($this->sign);
        $req->setSmsParam(json_encode($content));
        $req->setRecNum($to);
        $req->setSmsTemplateCode($this->template);
        $resp = $this->client->execute($req);
        return $resp;
    }

    public function initialInterface($appKey, $secret, $sessionKey = null)
    {
        $c = new TopClient();
        $c->appkey = $appKey;
        $c->secretKey = $secret;
        $this->client = $c;
    }

    public function isReady() {
        if(is_null($this->appKey) || is_null($this->secret)) {
            return false;
        } else if(is_null($this->template) || is_null($this->sign)){
            return false;
        } else if(is_null($this->type) || is_null($this->client)) {
            return false;
        } else {
            return true;
        }
    }

    public function setTemplate($act) {
        $this->template = self::ALI_TEMPLATES[$act];
        $this->sign = self::ALI_SIGNS[$act];
    }
}