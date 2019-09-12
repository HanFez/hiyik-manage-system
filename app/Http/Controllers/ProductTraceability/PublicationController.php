<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/12/17
 * Time: 5:39 PM
 */

namespace app\Http\Controllers\ProductTraceability;


use App\Http\Controllers\Controller;
use App\Http\Controllers\TraitRequestParams;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Constants\ImportExcel;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ProductTraceability\Author;
use App\IekModel\Version1_0\ProductTraceability\Museum;
use App\IekModel\Version1_0\ProductTraceability\Publication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class PublicationController extends Controller
{
    use TraitRequestParams;

    public function index()
    {
        $emp = new Publication();
        $field = $emp->tableSchema();
        $draw = $this->getRequestParam(request(),'draw');
        $skip = $this->getRequestParam(request(),'start');
        $take = $this->getRequestParam(request(),'length');
        $orders = $this->getRequestParam(request(),'order');
        $columns = $this->getRequestParam(request(),'columns');
        $shops = Publication::getAll(new Publication());
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
            $params-> type = 'tb-publication';
            $params-> url = 'tb/publications';
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
        $re = Publication::whereIn('id', $ids)
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
            $err = Publication::createPub($params);
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
            $err = Publication::modifyPub($params,$id);
            DB::connection('pgsql_product_traceability')->commit();
        }catch (\Exception $e){
            DB::connection('pgsql_product_traceability')->rollback();
            $err->setError(Errors::FAILED);
            $err->setMessage($e->getMessage());
        }
        return response()->json($err);
    }

    public function createView(){
        $err = new Error();
        $museums = Museum::getAll(Museum::class);
        $authors = Author::getAll(Author::class);
        $data = new \stdClass();
        $data->authors = $authors;
        $data->museums = $museums;
        $err->setData($data);
        return view('thirdProduct.publicationForm',['result'=> $err]);
    }

    public function modifyView($id){
        $err = new Error();
        $museums = Museum::getAll(Museum::class);
        $authors = Author::getAll(Author::class);
        $pub = Publication::find($id);
        $data = new \stdClass();
        $data->authors = $authors;
        $data->museums = $museums;
        $data->publication = $pub;
        $err->setData($data);
        return view('thirdProduct.publicationForm',['result'=> $err]);
    }

    public function getPublication($id){
        $err = new Error();
        $pub = Publication::where(IekModel::CONDITION)
            ->where(IekModel::ID,$id)
            ->with('author.authorIntroduction.introduction')//.introductionContent
            ->with('publicationIntroduction.introduction')//.introductionContent
//            ->with('core')
            ->first();
        $err->setData($pub);
        return response()->json($err);
    }

    public function importPublication(Request $request){
        $err = new Error();
        try{
            $realPath = $request->file('fileName');
            $data = Excel::load($realPath, function($reader) {
            })->all()->toArray();
            $publications = [];
            $returnPublications = [];
            $nos = [];
            $publicationFiled = ImportExcel::PUBLICATION;
            foreach ($data as $v){
                if(!is_null($v[$publicationFiled['no']]) && trim($v[$publicationFiled['no']]) != '' &&
                    !is_null($v[$publicationFiled['name']]) && trim($v[$publicationFiled['name']]) != ''){
                    $publication = [];
                    $nos[] = trim($v[$publicationFiled['no']]);
                    $publication['no'] = trim($v[$publicationFiled['no']]);
                    $publication['author_no'] = trim($v[$publicationFiled['author_no']]);
                    $publication['name'] = trim($v[$publicationFiled['name']]);
                    $publication['lang'] = isset($v[$publicationFiled['lang']])?trim($v[$publicationFiled['lang']]):null;
                    $publication['width'] = isset($v[$publicationFiled['width']])?trim($v[$publicationFiled['width']]):null;
                    $publication['height'] = isset($v[$publicationFiled['height']])?trim($v[$publicationFiled['height']]):null;
                    $publication['year'] = isset($v[$publicationFiled['year']])?trim($v[$publicationFiled['year']]):null;
                    $returnPublication = $publication;
                    if(!is_null($v[$publicationFiled['museum_name']]) && trim($v[$publicationFiled['museum_name']]) != '') {
                        $museum = Museum::where(IekModel::NAME,trim($v[$publicationFiled['museum_name']]))
                            ->first();
                        if(is_null($museum)){
                            $museum = Museum::createRecord([
                                'name' => trim($v[$publicationFiled['museum_name']]),
                                'description' => null,
                                'lang' => isset($v[$publicationFiled['museum_lang']])?trim($v[$publicationFiled['museum_lang']]):trim($v[$publicationFiled['museum_name']])
                            ]);
                        }
                        $publication['museum_id'] = $museum->id;
                        $returnPublication['museum'] = $museum;
                    } else {
                        $publication['museum_id'] = null;
                    }
                    $returnPublications[] = $returnPublication;
                    $publications[] = $publication;
                }
            }
            $exist = Publication::where(IekModel::CONDITION)
                ->whereIn('no',$nos)
                ->get();
            if(!$exist->isEmpty()){
                $exist = $exist->toArray();
                $exists = [];
                foreach($returnPublications as $key => $item) {
                    foreach($exist as $value) {
                        if($item['no'] == $value['no']) {
                            $item['line'] = $key + 2;
                            array_push($exists, $item);
                        }
                    }
                }
                $err->setError(Errors::EXIST);
                $err->setData($exists);
            }else{
                Publication::insert($publications);
                $err->setData($returnPublications);
            }
        }catch (\Exception $e){
            $err->setError(Errors::FAILED);
            $err->setMessage($e->getMessage());
        }
        return response()->json($err);
    }
}