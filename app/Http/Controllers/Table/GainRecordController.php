<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/22
 * Time: 15:33
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\GainPay;
use App\IekModel\Version1_0\IekModel;

class GainRecordController extends Controller
{
    /**
     * royalty record
     */
    public function gainList(){
        $err = new Error();
        $take = request()->input('take');
        $skip = request()->input('skip');
        $gain = GainPay::where(IekModel::CONDITION)
            ->with('person.personNick.nick')
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        $total = count($gain);
        $statistic= $gain->groupBy(function($item,$key){
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
            $records = $gain->slice($skip,$take);
        }
        $err->setData($records);
        $err->statistic = $num;
        $err->total = $total;
        $err->take = $take;
        $err->skip = $skip;
        return view('admin.trades.gain',['result' => $err]);
    }
}
?>