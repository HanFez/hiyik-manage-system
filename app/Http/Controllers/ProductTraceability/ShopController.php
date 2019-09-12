<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/12/17
 * Time: 5:35 PM
 */

namespace app\Http\Controllers\ProductTraceability;


use App\Http\Controllers\Controller;
use App\Http\Controllers\TraitRequestParams;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Constants\TableModel;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ProductTraceability\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ShopController extends Controller
{
    use TraitRequestParams;
    public function index()
    {
        $emp = new Shop();
        $field = $emp->tableSchema();
        $draw = $this->getRequestParam(request(),'draw');
        $skip = $this->getRequestParam(request(),'start');
        $take = $this->getRequestParam(request(),'length');
        $orders = $this->getRequestParam(request(),'order');
        $columns = $this->getRequestParam(request(),'columns');
        $shops = Shop::getAll(new Shop());
        $total = count($shops);
        $data = new \stdClass();
        $data->recordsTotal = $total;
        $data->recordsFiltered = $total;
        $data->draw = $draw;
        if($draw>=1){
            foreach($orders as $order){
                $shop = $emp->orderBy($columns[$order['column']]['data'],$order['dir']);
            }
            $result = $shop->skip($skip)->take($take)->get();
        }else{
            $result = $shops;
        }
        $data->data = $result;
        if(!is_null($skip) && !is_null($take) && isset($skip) && isset($take) && isset($total)){
            return response()->json($data);
        }else{
            $params = new \stdClass();
            $params-> type = 'tb-shop';
            $params-> url = 'tb/shops';
            return view('tableData.index',compact('field','params'));
        }
    }


    public function del(Request $request){
        return response()->json($this->changeStatus($request->input('ids'),true));
    }

    public function recover(Request $request){
        return response()->json($this->changeStatus($request->input('ids'),false));
    }

    /**
     * 批量change status
     */
    public function changeStatus($ids,$handel){
        $err = new Error();
        $re = Shop::whereIn('id', $ids)
            ->update([IekModel::REMOVED=>$handel]);
        if($re){
            $err->setData($re);
        }else{
            $err->setError(Errors::FAILED);
        }
        return $err;
    }

    public function create(Request $request){
        $err = new Error();
        $params = $request->all();
        try{
            DB::connection('pgsql_product_traceability')->beginTransaction();;
            $shop = Shop::createRecord($params);
            $err->setData($shop);
            DB::connection('pgsql_product_traceability')->commit();
        }catch (\Exception $e){
            DB::connection('pgsql_product_traceability')->rollback();
            $err->setError(Errors::FAILED);
            $err->setMessage($e->getMessage());
        }
        return response()->json($err);
    }

    public function modify(Request $request, $id){
        $err = new Error();
        $params = $request->all();
        try{
            DB::connection('pgsql_product_traceability')->beginTransaction();;
            $shop = Shop::createRecord($params, $id);
            $err->setData($shop);
            if(is_null($shop)){
                $err->setError(Errors::NOT_FOUND);
            }
            DB::connection('pgsql_product_traceability')->commit();
        }catch (\Exception $e){
            DB::connection('pgsql_product_traceability')->rollback();
            $err->setError(Errors::FAILED);
            $err->setMessage($e->getMessage());
        }
        return response()->json($err);
    }

    public function modifyView($id){
        $err = new Error();
        $shop = Shop::find($id);
        $err->setData($shop);
        return view('thirdProduct.shopForm',['result'=> $err]);
    }
}