<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/8/14
 * Time: 10:48
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Accessory;
use App\IekModel\Version1_0\Cart;
use App\IekModel\Version1_0\CartProduct;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;

class CartController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * cart person list
     */
    public function cartList(){
        $err = new Error();
        $take = request()->input('take');
        $skip = request()->input('skip');
        $carts = Cart::with('person.personNick.nick')
            ->where(IekModel::CONDITION)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        $err->total = count($carts);
        if(!is_null($take) && !is_null($skip)){
            $carts = $carts->slice($skip,$take);
        }
        $err->setData($carts);
        $err->take = $take;
        $err->skip = $skip;
        return view('admin.cart.cartList',['result'=>$err]);
    }

    public function cartInfo($id){
        $err = new Error();
        $take = request()->input('take');
        $skip = request()->input('skip');
        $carts = CartProduct::with(['products'=>function($query){
            $query->with('productDefine')
                ->with('border.materialDefine')
                ->with('core.materialDefine')
                ->with('frame.materialDefine')
                ->with('front.materialDefine')
                ->with('back.materialDefine')
                ->with('backFacade.materialDefine')
                ->with('productThumb.thumb.norm');
        }])
            ->where(IekModel::CONDITION)
            ->where(IekModel::CART_ID,$id)
            ->where(IekModel::HAVE_PURCHASED,false)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        $err->total = count($carts);
        if(!is_null($take) && !is_null($skip)){
            $carts = $carts->slice($skip,$take);
        }
        $err->setData($carts);
        $err->take = $take;
        $err->cartId = $id;
        $err->skip = $skip;
        return view('admin.cart.cart',['result'=>$err]);
    }
}