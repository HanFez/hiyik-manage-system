<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/12/17
 * Time: 5:38 PM
 */

namespace app\Http\Controllers\ProductTraceability;


use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ProductTraceability\AuthorIntroduction;
use App\IekModel\Version1_0\ProductTraceability\Core;
use App\IekModel\Version1_0\ProductTraceability\Introduction;
use App\IekModel\Version1_0\ProductTraceability\IntroductionContent;
use App\IekModel\Version1_0\ProductTraceability\ProduceParam;
use App\IekModel\Version1_0\ProductTraceability\Product;
use App\IekModel\Version1_0\ProductTraceability\ProductIntroduction;
use App\IekModel\Version1_0\ProductTraceability\Publication;
use App\IekModel\Version1_0\ProductTraceability\PublicationIntroduction;
use App\IekModel\Version1_0\ProductTraceability\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use League\Flysystem\Exception;

class ProductController extends Controller
{
    public function create(Request $request,$pid=null){
        $err = new Error();
        $params = $request->all();
        try{
            DB::connection('pgsql_product_traceability')->beginTransaction();
            if(isset($params['product']['core'])){
                $core = Core::createRecord($params['product']['core']);
                $params['product']['coreId'] = $core->id;
            }
            $product = Product::createRecord($params['product'],$pid);
            if(!$product->isOk()){
                $err = $product;
                throw new \Exception('rollback');
            }
            ProduceParam::insert([
                'product_no'=>trim($params['product']['no']),
                'core_no'=>trim($params['produceData']['coreNo']),
                'core_size'=>trim($params['produceData']['coreSize']),
                'border_no'=>trim($params['produceData']['borderNo']),
                'border_size'=>trim($params['produceData']['borderSize']),
                'core_nail'=>trim($params['produceData']['coreNail']),
                'back_nail'=>trim($params['produceData']['backNail']),
                'back_size'=>trim($params['produceData']['backSize']),
                'flannel_size'=>trim($params['produceData']['flannelSize']),
                'flannel_width'=>trim($params['produceData']['flannelWidth']),
                'frame_width'=>trim($params['produceData']['frameWidth']),
                'frame_size'=>trim($params['produceData']['frameSize']),
                'coating'=>trim($params['produceData']['coating']),
                'hide_hook'=>trim($params['produceData']['hideHook']),
                'core_material'=>trim($params['produceData']['coreMaterial']),
                'wire_rope'=>trim($params['produceData']['wireRope']),
                'line_locker'=>trim($params['produceData']['lineLocker']),
                'hook'=>trim($params['produceData']['hook']),
                'mount'=>trim($params['produceData']['mount']),
            ]);
            $product = $product->data;
            $proIntros = [];
            if(isset($params['introductionIds'])){
                foreach ($params['introductionIds'] as $introductionId){
                    if(!is_null($pid)){
                        $exist = ProductIntroduction::where('introduction_id',$introductionId)
                            ->where('product_id',$pid)
                            ->first();
                        if(!is_null($exist)){
                            $exist->{IekModel::REMOVED} = false;
                            $exist->save();
                            continue;
                        }
                    }
                    $proIntro = ['introduction_id'=>$introductionId,'product_id'=>$product->{IekModel::ID}];
                    $proIntros[] = $proIntro;
                }
            }
            if(isset($params['introductions'])){
                foreach ($params['introductions'] as $introduction){
                    $intro = Introduction::createRecord($introduction);
                    $introductionRelationParams = [];
                    $introductionRelationParams['introductionId'] = $intro->{IekModel::ID};
                    switch ($introduction['type']){
                        case 'publication':
                            $introductionRelationParams['publicationId'] = $params['product']['core']['publicationId'];
                            PublicationIntroduction::createRecord($introductionRelationParams);
                            break;
                        case 'author':
                            $author = Publication::getAuthor($params['product']['core']['publicationId']);
                            if(is_null($author)){
                                $err->setError(Errors::INVALID_PARAMS);
                                $err->setMessage('can not find author');
                            }
                            $introductionRelationParams['authorId'] = $author->{IekModel::ID};
                            AuthorIntroduction::createRecord($introductionRelationParams);
                            break;
                        case 'craft':
                        default:;
                    }
                    if(!is_null($pid)){
                        $exist = ProductIntroduction::where('introduction_id',$intro->{IekModel::ID})
                            ->where('product_id',$pid)
                            ->first();
                        if(!is_null($exist)){
                            $exist->{IekModel::REMOVED} = false;
                            $exist->save();
                            continue;
                        }
                    }
                    $proIntro = ['introduction_id'=>$intro->{IekModel::ID},'product_id'=>$product->{IekModel::ID}];
                    $proIntros[] = $proIntro;
                }
            }
            ProductIntroduction::insert($proIntros);
            $err->setData($product);
            DB::connection('pgsql_product_traceability')->commit();
        }catch (\Exception $e){
            DB::connection('pgsql_product_traceability')->rollback();
            if($err->isOk()){
                $err->setError(Errors::FAILED);
                $err->setMessage($e->getMessage());
            }
        }
        return response()->json($err);
    }

