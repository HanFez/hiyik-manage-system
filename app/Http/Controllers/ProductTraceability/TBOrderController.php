<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 3/6/18
 * Time: 5:28 PM
 */

namespace app\Http\Controllers\ProductTraceability;


use App\Http\Controllers\Controller;
use App\Http\Controllers\TraitRequestParams;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Constants\ImportExcel;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ProductTraceability\TBOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TBOrderController extends Controller
{
    use TraitRequestParams;

    public function import(Request $request){
        $err = new Error();
        try{
            DB::connection('pgsql_product_traceability')->beginTransaction();
            $realPath = $request->file('fileName');
            $content = file_get_contents($realPath);
            $fileType = mb_detect_encoding($content , array('UTF-8','GBK','LATIN1','BIG5'));//获取当前文本编码格式
            $data = Excel::load($realPath, function($reader) {
            },$fileType)->all()->toArray();
            $params = [];
            $produceFiled = ImportExcel::TB_ORDER;
            foreach ($data as $v){
                $param = [];
                $param['order_no'] = trim($v[$produceFiled['order_no']]);
                $param['memo'] = str_replace('\'','',trim($v[$produceFiled['memo']]));
                $param['receive_name'] = trim($v[$produceFiled['receive_name']]);
                $param['receive_address'] = trim($v[$produceFiled['receive_address']]);
                $param['receive_phone'] = str_replace('\'','',trim($v[$produceFiled['receive_phone']]));
                $param['receive_call'] = str_replace('\'','',trim($v[$produceFiled['receive_call']]));
//                $param['ship_no'] = trim($v[$produceFiled['ship_no']]);
//                $param['ship_company'] = trim($v[$produceFiled['ship_company']]);
                $params[] = $param;
            }
            TBOrder::insert($params);
            $err->setData($params);
            DB::connection('pgsql_product_traceability')->commit();
        }catch (\Exception $e){
            DB::connection('pgsql_product_traceability')->rollback();
            $err->setError(Errors::FAILED);
            $err->setMessage($e->getMessage());
        }
        return response()->json($err);
    }

    public function index(){
        $err = new Error();
        $no = $this->getRequestParam(request(), 'no');
        if(!is_null($no)) {
            $order = TBOrder ::where('order_no', $no)
                -> where(IekModel::CONDITION)
                -> with('orderProducts')
                -> with('orderRealProducts.realProduct')
                -> with('ships')
                -> first();
            if(is_null($order)){
                $err->setError(Errors::FAILED);
                $order = $err;
                return view('thirdProduct.order', compact('order', 'order'));
                return response() -> json($err);
            }
            $order->hiyik_origin_uri = config('traceability.TRACEABILITY_URL');
            $err->setData($order);
            $order = $err;
            return view('thirdProduct.order', compact('order', 'order'));
            $err -> setData($data);
            return response() -> json($err);
        } else {
            $err->setError(Errors::FAILED);
            $order = $err;
            return view('thirdProduct.order', compact('order', 'order'));
            return response() -> json($err);
        }
    }
}