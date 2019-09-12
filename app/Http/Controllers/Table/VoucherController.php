<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/6/7
 * Time: 19:29
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\ExpirationType;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Voucher;
use App\IekModel\Version1_0\VoucherGetDate;
use App\IekModel\Version1_0\VoucherLimit;
use App\IekModel\Version1_0\VoucherLimitRelation;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{
    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * voucher list
     */
    public function voucherList(){
        $model = new Voucher();
        $type = 'voucher';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * add view
     */
    public function add(){
        return view('admin.voucher.add');
    }

    /**
     * save voucher
     */
    public function create(){
        $err = new Error();
        $input = request()->except('_token');
        $check = $this->checkParam();
        if($check){
            return $this->checkParam();
        }
        try{
            DB::beginTransaction();
            $voucher = new Voucher();
            $voucher -> name = $input['name'];
            $voucher -> description = $input['description'];
            $voucher -> voucher_type = $input['voucherType'];
            $voucher -> figure = $input['figure'];
            $voucher -> currency = $input['currency'];
            $voucher -> is_universal = $input['isUniversal'];
            $voucher -> save();
            foreach($input['expiration'] as $expir) {
                $expiration = new ExpirationType();
                $expiration -> voucher_id = $voucher->id;
                $expiration -> description = $expir['description'];
                $expiration -> value = $expir['val'];
                $expiration -> name = $expir['key'];
                $expiration -> save();
            }

            $getDate = $input['getDate'];
            $voucherGetDate = new VoucherGetDate();
            $voucherGetDate->voucher_id = $voucher->id;
            $voucherGetDate->begin_at = $getDate['beginAt'];
            $voucherGetDate->end_at = $getDate['endAt'];
            $voucherGetDate->save();

            $voucherLimit = new VoucherLimit();
            $voucherLimit -> threshold = $input['threshold'];
            $voucherLimit -> amount = $input['amount'];
            $voucherLimit -> min_fee = $input['minFee'];
            $voucherLimit -> allow_author = $input['allowAuthor'];
            $voucherLimit -> target_type = $input['targetType'];
            $voucherLimit -> save();

            $voucherLimitRelation = new VoucherLimitRelation();
            $voucherLimitRelation -> voucher_id = $voucher->id;
            $voucherLimitRelation -> voucher_limit_id = $voucherLimit->id;
            $re = $voucherLimitRelation -> save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'添加成功','添加失败',$re);
    }

    /**
     * edit view
     */
    public function edit($id){
        $voucher = Voucher::with('voucherLimitRelation.voucherLimit')
            ->with('expiration')
            ->with('voucherGetDate')
            ->find($id);
        return view('admin.voucher.edit',compact('voucher'));
    }

    /**
     * save voucher
     */
    public function update($id){
        $err = new Error();
        $input = request()->except('_token');
        $check = $this->checkParam();
        if($check){
            return $this->checkParam();
        }
        try{
            DB::beginTransaction();
            $voucher = new Voucher();
            $voucher->where(IekModel::ID,$id)
                ->update([
                    'name' => $input['name'],
                    'description' => $input['description'],
                    'voucher_type' => $input['voucherType'],
                    'figure' => $input['figure'],
                    'currency' => $input['currency'],
                    'is_universal' => $input['isUniversal']
                ]);
            $expiration = Voucher::where(IekModel::ID, $id);
            foreach($input['expiration'] as $expir) {
                $expiration->whereHas('expiration', function ($q) use ($expir){
                    $q->where([
                        'name' => $expir['key'],
                        'value' => $expir['val'],
                        'description' => $expir['description']
                    ]);
                });
            }
            $expiration = $expiration->withCount('expiration')->first();
//            dd($expiration);
            if(is_null($expiration) || $expiration->expiration_count != count($input['expiration'])) {
                ExpirationType::where(IekModel::VOUCHER_ID, $id)
                    ->update([IekModel::REMOVED => true]);
                foreach ($input['expiration'] as $expir) {
                    $expiration = new ExpirationType();
                    $expiration -> voucher_id = $id;
                    $expiration -> description = $expir['description'];
                    $expiration -> value = $expir['val'];
                    $expiration -> name = $expir['key'];
                    $expiration->save();
                }
            }
            $getDate = $input['getDate'];
            $voucherGetDate = VoucherGetDate::where(IekModel::CONDITION)
                ->where([
                    'voucher_id' => $id,
                    'begin_at'=> $getDate['beginAt'],
                    'end_at' => $getDate['endAt']
                ])
                ->first();
            if(is_null($voucherGetDate)) {
                VoucherGetDate::where(IekModel::VOUCHER_ID, $id)
                    ->update([IekModel::REMOVED => true]);
                $voucherGetDate = new VoucherGetDate();
                $voucherGetDate -> voucher_id = $id;
                $voucherGetDate -> begin_at = $getDate['beginAt'];
                $voucherGetDate -> end_at = $getDate['endAt'];
                $voucherGetDate->save();
            }

            $voucherLimit = VoucherLimit::where(IekModel::CONDITION)
                ->where([
                    'target_type' => $input['targetType'],
                    'threshold' => $input['threshold'],
                    'amount' => $input['amount'],
                    'min_fee' => $input['minFee'],
                    'allow_author' => $input['allowAuthor']
                ])
                ->first();
            if(is_null($voucherLimit)) {
                $voucherLimit = new VoucherLimit();
                $voucherLimit -> threshold = $input['threshold'];
                $voucherLimit -> amount = $input['amount'];
                $voucherLimit -> min_fee = $input['minFee'];
                $voucherLimit -> allow_author = $input['allowAuthor'];
                $voucherLimit -> target_type = $input['targetType'];
                $voucherLimit->save();
            }

            $voucherLimitRelation = VoucherLimitRelation::where([IekModel::VOUCHER_ID => $id,
                IekModel::VOUCHER_LIMIT_ID => $voucherLimit->id])
                ->where(IekModel::CONDITION)
                ->first();
            if(is_null($voucherLimitRelation)) {
                VoucherLimitRelation::where(IekModel::VOUCHER_ID, $id)
                    ->update([IekModel::REMOVED => true]);
                $voucherLimitRelation = new VoucherLimitRelation();
                $voucherLimitRelation -> voucher_id = $id;
                $voucherLimitRelation -> voucher_limit_id = $voucherLimit -> id;
                $voucherLimitRelation -> save();
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return view('message.formResult', ['result' => $err]);
        return $this->curd(Errors::OK,Errors::FAILED,'修改成功','修改失败');
    }

    /**
     * delete voucher
     */
    public function del(){
        $err = new Error();
        $ids = request()->input('ids');
        if(is_null($ids)){
            $err->setError(Errors::INVALID_PARAMS);
            return view('message.formResult',['result'=>$err]);
        }
        $model = new Voucher();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }

    /**
     * recover voucher
     */
    public function recover(){
        $err = new Error();
        $ids = request()->input('ids');
        if(is_null($ids)){
            $err->setError(Errors::INVALID_PARAMS);
            return view('message.formResult',['result'=>$err]);
        }
        $model = new Voucher();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }

    /**
     * form validate
     */
    public function checkParam(){
        $param = request()->all();
        if(is_null($param['name'])){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入优惠券名字','name');
        }
        if(is_null($param['description'])){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入优惠券描述','description');
        }
        if(is_null($param['voucherType'])){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请选择优惠券类型','voucherType');
        }
        if(is_null($param['expiration'])){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入优惠券时效','expiration');
        }
        if(is_null($param['threshold'])){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入优惠券使用次数','threshold');
        }
        if(is_null($param['amount'])){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入优惠券发行数量','amount');
        }
        if(is_null($param['targetType'])){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入优惠券适用类型','targetType');
        }
    }
}