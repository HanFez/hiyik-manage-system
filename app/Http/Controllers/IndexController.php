<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/1/4
 * Time: 14:44
 */
namespace App\Http\Controllers;

use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    /**
     * @param $models
     * @param $type
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * index view
     */
    public function tableList($models , $type){
        $field = $models::tableSchema();
        $draw = request()->input('draw');
        $skip = request()->input('start');
        $take = request()->input('length');
        $orders =  request()->input('order');
        $columns =  request()->input('columns');
        $tableData = $models::orderBy(IekModel::ID)
            ->get();
        $total = count($tableData);
        $data = new \stdClass();
        $data->recordsTotal = $total;
        $data->recordsFiltered = $total;
        $data->draw = $draw;
        if($draw>=1){
            foreach($orders as $order){
                $tableData = $models::orderBy($columns[$order['column']]['data'],$order['dir']);
            }
            $result = $tableData->skip($skip)->take($take)->get();
        }else{
            $result = $models::orderBy(IekModel::ID)
                ->get();
        }
        $data->data = $result;
        if(!is_null($skip) && !is_null($take) && isset($skip) && isset($take) && isset($total)){
            return response()->json($data);
        }else{
            $params = new \stdClass();
            $params-> type = $type;
            $params-> url = $type;
            return view('tableData.index',compact('field','params'));
        }
    }

    /**
     * @param $model
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * delete button
     */
    public function tableDelete($model){
        $err = new Error();
        $ids = request()->input('ids');
        DB::beginTransaction();
        try{
            $re = $model::whereIn(IekModel::ID, $ids)
                ->update([
                    IekModel::REMOVED => true
                ]);
            $err->setData($re);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        if($re){
            $err->setError(Errors::OK);
            $err->setMessage('删除成功');
        }else{
            $err->setError(Errors::FAILED);
            $err->setMessage('删除失败');
        }
        return response()->json($err);
    }

    /**
     * @param $model
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * delete button
     */
    public function tableRecover($model){
        $err = new Error();
        $ids = request()->input('ids');
        DB::beginTransaction();
        try{
            $re = $model::whereIn(IekModel::ID, $ids)
                ->update([
                    IekModel::REMOVED => false
                ]);
            $err->setData($re);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        if($re){
            $err->setError(Errors::OK);
            $err->setMessage('恢复成功');
        }else{
            $err->setError(Errors::FAILED);
            $err->setMessage('恢复失败');
        }
        return response()->json($err);
    }
}
?>