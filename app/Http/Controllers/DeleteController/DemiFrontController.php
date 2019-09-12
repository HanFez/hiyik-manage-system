<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 5/2/17
 * Time: 8:54 PM
 */

namespace App\Http\Controllers\Table;


use App\Http\Controllers\Admin\PrivilegeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\DemiFront;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Material;
use App\IekModel\Version1_0\ProductTemporary;
use App\IekModel\Version1_0\Texture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DemiFrontController extends Controller
{

    public function add(Request $request){
        $err = new Error();
        $material = Material::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $texture = Texture::where(IekModel::CONDITION)
            ->with(['textureImages'=>function($q){
                $q->where(IekModel::CONDITION);
            }])
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        if($material->isEmpty()){
            $material = null;
        }
        if($texture->isEmpty()){
            $texture = null;
        }
        $data = new \stdClass();
        $data -> material = $material;
        $data -> texture = $texture;
        $err->setData($data);
        return view('admin.systemSetting.product.frontAdd',['result'=>$err]);
    }

    public function create(Request $request){
        $demiFront = self::createDemiFront($request);
        return view('message.formResult',['result'=>$demiFront]);
    }

    public function createDemiFront(Request $request){
        $err = new Error();
        $params = self::getCreateParams($request);
        if($params -> statusCode != 0){
            return $params;
        }
        $checkMark = DemiFront::checkMark($params->data->mark);
        if($checkMark){
            $err->setError(Errors::EXIST);
            $err->setMessage("该编号已使用");
            $err->setData('mark');
            return $err;
        }
        $params = $params->data;
        $demiFront = new DemiFront();
        $demiFront -> name = $params -> name;
        $demiFront -> material_id = $params -> materialId;
        $demiFront -> mark = $params -> mark;
        $demiFront -> unit = $params -> unit;
        $demiFront -> weight = $params -> weight;
        $demiFront -> texture_id = $params -> textureId;
        $demiFront -> thick = $params -> thick;
        $demiFront -> price = $params -> price;
        $demiFront -> currency = $params -> currency;
        $demiFront -> description = $params -> description;
        $demiFront -> save();
        return $err;
    }

    public function getCreateParams(Request $request){
        $err = new Error();
        $name = $request->input('name');
        $materialId = $request->input('materialId');
        $mark = $request->input('mark');
        $unit = $request->input('unit');
        if(is_null($name) || is_null($materialId) || is_null($mark) || is_null($unit)){
            $err->setError(Errors::INVALID_PARAMS);
            return $err;
        }
        $params = new \stdClass();
        $params -> name = $name;
        $params -> materialId = $materialId;
        $params -> mark = $mark;
        $params -> unit = $unit;
        $params -> weight = $request->input('weight');
        $params -> textureId = $request->input('textureId');
        $params -> thick = $request->input('thick');
        $params -> price = $request->input('price');
        $params -> currency = $request->input('currency');
        $params -> description = $request->input('description');
        $err->setData($params);
        return $err;
    }

    public function edit($id){
        $err = new Error();
        $demiFront = DemiFront::where(IekModel::ID,$id)
            ->where(IekModel::CONDITION)
            ->with(['materials'=>function($q){
                $q->where(IekModel::CONDITION);
            }])
            ->with(['textures'=>function($q){
                $q->where(IekModel::CONDITION);
            }])
            ->first();
        if(is_null($demiFront)){
            $err->setError(Errors::INVALID_PARAMS);
            return view('admin.systemSetting.product.frontEdit',['result'=>$err]);
        }
        $material = Material::where(IekModel::CONDITION)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        $texture = Texture::where(IekModel::CONDITION)
            ->with(['textureImages'=>function($q){
                $q->where(IekModel::CONDITION);
            }])
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        if($material->isEmpty()){
            $material = null;
        }
        if($texture->isEmpty()){
            $texture = null;
        }
        $data = new \stdClass();
        $data -> material = $material;
        $data -> texture = $texture;
        $data -> demiFront = $demiFront;
        $err->setData($data);
        return view('admin.systemSetting.product.frontEdit',['result'=>$err]);
    }

    public function modify(Request $request , $id){
        $err = new Error();
        try{
            DB::beginTransaction();
            DemiFront::where(IekModel::ID,$id)
                ->update([
                    IekModel::IS_MODIFY => true
                ]);
            $demiFront = self::createDemiFront($request);
            if($demiFront->statusCode != 0){
                throw new \Exception('rollback');
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            if(isset($demiFront)){
                $err = $demiFront;
            }
        }
        return view('message.formResult',['result'=>$err]);
    }

    public function lists(Request $request){
        $tableName = 'tblDemiFronts';
        $start = $request->input('start');
        $getAll = new PrivilegeController();
        $res = $getAll->getAllList($request , $tableName);
        if(is_null($start)){
            $params = new \stdClass();
            $params-> type = 'demiFront';
            $params-> url = 'demiFront';
            $field = $res;
            return view('tableData.index',compact('field', 'params'));
        }
        return response()->json($res);
    }

    public function delete(Request $request){
        $err = new Error();
        $ids = request()->input('ids');
        if(is_null($ids)){
            $err->setError(Errors::INVALID_PARAMS);
            return response()->json($err);
        }
        DB::beginTransaction();
        try{
            $temporary = ProductTemporary::where(IekModel::CONDITION)->pluck(IekModel::DATA);
            foreach($ids as $id){
                $use_demiFront = DemiFront::whereHas('frontPatternDemi.frontPattern.front.productFront')
                    ->with('frontPatternDemi.frontPattern.front')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_demiFront)){
                    $res = $this->limitDemi($use_demiFront);
                    foreach($res as $re){
                        foreach($temporary as $tem){
                            $str = substr_count($tem,$re);
                            if($str>0){
                                $err->setError(Errors::INVALID_PARAMS);
                                $err->setMessage('有产品草稿正在使用这条数据，请不要删除');
                                return response()->json($err);
                            }
                        }
                    }
                    $err->setError(Errors::INVALID_PARAMS);
                    $err->setMessage('有产品正在使用这条数据，请不要删除');
                    return response()->json($err);
                }else{
                    $re = DemiFront::where(IekModel::ID,$id)
                        ->update([
                            IekModel::REMOVED => true
                        ]);
                    if($re){
                        $err->setError(Errors::OK);
                        $err->setMessage('删除成功');
                    }else{
                        $err->setError(Errors::FAILED);
                        $err->setMessage('删除失败');
                    }
                }
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
        }
        return response()->json($err);
    }

    public function limitDemi($use){
        if(!$use->frontPatternDemi->isEmpty()){
            $frontId = [];
            foreach($use->frontPatternDemi as $demi){
                if(!is_null($demi->frontPattern)){
                    if(!is_null($demi->frontPattern->front)) {
                        $frontId[] = $demi->frontPattern->front->id;
                    }
                }
            }
            return $frontId;
        }
    }

    public function recover(Request $request){
        $err = new Error();
        $ids = $request->input('ids');
        if(is_null($ids)){
            $err->setError(Errors::INVALID_PARAMS);
            return response()->json($err);
        }
        $model = new DemiFront();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
}