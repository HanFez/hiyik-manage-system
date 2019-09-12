<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/12/17
 * Time: 5:33 PM
 */

namespace app\Http\Controllers\ProductTraceability;


use App\Http\Controllers\Controller;
use App\Http\Controllers\TraitRequestParams;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Constants\ImportExcel;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ProductTraceability\Museum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class MuseumController extends Controller
{
    use TraitRequestParams;

    public function index()
    {
        $emp = new Museum();
        $field = $emp->tableSchema();
        $draw = $this->getRequestParam(request(),'draw');
        $skip = $this->getRequestParam(request(),'start');
        $take = $this->getRequestParam(request(),'length');
        $orders = $this->getRequestParam(request(),'order');
        $columns = $this->getRequestParam(request(),'columns');
        $shops = Museum::getAll(new Museum());
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
            $params-> type = 'tb-museum';
            $params-> url = 'tb/museums';
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
        $re = Museum::whereIn('id', $ids)
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
            $museum = Museum::createRecord($params);
            $err->setData($museum);
            DB::connection('pgsql_product_traceability')->commit();
        }catch (\Exception $e){
            DB::connection('pgsql_product_traceability')->rollback();
                $err->setError(Errors::FAILED);
            $err->setMessage($e->getMessage());
        }
        return response()->json($err);
    }

    public function modify(Request $request,$id){
        $params = $request->all();
        $err = new Error();
        try{
            DB::connection('pgsql_product_traceability')->beginTransaction();;
            $museum = Museum::createRecord($params,$id);
            $err->setData($museum);
            if(is_null($museum)){
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

    function modifyView($id){
        $err = new Error();
        $shop = Museum::find($id);
        $err->setData($shop);
        return view('thirdProduct.museumForm',['result'=> $err]);
    }

    public function importMuseum(Request $request){
        $err = new Error();
        try{
            $realPath = $request->file('fileName');
            $data = Excel::load($realPath, function($reader) {
            })->all()->toArray();
            $museums = [];
            $nos = [];
            $museumFiled = ImportExcel::MUSEUM;
            foreach ($data as $v){
                if(!is_null($v[$museumFiled['name']]) && trim($v[$museumFiled['name']]) != ''){
                    $museum = [];
                    $nos[] = trim($v[$museumFiled['name']]);
                    $museum['name'] = trim($v[$museumFiled['name']]);
                    $museum['lang'] = isset($v[$museumFiled['lang']])?trim($v[$museumFiled['lang']]):null;
                    $museum['description'] = isset($v[$museumFiled['description']])?trim($v[$museumFiled['description']]):null;
                    $museums[] = $museum;
                }
            }
            $exist = Museum::where(IekModel::CONDITION)
                ->whereIn('name',$nos)
                ->get();
            if(!$exist->isEmpty()){
                $exist = $exist->toArray();
                $exists = [];
                foreach($museums as $key => $item) {
                    foreach($exist as $value) {
                        if($item['name'] == $value['name']) {
                            $item['line'] = $key + 2;
                            array_push($exists, $item);
                        }
                    }
                }
                $err->setError(Errors::EXIST);
                $err->setData($exists);
            }else{
                Museum::insert($museums);
                $err->setData($museums);
            }
        }catch ( \Exception $e){
            $err->setError(Errors::FAILED);
            $err->setMessage($e->getMessage());
        }
        return response()->json($err);
    }

}