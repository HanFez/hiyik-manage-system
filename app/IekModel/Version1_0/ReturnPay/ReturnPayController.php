<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 4/4/17
 * Time: 2:32 PM
 */

namespace App\Http\Controllers\ReturnPay;


use App\Http\Controllers\Controller;
use App\Http\Controllers\ReturnPay\AlipaySubmit;
use App\Http\Controllers\ReturnPay\AliPayConfig;
use App\IekModel\Version1_0\ReFundBatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

//use Illuminate\Support\Facades\Hash;

class ReturnPayController extends Controller
{
    public function te(Request $request){
        $alipay_config = AliPayConfig::configData();
        /**************************请求参数**************************/

        //批次号，必填，格式：当天日期[8位]+序列号[3至24位]，如：201603081000001
        $batch_no = $request->input('WIDbatch_no');

        //退款笔数，必填，参数detail_data的值中，“#”字符出现的数量加1，最大支持1000笔（即“#”字符出现的数量999个）
        $batch_num = $request->input('WIDbatch_num');

        //退款详细数据，必填，格式（支付宝交易号^退款金额^备注），多笔请用#隔开
        $detail_data = $request->input('WIDdetail_data');

        /************************************************************/

//           operator_id          TEXT         NOT NULL,
//           reason_id            UUID         NOT NULL,
//           batch_no             TEXT         NOT NULL,
//           data                 TEXT         NOT NULL,
//           memo                 TEXT         NULL,
//           result               BOOL         DEFAULT 'f',
//           return_data          TEXT         NULL,
        //商户逻辑
        $reFundBatch = new ReFundBatch();
        $reFundBatch -> operator_id = '';
        $reFundBatch -> reason_id = '';


        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => trim($alipay_config['service']),
            "partner" => trim($alipay_config['partner']),
            "notify_url"	=> trim($alipay_config['notify_url']),
            "seller_user_id"	=> trim($alipay_config['seller_user_id']),
            "refund_date"	=> trim($alipay_config['refund_date']),
            "batch_no"	=> $batch_no,
            "batch_num"	=> $batch_num,
            "detail_data"	=> $detail_data,
            "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
        );

        //建立请求
        $alipaySubmit = new AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        return response($html_text);
    }
}