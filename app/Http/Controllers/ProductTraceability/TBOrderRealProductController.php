<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 3/7/18
 * Time: 9:56 AM
 */

namespace app\Http\Controllers\ProductTraceability;


use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\ProductTraceability\TBOrderRealProduct;

class TBOrderRealProductController extends Controller
{
    public function getOrder($pno){
        $err = new Error();
        $data = TBOrderRealProduct::where('real_product_no',$pno)
            ->where(IekModel::CONDITION)
            ->with(['order'=>function($q){
                $q->with('orderProduct')
                    ->with('orderRealProduct')
                    ->with('ship');
            }])
            ->first();
        $err->setData($data);
        return response()->json($err);
    }

}