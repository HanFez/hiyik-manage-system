<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/22
 * Time: 15:23
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\CashPay;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;

class CashRecordController extends Controller
{
    /**
     * withdrawal record
     */
    public function cashList(){
        $err = new Error();
        $take = request()->input('take');
        $skip = request()->input('skip');
        $cash = CashPay::where(IekModel::CONDITION)
            ->with('person.personNick.nick')
            ->with('fromAccount')
            ->with('toAccount')
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        $total = count($cash);
        $statistic = CashPay::where(IekModel::STATUS,true)->get();
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
            $records = $cash->slice($skip,$take);
        }
        $err->setData($records);
        $err->statistic = $num;
        $err->total = $total;
        $err->take = $take;
        $err->skip = $skip;
        return view('admin.trades.cash',['result' => $err]);
    }
}
?>