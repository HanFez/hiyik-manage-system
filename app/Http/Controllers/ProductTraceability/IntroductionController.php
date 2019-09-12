<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/12/17
 * Time: 5:37 PM
 */

namespace app\Http\Controllers\ProductTraceability;


use App\Http\Controllers\Controller;
use App\Http\Controllers\TraitRequestParams;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ProductTraceability\Introduction;
use App\IekModel\Version1_0\ProductTraceability\IntroductionContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IntroductionController extends Controller
{
    use TraitRequestParams;
    public function getIntroduction($id){
        $err = new Error();
        $intro = Introduction::where(IekModel::CONDITION)
            ->where(IekModel::ID,$id)
            ->with('introductionContent.image.norms')
            ->first();
        $err->setData($intro);
        return response()->json($err);
    }

    public function index()
    {
        $emp = new Introduction();
        $field = $emp->tableSchema();
        $draw = $this->getRequestParam(request(),'draw');
        $skip = $this->getRequestParam(request(),'start');
        $take = $this->getRequestParam(request(),'length');
        $orders = $this->getRequestParam(request(),'order');
        $columns = $this->getRequestParam(request(),'columns');
        $type = $this->getRequestParam(request(),'type');
        $shops = Introduction::where(IekModel::CONDITION);
        if(!is_null($type)){
            $shops = $shops->where(IekModel::TYPE,$type);
        }
        $shops = $shops->get();
        $total = $shops->count();
        $data = new \stdClass();
        $data->recordsTotal = $total;
        $data->recordsFiltered = $total;
        $data->draw = $draw;
        if($draw>=1){
            foreach($orders as $order){
                $shop = $emp->orderBy($columns[$order['column']]['data'],$order['dir']);
            }
            if(!is_null($type)){
                $shop = $shop->where(IekModel::TYPE,$type);
            }
            $result = $shop->skip($skip)->take($take)
                ->with('productIntroduction')
                ->with(['introductionContent' => function ($q) {
                    $q -> with('image.norms');
                }])
                ->get()
                ->each(function($item, $key) {
                    $productIntroductionCount = $item->productIntroduction->count();
                    $item->productIntroductionCount = $productIntroductionCount;
                    unset($item->productIntroduction);
                });
        }else{
            $result = $shops;
        }
        $data->data = $result;
        if(!is_null($skip) && !is_null($take) && isset($skip) && isset($take) && isset($total)){
            return response()->json($data);
        }else{
            $params = new \stdClass();
//            $params-> type = 'tb-real-product';
//            $params-> url = 'tb/realProducts';
            return view('tableData.index',compact('field','params'));
        }
    }

    public function modify(Request $request,$iid){
        $err = new Error();
        $name = $request->input('name');
        $desc = $request->input('description');
        $type = $request->input('type');
        $contents = $request->input('contents');
        try{
            DB::connection('pgsql_product_traceability')->beginTransaction();
            Introduction::where(IekModel::ID,$iid)
                ->update([IekModel::NAME=>$name,IekModel::DESC=>$desc,IekModel::TYPE=>$type]);
            IntroductionContent::where('introduction_id',$iid)
                ->update([IekModel::REMOVED=>true]);
            $params = [];
            foreach ($contents as $content){
                $param = [
                    IekModel::CONTENT=>$content['content'],
                    IekModel::IID=>$content['imageId'],
                    IekModel::INDEX=>$content['index'],
                    'introduction_id'=>$iid
                ];
                $params[] = $param;
            }
            IntroductionContent::insert($params);
            DB::connection('pgsql_product_traceability')->commit();
        }catch (\Exception $e){
            DB::connection('pgsql_product_traceability')->rollback();
            $err->setError(Errors::FAILED);
            $err->setData($e->getMessage());
        }
        return response()->json($err);
    }

    public function create(Request $request){
        $err = new Error();
        $name = $request->input('name');
        $desc = $request->input('description');
        $type = $request->input('type');
        $contents = $request->input('contents');
        try{
            DB::connection('pgsql_product_traceability')->beginTransaction();
            $intro = Introduction::createRecord([
                IekModel::NAME=>$name,
                IekModel::DESC=>$desc,
                IekModel::TYPE=>$type,
                'contents' => $contents]);
            DB::connection('pgsql_product_traceability')->commit();
        }catch (\Exception $e){
            DB::connection('pgsql_product_traceability')->rollback();
            $err->setError(Errors::FAILED);
            $err->setData($e->getMessage());
        }
        return response()->json($err);
    }

    public function modifyView($id){
        $err = new Error();
        $intro = Introduction::where(IekModel::ID, $id)
            ->with('introductionContent.image.norms')
            ->first();
        $err->setData($intro);
        return view('thirdProduct.introductionEditForm',['result'=> $err]);
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
        $re = Introduction::whereIn('id', $ids)
            ->update([IekModel::REMOVED=>$handel]);
        if($re){
            $err->setData($re);
        }else{
            $err->setError(Errors::FAILED);
        }
        return $err;
    }

}