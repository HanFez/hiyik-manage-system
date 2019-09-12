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
use App\IekModel\Version1_0\Constants\ImportExcel;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ProductTraceability\Author;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AuthorController extends Controller
{
    use TraitRequestParams;

    public function index()
    {
        $emp = new Author();
        $field = $emp->tableSchema();
        $draw = $this->getRequestParam(request(),'draw');
        $skip = $this->getRequestParam(request(),'start');
        $take = $this->getRequestParam(request(),'length');
        $orders = $this->getRequestParam(request(),'order');
        $columns = $this->getRequestParam(request(),'columns');
        $shops = Author::getAll(new Author());
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
            $params-> type = 'tb-author';
            $params-> url = 'tb/authors';
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
        $re = Author::whereIn('id', $ids)
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
            $err = Author::createAuthor($params);
            DB::connection('pgsql_product_traceability')->commit();
        }catch (\Exception $e){
            DB::connection('pgsql_product_traceability')->rollback();
            $err->setError(Errors::FAILED);
            $err->setMessage($e->getMessage());
        }
        return response()->json($err);
    }

    public function modify(Request $request,$id){
        $err = new Error();
        $params = $request->all();
        try{
            DB::connection('pgsql_product_traceability')->beginTransaction();;
            $err = Author::modifyAuthor($params,$id);
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
        $shop = Author::find($id);
        $err->setData($shop);
        return view('thirdProduct.authorForm',['result'=> $err]);
    }

    public function getAuthor($id){
        $err = new Error();
        $author = Author::where(IekModel::CONDITION)
            ->where(IekModel::ID,$id)
            ->with('authorIntroduction.introductionContent')
            ->first();
        $err->setData($author);
        return response()->json($err);
    }

    public function importAuthor(Request $request){
        $err = new Error();
        try{
            $realPath = $request->file('fileName');
            $data = Excel::load($realPath, function($reader) {
            })->all()->toArray();
            $authors = [];
            $nos = [];
            $authorFiled = ImportExcel::AUTHOR;
            foreach ($data as $v){
                if(!is_null($v[$authorFiled['no']]) && trim($v[$authorFiled['no']]) != ''){
                    $author = [];
                    $nos[] = trim($v[$authorFiled['no']]);
                    $author['no'] = trim($v[$authorFiled['no']]);
                    $author['name'] = trim($v[$authorFiled['name']]);
                    $author['lang'] = trim($v[$authorFiled['lang']]);
                    $author['description'] = trim($v[$authorFiled['description']]);
                    $author['introduction'] = trim($v[$authorFiled['introduction']]);
                    $author['nationality'] = trim($v[$authorFiled['nationality']]);
                    $author['saying'] = trim($v[$authorFiled['saying']]);
                    $author['feature'] = trim($v[$authorFiled['feature']]);
                    $authors[] = $author;
                }
            }
            $existAuthor = Author::where(IekModel::CONDITION)
                ->whereIn('no',$nos)
                ->get();
            if(!$existAuthor->isEmpty()){
                $existAuthor = $existAuthor->toArray();
                $exists = [];
                foreach($authors as $key => $item) {
                    foreach($existAuthor as $value) {
                        if($item['no'] == $value['no']) {
                            $item['line'] = $key + 2;
                            array_push($exists, $item);
                        }
                    }
                }
                $err->setError(Errors::EXIST);
                $err->setData($exists);
            }else{
                Author::insert($authors);
                $err->setData($authors);
            }
        }catch (\Exception $e){
            $err->setError(Errors::FAILED);
            $err->setMessage($e->getMessage());
        }
        return response()->json($err);
    }
}