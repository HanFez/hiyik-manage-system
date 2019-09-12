<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/28
 * Time: 17:36
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Product\Product;
use App\IekModel\Version1_0\Product\ProductDefine;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    /**
     * get product list
     */
    public function productList(Request $request){
        $err = new Error();
        $take = $request->input('take');
        $skip = $request->input('skip');
        $title = $request->input('title');
        $type = $request->input('type');
        if(!is_null($title)){
            $did = $this->getProductsByName($title);
        }
        switch($type){
            case 'all':
                $products = $this->all();
                break;
            case 'modify':
                $products = $this->modify();
                break;
            case 'change':
                $products = $this->change();
                break;
            case 'active':
                $products = $this->active();
                break;
            default:
                break;
        }
        //dd($products);
        if(!is_null($title)){
            $products = $products->whereIn(IekModel::PRODUCT_DID,$did);
        }
        $count = $products->count();
        if($take != null && $skip != null){
            $products = $products->slice($skip,$take);
        }
        $err->setData($products);
        $err -> total = $count;
        $err -> skip = $skip;
        $err -> take = $take;
        $err -> search = $title;
        $err -> type = $type;
        return view('product.productList',['result'=>$err]);
    }

    /**
     * @return mixed
     * all product
     */
    public function all(){
        $products = Product::where(IekModel::CONDITION)
            ->with('productThumb.thumb.norm')
            ->with('productDefine')
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        return $products;
    }

    /**
     * @return mixed
     * is_modify = true;
     */
    public function modify(){
        $products = Product::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,true)
            ->with('productThumb.thumb.norm')
            ->with('productDefine')
            ->orderBy(IekModel::UPDATED,'desc')
            ->get();
        return $products;
    }

    /**
     * @return mixed
     * is_changed = true;
     */
    public function change(){
        $products = Product::where(IekModel::CONDITION)
            ->where(IekModel::CHANGED,true)
            ->with('productThumb.thumb.norm')
            ->with('productDefine')
            ->orderBy(IekModel::UPDATED,'desc')
            ->get();
        return $products;
    }

    /**
     * @return mixed
     * is_active = false;
     */
    public function active(){
        $products = Product::where(IekModel::CONDITION)
            ->where(IekModel::ACTIVE,false)
            ->with('productThumb.thumb.norm')
            ->with('productDefine')
            ->orderBy(IekModel::UPDATED,'desc')
            ->get();
        return $products;
    }
    /**
     * @param $title
     * @return mixed
     * get search content
     */
    public function getProductsByName($title){
        $did = ProductDefine::where(IekModel::NAME,'like','%'.$title.'%')->pluck(IekModel::ID);
        return $did;
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * product detail
     */
    public function product($id){
        $err = new Error();
        $product = Product::where(IekModel::CONDITION)
            ->with('productDefine')
            ->with(['border'=>function($query){
                $query->with('materialDefine.facade')
                    ->with('material')
                    ->with('line');
            }])
            ->with(['core'=>function($query) {
                $query->with('material')
                    ->with('materialDefine.facade')
                    ->with('coreHandle')
                    ->with(['coreContent.content' => function ($q) {
                        $q->with('corePublication.title.title.description')
                            ->with('image.norms');
                    }]);
            }])
            ->with(['frame'=>function($query){
                $query->with('material')
                    ->with('materialDefine.facade')
                    ->with('frameHole.shape');
            }])
            ->with(['front'=>function($query){
                $query->with('material')
                    ->with('materialDefine.facade');
            }])
            ->with(['back'=>function($query){
                $query->with('material')
                    ->with('materialDefine.facade');
            }])
            ->with(['backFacade'=>function($query){
                $query->with('material')
                    ->with('materialDefine.facade');
            }])
            ->with(['show'=>function($query){
                $query->with('material')
                    ->with('show');
            }])
            ->with('productThumb.thumb.norm')
            ->with('postMaker.maker')
            ->with('person.personNick.nick')
            ->find($id);
        $price = $this->productPrice($product);
        $err->borderPrice = round($price['border'],2);
        $err->coreMaterial = round($price['coreMaterial'],2);
        $err->coreHandle = round($price['coreHandle'],2);
        $err->framePrice = round($price['frame'],2);
        $err->frontPrice = round($price['front'],2);
        $err->backPrice = round($price['back'],2);
        $err->backFacadePrice = round($price['backFacade'],2);
        $err->price = round(array_sum($price),2);
        $err->setData($product);
        return view('product.product',['result'=>$err]);
    }

    /**
     * @param $product
     * @return array
     * product material price
     */
    public function productPrice($product){
        $border = $product->border;
        if(!is_null($border)){
            $borderDosage = $border->dosage;
            $borderSetPrice = $border->materialDefine->price;
            $borderPrice = $this->transLength($border->dosage_unit,$borderSetPrice,$borderDosage);
        }else{
            $borderPrice = null;
        }
        $core = $product->core;
        if(!is_null($core)){
            $coreDosage = $core->dosage;
            $coreSetPrice = $core->materialDefine->price;
            $coreMaterialPrice = $this->transArea($core->dosage_unit,$coreSetPrice,$coreDosage);
            $handlePrice = $core->coreHandle->price;
            foreach($core->coreContent as $content){
                $contentDosage = $content->dosage;
                $coreContentPrice[] = $this->transArea($content->dosage_unit,$handlePrice,$contentDosage);
            }
            $coreContentPrice = array_sum($coreContentPrice);
        }else{
            $coreMaterialPrice = null;
            $coreContentPrice = null;
        }
        $frame = $product->frame;
        if(!$frame->isEmpty()){
            foreach($frame as $fe){
                $frameDosage = $fe->dosage;
                $frameSetPrice = $fe->materialDefine->price;
                $frameMaterialPrice = $this->transArea($fe->dosage_unit,$frameSetPrice,$frameDosage);
                if(!$fe->frameHole->isEmpty()){
                    $holePrice = [];
                    foreach($fe->frameHole as $hole){
                        $holePrice[] = $hole->price;
                    }
                }else{
                    $holePrice = [];
                }
                $framePrice[] = $frameMaterialPrice+array_sum($holePrice);
            }
        }else{
            $framePrice = [];
        }
        $framePrice = array_sum($framePrice);
        $front = $product->front;
        if(!is_null($front)){
            $frontDosage = $front->dosage;
            $frontSetPrice = $front->materialDefine->price;
            $frontPrice = $this->transArea($front->dosage_unit,$frontSetPrice,$frontDosage);
        }else{
            $frontPrice = null;
        }
        $back = $product->back;
        if(!is_null($back)){
            $backDosage = $back->dosage;
            $backSetPrice = $back->materialDefine->price;
            $backPrice = $this->transArea($back->dosage_unit,$backSetPrice,$backDosage);
        }else{
            $backPrice = null;
        }
        $backFacade = $product->backFacade;
        if(!is_null($back)){
            $backFacadeDosage = $backFacade->dosage;
            $backFacadeSetPrice = $backFacade->materialDefine->price;
            $backFacadePrice = $this->transArea($backFacade->dosage_unit,$backFacadeSetPrice,$backFacadeDosage);
        }else{
            $backFacadePrice = null;
        }
        $price = [
            'border'=>$borderPrice,
            'coreMaterial' => $coreMaterialPrice,
            'coreHandle' => $coreContentPrice,
            'frame'=>$framePrice,
            'front'=>$frontPrice,
            'back'=>$backPrice,
            'backFacade'=>$backFacadePrice
        ];
        return $price;
    }

    /**
     * @param $unit
     * @param $setPrice
     * @param $dosage
     * @return float
     * unit area trans
     */
    public function transArea($unit,$setPrice,$dosage){
        switch($unit){
            case 'sqm':
                $price = floatval($setPrice*$dosage);
                break;
            case 'sqdm':
                $price = floatval($setPrice*$dosage/100);
                break;
            case 'sqcm':
                $price = floatval($setPrice*$dosage/10000);
                break;
            case 'sqmm':
                $price = floatval($setPrice*$dosage/1000000);
                break;
        }
        return $price;
    }

    /**
     * @param $unit
     * @param $setPrice
     * @param $dosage
     * @return float
     * unit length trans
     */
    public function transLength($unit,$setPrice,$dosage){
        switch ($unit){
            case 'm':
                $price = floatval($setPrice*$dosage);
                break;
            case 'dm':
                $price = floatval($setPrice*$dosage/10);
                break;
            case 'cm':
                $price = floatval($setPrice*$dosage/100);
                break;
            case 'mm':
                $price = floatval($setPrice*$dosage/1000);
                break;
        }
        return $price;
    }
}
?>