    public function createView(Request $request){
        $err = new Error();
        $type = $request->input('introductions');
        $type = json_decode($type);
        $data = new \stdClass();
        $data->introductions = [];
        if(!is_null($type) && !empty($type)){
            $introduction = Introduction::whereIn(IekModel::TYPE,$type)
                ->where(IekModel::CONDITION)
                ->get()
                ->toArray();
        }else{
            $introduction = Introduction::where(IekModel::CONDITION)
                ->get()
                ->toArray();
        }
        $shop = Shop::getAll(Shop::class);
        $publication = Publication::getAll(Publication::class);
        $data->shops = $shop;
        $data->publications = $publication;
        $data->introductions = $introduction;
        $err->setData($data);
        return view('thirdProduct.productForm',['result'=> $err]);
    }

    public function modifyView(Request $request,$id){
        $err = new Error();
        $type = $request->input('introductions');
        $data = new \stdClass();
        $data->introductions = [];
        if(!is_null($type) && !empty($type)){
            $introduction = Introduction::whereIn(IekModel::TYPE,$type)
                ->where(IekModel::CONDITION)
                ->get()
                ->toArray();
        }else{
            $introduction = Introduction::where(IekModel::CONDITION)
                ->get()
                ->toArray();
        }
        $shop = Shop::getAll(Shop::class);
        $publication = Publication::getAll(Publication::class);
        $data->shops = $shop;
        $data->publications = $publication;
        $data->introductions = $introduction;
        $product = Product::where(IekModel::ID,$id)
            ->with('image.norms')
            ->with('productIntroduction.introduction.introductionContent.image.norms')
            ->with('core')
            ->with('produceParams')
            ->first();
        $data->product = $product;
//        dd($data);
        $err->setData($data);
        return view('thirdProduct.productForm',['result'=> $err]);
    }

    public function productList(Request $request){
        $err = new Error();
        $take = $request->input('take');
        $skip = $request->input('skip');
        $isSell = $request->input('isSell');
        $isDelete = $request->input('isDelete');
        $products = Product::with('image.norms')
            ->with('produceParams')
            ->orderBy(IekModel::UPDATED, 'DESC');
        if(!is_null($isSell)){
            if($isSell != 'false'){
                $isSell = true;
            }else{
                $isSell = false;
            }
            $products = $products->where('is_sell',$isSell);
        }
        if(!is_null($isDelete)){
            if($isDelete != 'true'){
                $isDelete = false;
            }else{
                $isDelete = true;
            }
            $products = $products->where(IekModel::REMOVED,$isDelete);
        }
        $products = $products->get();
        $total = $products->count();
        if($products->isEmpty()){
            $products = null;
        }else{
            if(!is_null($take) && !is_null($skip)){
                $products = $products->slice($skip,$take);
            }
        }
        $data = new \stdClass();
        $data->take = $take;
        $data->skip = $skip;
        $data->total = $total;
        $data->isSell = $isSell;
        $data->isDelete = $isDelete;
        $data->data = $products;
        $err->setData($data);
        return view('thirdProduct.productList',['result'=> $err]);
//        return response()->json($err);
    }

    public function getProduct($id){
        $err = new Error();
        $product = Product::where(IekModel::ID,$id)
            ->with('image.norms')
            ->with('shop')
            ->with(['core.publication'=>function($q){
                $q->with('museum')
                    ->with('author');
            }])
            ->with('produceParams')
            ->with('productIntroduction.introduction.introductionContent.image.norms')
            ->first();
        $err->setData($product);
        return view('thirdProduct.productInfo',['result'=> $err]);
//        return response()->json($err);
    }

    public function getProductByNo($no){
        $err = new Error();
        $product = Product::where('no',$no)
            ->with('image.norms')
            ->with('shop')
            ->with('produceParams')
            ->with(['core.publication'=>function($q){
                $q->with('author.authorIntroduction.introduction.introductionContent')
                    ->with('publicationIntroduction.introduction.introductionContent');
            }])
            ->with('productIntroduction.introduction.introductionContent')
            ->first();
        $err->setData($product);
        return response()->json($err);
    }

    public function changeSell(Request $request,$id){
        $err = new Error();
        $isSell = $request->input('isSell');
        if($isSell != false){
            $isSell = true;
        }
        $product = Product::changeSell($id,$isSell);
        return response()->json($product);
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
        $re = Product::whereIn('id', $ids)
            ->update([IekModel::REMOVED=>$handel]);
        if($re){
            $err->setData($re);
        }else{
            $err->setError(Errors::FAILED);
        }
        return $err;
    }


}