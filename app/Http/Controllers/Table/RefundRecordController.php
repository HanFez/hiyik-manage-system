<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/22
 * Time: 14:43
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\OrderReturnThirdPay;
use App\IekModel\Version1_0\OrderReturnWealthPay;
use App\IekModel\Version1_0\ViewOrderReturnPay;

class RefundRecordController extends Controller
{
    public function refundRecordList()
    {
        /**
         * order refund record
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
                $records = $this->refundThirdPay();
                if($time != "undefined"){
                    $records = $records->where(IekModel::CREATED,'>=',$time)
                        ->where(IekModel::CREATED,'<=',$now);
                }else{
                    if($starttime != '' && $endtime != ''){
                        $records = $records->where(IekModel::CREATED,'>=',$starttime)
                            ->where(IekModel::CREATED,'<=',$endtime);
                    }
                    if($starttime != '' && $endtime == ''){
                        $records = $records->where(IekModel::CREATED,'>=',$starttime)
                            ->where(IekModel::CREATED,'<=',$now);
                    }
                    if($starttime == '' && $endtime != ''){
                        $records = $records->where(IekModel::CREATED,'<=',$endtime);;
                    }
                }
                $count = count($records);
                break;
            case 'wealth':
                $records = $this->refundWealthPay();
                if($time != "undefined"){
                    $records = $records->where(IekModel::CREATED,'>=',$time)
                        ->where(IekModel::CREATED,'<=',$now);
                }else{
                    if($starttime != '' && $endtime != ''){
                        $records = $records->where(IekModel::CREATED,'>=',$starttime)
                            ->where(IekModel::CREATED,'<=',$endtime);
                    }
                    if($starttime != '' && $endtime == ''){
                        $records = $records->where(IekModel::CREATED,'>=',$starttime)
                            ->where(IekModel::CREATED,'<=',$now);
                    }
                    if($starttime == '' && $endtime != ''){
                        $records = $records->where(IekModel::CREATED,'<=',$endtime);;
                    }
                }
                $count = $records->count();
                break;
            default:
                $records = ViewOrderReturnPay::where(IekModel::CONDITION)
                    ->with('fromPerson.personNick.nick')
                    ->orderBy(IekModel::CREATED,'desc')
                    ->get();
                if($time != "undefined"){
                    $records = $records->where(IekModel::CREATED,'>=',$time)
                        ->where(IekModel::CREATED,'<=',$now);
                }else{
                    if($starttime != '' && $endtime != ''){
                        $records = $records->where(IekModel::CREATED,'>=',$starttime)
                            ->where(IekModel::CREATED,'<=',$endtime);
                    }
                    if($starttime != '' && $endtime == ''){
                        $records = $records->where(IekModel::CREATED,'>=',$starttime)
                            ->where(IekModel::CREATED,'<=',$now);
                    }
                    if($starttime == '' && $endtime != ''){
                        $records = $records->where(IekModel::CREATED,'<=',$endtime);;
                    }
                }
                $count = count($records);
                break;
        }
        $statistic = $records->groupBy(function($item,$key){
            return $item->created_at->toDateString();
        });
        $num = [];
        foreach($statistic as $k=> $sta){
            $num[$k]['nums'] = count($sta);
            $money = 0;
            foreach($sta as $fee){
                $money += $fee->fee;
            }
            $num[$k]['moneys'] = round($money,2);
        }
        if(!is_null($take) && !is_null($skip)){
            $records = $records->slice($skip,$take);
        }
        $err->setData($records);
        $err->statistic = $num;
        $err->total = $count;
        $err->take = $take;
        $err->skip = $skip;
        $err->type = $type;
        $err->time = $time;
        $err->starttime = $starttime;
        $err->endtime = $endtime;
        return view('admin.trades.refund',['result'=>$err]);
    }
    /**
     * third pay
     */
    public function refundThirdPay(){
        $refund = new OrderReturnThirdPay();
        $records = $refund->where(IekModel::CONDITION)
            ->with('person.personNick.nick')
            ->with('fromAccount')
            ->with('toAccount')
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $records;
    }
    /**
     * wealth pay
     */
    public function refundWealthPay(){
        $refund = new OrderReturnWealthPay();
        $records = $refund->where(IekModel::CONDITION)
            ->with('fromPerson.personNick.nick')
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $records;
    }
}
?>