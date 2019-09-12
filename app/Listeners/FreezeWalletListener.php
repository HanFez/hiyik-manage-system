<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/4/21
 * Time: 12:25
 */
namespace App\Listeners;

use App\Events\FreezeWallet;
use App\IekModel\Utils\AlidayuSms;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class FreezeWalletListener
{
    protected $mailer;
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param  FreezeWallet  $event
     * @return void
     */
    public function handle(FreezeWallet $event)
    {
        $args = $event->args;
        $data = $args->data;
        $registerType = array_key_exists('registerType', $data)?$data['registerType']:null;
        $act = array_key_exists('act', $data)?$data['act']:null;
        $type = array_key_exists('type', $data)?$data['type']:null;
        $callback = $args->callback;
        $callbackArgs = $args->callbackArgs;
        if($registerType === "phone") {
            $param = new \stdClass();
            $param->code = $data['registerCode'];
            $param->product = AlidayuSms::PRODUCT_WEB;
            $to = $data['target'];
            $this->sendRegisterCodeMessage($to, $param, $act);
        } else {
            $viewData = ['registerCode' => $data['registerCode']];
            $to = $data['target'];
            $this->sendRegisterCodeMail($viewData, $to, $type, $act);
        }
        if(!is_null($callback)) {
            call_user_func($callback, $callbackArgs);
        }
    }
    /**
     * To send register code mail to user registered mail address.
     * @param $data array A key => value array used in view.
     * @param $to String 'xxx@xxx.xxx'
     * @param $type String The category of act.
     * @param $act String
     * @return boolean
     */
    public function sendRegisterCodeMail($data, $to, $type, $act) {
        if(is_null($data) || is_null($to)) {
            Log::info(__FILE__.'('.__LINE__.') Invalid mail data to send');
            return false;
        }
        $view = 'message.'.studly_case($act);
        if(!is_null($type)) {
            $title = trans(studly_case($type) . '.' . $act);
        } else {
            $title = trans('Register.'.$act);
        }
        $this->mailer->send($view, $data, function ($message) use($to, $title) {
            $message->to($to)->subject($title);
        });
        Log::info(__FILE__.'('.__LINE__.') send register code to: '.$to);
        return true;
    }

    public function sendRegisterCodeMessage($to, $content, $act) {
        $client = new AlidayuSms();
        $client->setTemplate($act);
        if($client->isReady()) {
            $resp = $client->send($to, $content);
            Log::info("ali response: ".$resp);
//            $resp = json_decode($resp);
            Log::info($resp);
            if(property_exists($resp, 'error_response')) {
                Log::info(__FILE__.'('.__LINE__.') send sms register code to: '.$to.' error.');
                Log::info(__FILE__.'('.__LINE__.') err: '.$resp);
                return false;
            } else if(property_exists($resp, 'alibaba_aliqin_fc_sms_num_send_response')) {
                if($resp->alibaba_aliqin_fc_sms_num_send_response->result->err_code == 0) {
                    Log::info(__FILE__.'('.__LINE__.') send sms register code to: '.$to.' ok.');
                    return true;
                } else {
                    Log::info(__FILE__.'('.__LINE__.') send sms register code to: '.$to.' error.');
                    Log::info(__FILE__.'('.__LINE__.') err: '.$resp);
                    return false;
                }
            } else {
                return false;
            }
        } else {
            Log::info(__FILE__.'('.__LINE__.') send sms register code to: '.$to.' not ready.');
            return false;
        }
    }
}
