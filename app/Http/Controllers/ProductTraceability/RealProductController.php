<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/18/17
 * Time: 9:30 AM
 */

namespace app\Http\Controllers\ProductTraceability;


use App\Http\Controllers\Controller;
use App\Http\Controllers\FileZIPController;
use App\Http\Controllers\TraitFileZip;
use App\Http\Controllers\TraitRequestParams;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Constants\ImportExcel;
use App\IekModel\Version1_0\Constants\RealProductStatus;
use App\IekModel\Version1_0\Constants\TableModel;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ProductTraceability\Product;
use App\IekModel\Version1_0\ProductTraceability\QRImage;
use App\IekModel\Version1_0\ProductTraceability\RealProduct;
use App\IekModel\Version1_0\ProductTraceability\RealProductQRImage;
use App\IekModel\Version1_0\ProductTraceability\TBOrder;
use App\IekModel\Version1_0\ProductTraceability\TBOrderProduct;
use App\IekModel\Version1_0\ProductTraceability\TBOrderRealProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class RealProductController extends Controller
{
    use TraitRequestParams;
    use TraitFileZip;

    public function test($tb){
        $tem = TableModel::TABLE_MODEL[$tb];
        $tem = new $tem;
        $field = $tem->tableSchema();
        $str1='';
        $str2='';
        foreach ($field as $k=>$v){
            $str1 = $str1.$v->column_name;
            $str2 = $str2.$v->column_name.' '.$v->data_type;
            if($k+1 != count($field)){
                $str1 = $str1.',';
                $str2 = $str2.',';
            }
        }

        $sql = 'insert into "'.$tb.'"('.$str1.')  select '.$str1.' from dblink(\'hostaddr=192.168.0.2 port=5434 dbname=db_iek_v11 user=iekDatabase password=iekDatabase\',\'select '.$str1.' from "'.$tb.'"\') as t('.$str2.');';
        dd($sql);
    }
    public function create(Request $request){
        $err = new Error();
        $products = $request->input('products');
        if(is_null($products)){
            $err->setError(Errors::NOT_FOUND);
            $err->setData('product');
            return response()->json($err);
        }
        $realPro = [];
        try{
            DB::connection('pgsql_product_traceability')->beginTransaction();
            foreach ($products as $productId){
                $product = Product::getProductById($productId['id']);
                if(is_null($product)){
                    $err->setError(Errors::NOT_FOUND);
                    $err->setData('product');
                    throw new \Exception('rollback');
                }
                for ($x=0; $x<$productId['num']; $x++) {
                    $no = RealProduct::getNo($product['no']);
                    $params = [
                        'productId'=>$productId['id'],
                        'no'=>$no->no,
                        'userNo'=>$no->userNo,
                        'status' => RealProductStatus::WAIT_PRODUCT
                    ];
                    //http://192.168.0.21:8093/tb/getProduceView/ba835722-7008-43ad-9b14-1b17dd7f63b6
                    $realProduct = RealProduct::createRecord($params);
                    $createQRImage = $this->createQRImage($params['no'],'origin',$realProduct->{IekModel::ID},config('traceability.TRACEABILITY_URL').$params['no']);
                    $createQRManageImage = $this->createQRImage($params['no'],'manage',$realProduct->{IekModel::ID},config('traceability.PRODUCE_URL').$realProduct->{IekModel::ID});
                    $realPro[] = $realProduct->{IekModel::ID};
                }
            }
            DB::connection('pgsql_product_traceability')->commit();
        }catch (\Exception $e){
            DB::connection('pgsql_product_traceability')->rollback();
            if(!$err->isOk()){
                return response()->json($err);
            }
            $err->setError(Errors::FAILED);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        $data = RealProduct::whereIn(IekModel::ID,$realPro)
            ->with('QRImage.QRImage')
            ->get();
        $err->setData($data);
        return response()->json($err);
    }

    public function createView(){
        $err = new Error();
        $products = Product::getAll(Product::class);
        $data = new \stdClass();
        $data->products = $products;
        $err->setData($data);
        return view('thirdProduct.realProductForm',['result'=> $err]);
    }

    function modifyView($id){
        $err = new Error();
        $data = RealProduct::find($id);
        $err->setData($data);
        return view('thirdProduct.realProductForm',['result'=> $err]);
    }

    public function checkProduct(Request $request,$pid){
        $err = new Error();
        $checker = $request->input('checker');
        $status = $request->input('status');
        $detail = $request->input('detail');
        if(is_null($detail) || !is_array($detail) || empty($detail)){
            $detail = null;
        }else{
            $detail = json_encode($detail);
        }
        if($status != RealProductStatus::PASS){
            $status = RealProductStatus::FAIL;
        }
        if(is_null($checker)){
            $err->setError(Errors::NOT_FOUND);
            $err->setData('checker');
            return response()->json($err);
        }
        $product = RealProduct::where(IekModel::ID,$pid)
            ->where(IekModel::CONDITION)
            ->where('status','!=',RealProductStatus::PASS)
            ->first();
        if(is_null($product)){
            $err->setError(Errors::NOT_FOUND);
            $err->setData('product');
            return response()->json($err);
        }
        $product->checker = $checker;
        $product->status = $status;
        if(is_null($product->detail)){
            $product->detail = $detail;
        }
        $product->save();
        return response()->json($err);
    }

    public function createQRImage($no,$type,$pid,$content){
        $qrcode = QrCode::format('svg')->size(200)->margin(0)->backgroundColor(255,255,255)->generate($content);
        Storage::put('files/TBProducts/qrcode/'.$type.'/'.$no.'.svg', $qrcode);
        $qrParams = [
            'file_name' => $no,
            'extension' => 'svg',
            'width' => 200,
            'height' => 200,
            'md5' => md5($qrcode),
            'uri' => 'files/TBProducts/qrcode/'.$type.'/'.$no.'.svg',
            'length' => null
        ];
        $qrImage = QRImage::createRecord($qrParams);
        $productQRImage = RealProductQRImage::insert(['real_product_id'=>$pid,'image_id'=>$qrImage->{IekModel::ID},'type'=>$type]);
    }

    public function index()
    {
        $emp = new RealProduct();
        $userNo = $this->getRequestParam(request(), 'userNo');
        if(!is_null($userNo)) {
            $product = RealProduct::where('user_no',$userNo)
                ->with('produced')
                ->with(['orderRealProduct.order'=>function($q){
                    $q->with('orderRealProducts.realProduct')
                        ->with('ships');
                }])
                ->with(['product'=>function($q) {
                    $q->with('image.norms')
                        ->with('produceParams');
                }])
                ->first();
            if(!is_null($product)){
                $product -> originManage = config('traceability.PRODUCE_URL').$product -> id;
                $product -> originHiyik = config('traceability.TRACEABILITY_URL').$product -> no;
                $product->detail = json_decode($product->detail);
            }
            $product->hiyik_origin_uri = config('traceability.TRACEABILITY_URL');
            return view('thirdProduct.produceParam',compact('product','product'));
        } else {
            $field = $emp -> tableSchema();
            $draw = $this -> getRequestParam(request(), 'draw');
            $skip = $this -> getRequestParam(request(), 'start');
            $take = $this -> getRequestParam(request(), 'length');
            $orders = $this -> getRequestParam(request(), 'order');
            $columns = $this -> getRequestParam(request(), 'columns');
            $status = $this -> getRequestParam(request(), 'status');
            $isProduced = $this -> getRequestParam(request(), 'isProduced');
            if ($status === 'null') {
                $status = null;
            }
            $shops = RealProduct ::where(IekModel::CONDITION);
            if (!is_null($status)) {
                $shops = $shops -> where(IekModel::STATUS, $status);
            }
            $shops = $shops -> get();
            $total = $shops -> count();
            $data = new \stdClass();
            $data -> recordsTotal = $total;
            $data -> recordsFiltered = $total;
            $data -> draw = $draw;
            if ($draw >= 1) {
                foreach ($orders as $order) {
                    $shop = $emp -> orderBy($columns[$order['column']]['data'], $order['dir']);
                }
                if (!is_null($status)) {
                    $shop = $shop -> where(IekModel::STATUS, $status);
                }
                if (!is_null($isProduced)) {
                    if ($isProduced == 'true') {
                        $shop = $shop -> whereHas('produced');
                    } else {
                        $shop = $shop -> doesntHave('produced');
                    }
                }
                $result = $shop -> skip($skip) -> take($take)
                    -> with('produced')
                    -> with(['product' => function ($q) {
                        $q -> with('image.norms')
                            -> with('produceParams')
                            -> with('core.publication');
                    }])
                    -> get();
                $result -> each(function ($item, $key) {
                    $item -> originManage = config('traceability.PRODUCE_URL').$item -> id;
                    $item -> originHiyik = config('traceability.TRACEABILITY_URL').$item -> no;
                    $item -> detail = json_decode($item -> detail);
                });
            } else {
                $result = $shops;
            }
            $data -> data = $result;
            if (!is_null($skip) && !is_null($take) && isset($skip) && isset($take) && isset($total)) {
                return response() -> json($data);
            } else {
                $params = new \stdClass();
                $params -> type = 'tb-real-product';
                $params -> url = 'tb/realProducts';
                return view('tableData.index', compact('field', 'params'));
            }
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
        $re = RealProduct::whereIn('id', $ids)
            ->update([IekModel::REMOVED=>$handel]);
        if($re){
            $err->setData($re);
        }else{
            $err->setError(Errors::FAILED);
        }
        return $err;
    }

    public function getProduceView($id){
        $product = RealProduct::where(IekModel::ID,$id)
            ->with('produced')
            ->with(['orderRealProduct.order'=>function($q){
                $q->with('orderRealProducts.realProduct')
                    ->with('ships');
            }])
            ->with(['product'=>function($q) {
                $q->with('image.norms')
                    ->with('produceParams');
            }])
            ->first();
        if(!is_null($product)){
            $product->detail = json_decode($product->detail);
            $product->hiyik_origin_uri = config('traceability.TRACEABILITY_URL');
        }
        return view('thirdProduct.produceParam',compact('product','product'));
    }

    public function getProduceViewByNo($no){
        $product = RealProduct::where('user_no',$no)
            ->with('produced')
            ->with(['orderRealProduct.order'=>function($q){
                $q->with('orderRealProducts.realProduct')
                    ->with('ships');
            }])
            ->with(['product'=>function($q) {
                $q->with('image.norms')
                    ->with('produceParams');
            }])
            ->first();
        if(!is_null($product)){
            $product->detail = json_decode($product->detail);
        }
        return view('thirdProduct.produceParam',compact('product','product'));
    }

    public function createAgain(Request $request){
        $err = new Error();
        $ids = $request->input('ids');
        if(is_null($ids) || !is_array($ids) || empty($ids)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setData('ids can not null');
            return response()->json($err);
        }
        $products = RealProduct::whereIn(IekModel::ID,$ids)
            ->get();
        if($products->isEmpty() || $products->count() != count($ids)){
            $err->setError(Errors::NOT_FOUND);
            $err->setMessage('some product not found');
            return response()->json($err);
        }
        $products = $products->toArray();
        try{
            DB::connection('pgsql_product_traceability')->beginTransaction();
            $newIds = [];
            foreach ($products as $product){
                $productNo = explode('-',$product['no']);
                $no = RealProduct::getNo($productNo[1]);
                $againProduct = RealProduct::createRecord([
                    'productId'=>$product['product_id'],
                    'no'=>$no->no,
                    'userNo'=>$no->userNo,
                    'from_no'=> $product['no'],
                    'status' => RealProductStatus::WAIT_PRODUCT
                ]);
                $createQRImage = $this->createQRImage($againProduct->no,'origin',$againProduct->{IekModel::ID},config('traceability.TRACEABILITY_URL').$againProduct->no);
                $createQRManageImage = $this->createQRImage($againProduct->no,'manage',$againProduct->{IekModel::ID},config('traceability.PRODUCE_URL').$againProduct->{IekModel::ID});
                $newIds[] = $againProduct->id;
            }
            $err = $this->downloadProductsMake($newIds);
            $err->setData($newIds);
            DB::connection('pgsql_product_traceability')->commit();
        }catch (\Exception $e){
            DB::connection('pgsql_product_traceability')->rollback();
            if(!$err->isOk()){
                return response()->json($err);
            }
            $err->setError(Errors::FAILED);
            $err->setMessage($e->getMessage());
        }
        return response()->json($err);
    }

    //-------------------------------------first phase taobao order handel -------------------------------------

    public function import(Request $request){
        $err = new Error();
        try{
            DB::connection('pgsql_product_traceability')->beginTransaction();
            $realPath = $request->file('fileName');
            $isAgain = $request->input('isAgain');
            $content = file_get_contents($realPath);
            $fileType = mb_detect_encoding($content , array('UTF-8','GBK','LATIN1','BIG5'));//获取当前文本编码格式
            $data = Excel::load($realPath, function($reader) {
            },$fileType)->all()->toArray();
            $params = [];
            $produceFiled = ImportExcel::TB_ORDER_PRODUCT;
            foreach ($data as $v){
                $param = [];
                $param['order_no'] = trim($v[$produceFiled['order_no']]);
                $param['product_no'] = trim($v[$produceFiled['product_no']]);
                $param['num'] = trim($v[$produceFiled['num']]);
                $param['is_again'] = $isAgain;
                $product = Product::getProductByNo($param['product_no']);
                if(is_null($product)){
                    $err->setError(Errors::NOT_FOUND);
                    $err->setData('product');
                    throw new \Exception('rollback');
                }
                $order = TBOrder::where('order_no',$param['order_no'])->where(IekModel::CONDITION)->first();
                if(is_null($order)){
                    $err->setError(Errors::NOT_FOUND);
                    $err->setData('order');
                    throw new \Exception('rollback');
                }
                $params[] = $param;
            }
            TBOrderProduct::insert($params);
            $err = $this->orderRealProduct($params);
            if(!$err->isOk()){
                throw new \Exception('rollback');
            }
            $err->setData($params);
            DB::connection('pgsql_product_traceability')->commit();
        }catch (\Exception $e){
            DB::connection('pgsql_product_traceability')->rollback();
            if($err->isOk()) {
                $err->setError(Errors::FAILED);
                $err->setMessage($e->getMessage());
            }
        }
        return response()->json($err);
    }

    public function orderRealProduct($createParams){
        $err = new Error();
        try{
            DB::connection('pgsql_product_traceability')->beginTransaction();
            foreach ($createParams as $createParam){
                $product = Product::getProductByNo($createParam['product_no']);
                if(is_null($product)){
                    $err->setError(Errors::NOT_FOUND);
                    $err->setData('product');
                    throw new \Exception('rollback');
                }
                for ($x=0; $x<$createParam['num']; $x++) {
                    $no = RealProduct::getNo($product['no']);
                    $params = [
                        'productId'=>$product->id,
                        'no'=>$no->no,
                        'userNo'=>$no->userNo,
                        'status' => RealProductStatus::WAIT_PRODUCT
                    ];
                    $realProduct = RealProduct::createRecord($params);
                    TBOrderRealProduct::insert(['order_no'=>$createParam['order_no'],'real_product_no'=>$no->userNo]);
                    $createQRImage = $this->createQRImage($params['no'],'origin',$realProduct->{IekModel::ID},config('traceability.TRACEABILITY_URL').$params['no']);
                    $createQRManageImage = $this->createQRImage($params['no'],'manage',$realProduct->{IekModel::ID},config('traceability.PRODUCE_URL').$realProduct->{IekModel::ID});
                    $realPro[] = $realProduct->{IekModel::ID};
                }
            }
            DB::connection('pgsql_product_traceability')->commit();
        }catch (\Exception $e){
            DB::connection('pgsql_product_traceability')->rollback();
            if($err->isOk()) {
                $err->setError(Errors::FAILED);
                $err->setMessage($e->getMessage());
            }
        }
        return $err;
    }

}