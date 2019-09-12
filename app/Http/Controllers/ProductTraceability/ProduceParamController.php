<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 1/9/18
 * Time: 2:06 PM
 */

namespace app\Http\Controllers\ProductTraceability;


use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Constants\ImportExcel;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ProductTraceability\ProduceParam;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ProduceParamController extends Controller
{
    public function checkExist($no){
        $err = new Error();
        $exist = ProduceParam::where('product_no',$no)
            ->where(IekModel::CONDITION)
            ->first();
        $err->setData($exist);
        return response()->json($err);
    }
    public function import(Request $request){
        $err = new Error();
        try{
            $realPath = $request->file('fileName');
            $data = Excel::load($realPath, function($reader) {
            })->all()->toArray();
            $params = [];
            $nos = [];
            $produceFiled = ImportExcel::PRODUCE_PARAMS;
            foreach ($data as $v){
                if(!is_null($v[$produceFiled['product_no']]) && trim($v[$produceFiled['product_no']]) != ''){
                    $param = [];
                    $nos[] = trim($v[$produceFiled['product_no']]);
                    $param['product_no'] = trim($v[$produceFiled['product_no']]);
                    $param['core_no'] = trim($v[$produceFiled['core_no']]);
                    $param['core_size'] = trim($v[$produceFiled['core_size']]);
                    $param['border_no'] = trim($v[$produceFiled['border_no']]);
                    $param['border_size'] = trim($v[$produceFiled['border_size']]);
                    $param['core_nail'] = trim($v[$produceFiled['core_nail']]);
                    $param['back_nail'] = trim($v[$produceFiled['back_nail']]);
                    $param['flannel_size'] = trim($v[$produceFiled['flannel_size']]);
                    $param['flannel_width'] = trim($v[$produceFiled['flannel_width']]);
                    $param['coating'] = trim($v[$produceFiled['coating']]);
                    $param['hide_hook'] = trim($v[$produceFiled['hide_hook']]);
                    $param['hook'] = trim($v[$produceFiled['hook']]);
                    $param['core_material'] = trim($v[$produceFiled['core_material']]);
                    $param['wire_rope'] = trim($v[$produceFiled['wire_rope']]);
                    $param['line_locker'] = trim($v[$produceFiled['line_locker']]);
                    $param['mount'] = trim($v[$produceFiled['mount']]);
                    $param['back_size'] = trim($v[$produceFiled['back_size']]);
                    $param['frame_size'] = trim($v[$produceFiled['frame_size']]);
                    $param['frame_width'] = trim($v[$produceFiled['frame_width']]);
                    $params[] = $param;
                }
            }
            $exist = ProduceParam::where(IekModel::CONDITION)
                ->whereIn('product_no',$nos)
                ->get();
            if(!$exist->isEmpty()){
                $exist = $exist->toArray();
                $exists = [];
                foreach($params as $key => $item) {
                    foreach($exist as $value) {
                        if($item['product_no'] == $value['product_no']) {
                            $item['line'] = $key + 2;
                            array_push($exists, $item);
                        }
                    }
                }
                $err->setError(Errors::EXIST);
                $err->setData($exists);
            }else{
                ProduceParam::insert($params);
                $err->setData($params);
            }
        }catch (\Exception $e){
            $err->setError(Errors::FAILED);
            $err->setMessage($e->getMessage());
        }
        return response()->json($err);
    }

}