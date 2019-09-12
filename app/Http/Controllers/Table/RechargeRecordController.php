<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/22
 * Time: 15:02
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\RechargePay;

class RechargeRecordController extends Controller
{
    /**
     * recharge record
     */
    public function rechargeList(){
        $err = new Error();
        $take = request()->input('take');
        $skip = request()->input('skip');
        $recharge = RechargePay::where(IekModel::CONDITION)
            ->with('person.personNick.nick')
            ->with('fromAccount')
            ->with('toAccount')
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        $total = count($recharge);
        $statistic = RechargePay::where(IekModel::STATUS,true)->get();
        $statistic= $statistic->groupBy(function($item,$key){
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
            $records = $recharge->slice($skip,$take);
        }
        $err->setData($records);
        $err->statistic = $num;
        $err->total = $total;
        $err->take = $take;
        $err->skip = $skip;
        return view('admin.trades.recharge',['result' => $err]);
    }
}
?>