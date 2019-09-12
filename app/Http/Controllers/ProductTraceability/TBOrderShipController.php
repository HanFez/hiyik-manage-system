<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 3/7/18
 * Time: 3:27 PM
 */

namespace app\Http\Controllers\ProductTraceability;


use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ProductTraceability\TBOrder;
use App\IekModel\Version1_0\ProductTraceability\TBOrderShip;
use Illuminate\Http\Request;

class TBOrderShipController extends Controller
{
    public function addShip(Request $request,$ono){
        $err = new Error();
        $shipMsg = $request->input('shipMsg');
        $order = TBOrder::where(IekModel::CONDITION)
            ->where('order_no',$ono)
            ->first();
        if(is_null($shipMsg) || count($shipMsg) < 1 || is_null($order)){
            $err->setError(Errors::FAILED);
            return response()->json($err);
        }
        $params = [];
        foreach ($shipMsg as $ship){
            if(isset($ship['shipNo']) && isset($ship['shipCompany'])){
                $param = ['ship_no'=>$ship['shipNo'],'ship_company'=>$ship['shipCompany'],'order_no'=>$ono,'real_product'=>$ship['productNo']];
                $params[]=$param;
            }else{
                $err->setError(Errors::FAILED);
                return response()->json($err);
            }
        }
        TBOrderShip::insert($params);
        return response()->json($err);
    }

}