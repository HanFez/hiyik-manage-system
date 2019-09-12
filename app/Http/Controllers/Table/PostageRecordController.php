<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/9/22
 * Time: 15:41
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ShipFeeReturnPay;

class PostageRecordController extends Controller
{
    /**
     * postage record
     */
    public function postageList(){
        $err = new Error();
        $take = request()->input('take');
        $skip = request()->input('skip');
        $postage = ShipFeeReturnPay::where(IekModel::CONDITION)
            ->with('person.personNick.nick')
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        $total = count($postage);
        $statistic= $postage->groupBy(function($item,$key){
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
            $records = $postage->slice($skip,$take);
        }
        $err->setData($records);
        $err->statistic = $num;
        $err->total = $total;
        $err->take = $take;
        $err->skip = $skip;
        return view('admin.trades.postage',['result' => $err]);
    }
}
?>