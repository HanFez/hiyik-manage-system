<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/22
 * Time: 14:20
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\RewardThirdPay;
use App\IekModel\Version1_0\RewardWealthPay;
use App\IekModel\Version1_0\ViewRewardPay;

class RewardRecordController extends Controller
{
    public function rewardList()
    {
        /**
         * reward record
         */
        $err = new Error();
        $take = request()->input('take');
        $skip = request()->input('skip');
        $type = request()->input('type');
        $time = request()->input('time');
        $starttime = request()->input('startTime');
        $endtime = request()->input('endTime');
        $now = date("Y-m-d H:i:s",strtotime("+8 hour"));
        switch($type){
            case 'ali':
                $rewards = $this->rewardThirdPay();
                if($time != "undefined"){
                    $rewards = $rewards->where(IekModel::CREATED,'>=',$time)
                        ->where(IekModel::CREATED,'<=',$now);
                }else{
                    if($starttime != '' && $endtime != ''){
                        $rewards = $rewards->where(IekModel::CREATED,'>=',$starttime)
                            ->where(IekModel::CREATED,'<=',$endtime);
                    }
                    if($starttime != '' && $endtime == ''){
                        $rewards = $rewards->where(IekModel::CREATED,'>=',$starttime)
                            ->where(IekModel::CREATED,'<=',$now);
                    }
                    if($starttime == '' && $endtime != ''){
                        $rewards = $rewards->where(IekModel::CREATED,'<=',$endtime);;
                    }
                }
                $count = count($rewards);
                break;
            case 'wealth':
                $rewards = $this->rewardWealthPay();
                if($time != "undefined"){
                    $rewards = $rewards->where(IekModel::CREATED,'>=',$time)
                        ->where(IekModel::CREATED,'<=',$now);
                }else{
                    if($starttime != '' && $endtime != ''){
                        $rewards = $rewards->where(IekModel::CREATED,'>=',$starttime)
                            ->where(IekModel::CREATED,'<=',$endtime);
                    }
                    if($starttime != '' && $endtime == ''){
                        $rewards = $rewards->where(IekModel::CREATED,'>=',$starttime)
                            ->where(IekModel::CREATED,'<=',$now);
                    }
                    if($starttime == '' && $endtime != ''){
                        $rewards = $rewards->where(IekModel::CREATED,'<=',$endtime);;
                    }
                }
                $count = $rewards->count();
                break;
            default:
                $rewards = ViewRewardPay::where(IekModel::CONDITION)
                    ->with('fromPerson')
                    ->with('toPerson')
                    ->orderBy(IekModel::CREATED,'desc')
                    ->get();
                if($time != "undefined"){
                    $rewards = $rewards->where(IekModel::CREATED,'>=',$time)
                        ->where(IekModel::CREATED,'<=',$now);
                }else{
                    if($starttime != '' && $endtime != ''){
                        $rewards = $rewards->where(IekModel::CREATED,'>=',$starttime)
                            ->where(IekModel::CREATED,'<=',$endtime);
                    }
                    if($starttime != '' && $endtime == ''){
                        $rewards = $rewards->where(IekModel::CREATED,'>=',$starttime)
                            ->where(IekModel::CREATED,'<=',$now);
                    }
                    if($starttime == '' && $endtime != ''){
                        $rewards = $rewards->where(IekModel::CREATED,'<=',$endtime);;
                    }
                }
                $count = count($rewards);
                break;
        }
        $statistic = $rewards->groupBy(function($item,$key){
            return $item->created_at->toDateString();
        });
        $num = [];
        if($type == 'ali'){
            foreach($statistic as $k=> $sta){
                $num[$k]['nums'] = count($sta);
                $money = 0;
                foreach($sta as $fee){
                    if($fee->status == true){
                        $money += $fee->fee;
                    }
                }
                $num[$k]['moneys'] = round($money,2);
                if($money == 0){
                    unset($num[$k]);
                }
            }
        }else{
            foreach($statistic as $k=> $sta){
                $num[$k]['nums'] = count($sta);
                $money = 0;
                foreach($sta as $fee){
                    $money += $fee->fee;
                }
                $num[$k]['moneys'] = round($money,2);
            }
        }
        if(!is_null($take) && !is_null($skip)){
            $rewards = $rewards->slice($skip,$take);
        }
        $err->setData($rewards);
        $err->statistic = $num;
        $err->total = $count;
        $err->take = $take;
        $err->skip = $skip;
        $err->type = $type;
        $err->time = $time;
        $err->starttime = $starttime;
        $err->endtime = $endtime;
        return view('admin.trades.reward',['result'=>$err]);
    }
    /**
     * third pay
     */
    public function rewardThirdPay(){
        $reward = new  RewardThirdPay();
        $records = $reward->where(IekModel::CONDITION)
            ->with('fromPerson.personNick.nick')
            ->with('toPerson.personNick.nick')
            ->with('fromAccount')
            ->with('toAccount')
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $records;
    }
    /**
     * wealth pay
     */
    public function rewardWealthPay(){
        $reward = new RewardWealthPay();
        $records = $reward->where(IekModel::CONDITION)
            ->with('fromPerson.personNick.nick')
            ->with('toPerson.personNick.nick')
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $records;
    }
}
?>