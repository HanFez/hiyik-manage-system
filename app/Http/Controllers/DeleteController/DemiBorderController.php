<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 5/2/17
 * Time: 2:19 PM
 */

namespace App\Http\Controllers\Table;


use App\Http\Controllers\Admin\PrivilegeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\DemiBorder;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Material;
use App\IekModel\Version1_0\PatternDemiBorder;
use App\IekModel\Version1_0\ProductTemporary;
use App\IekModel\Version1_0\Texture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DemiBorderController extends Controller
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
        return view('admin.systemSetting.product.lineAdd',['result'=>$err]);
    }


    function create(Request $request){
        $demiBorder = self::createDemiBorder($request);
        return view('message.formResult',['result'=>$demiBorder]);
    }

    function createDemiBorder(Request $request){
        $err = new Error();
        $params = self::getCreateParams($request);
        if($params -> statusCode != 0){
            return $params;
        }
        $checkMark = DemiBorder::checkMark($params->data->mark);
        if($checkMark){
            $err->setError(Errors::EXIST);
            $err->setMessage("该编号已使用");
            $err->setData('mark');
            return $err;
        }
        $params = $params -> data;
        $demiBorder = new DemiBorder();
        $demiBorder -> name = $params -> name;
        $demiBorder -> material_id = $params -> materialId;
        $demiBorder -> texture_id = $params -> textureId;
        $demiBorder -> unit = $params -> unit;
        $demiBorder -> description = $params -> description;
        $demiBorder -> line_out_width = $params -> lineOutWidth;
        $demiBorder -> line_out_height = $params -> lineOutHeight;
        $demiBorder -> pressure_draw_width = $params -> pressureDrawWidth;
        $demiBorder -> pressure_draw_height = $params -> pressureDrawHeight;
        $demiBorder -> mark = $params -> mark;
        $demiBorder -> slot_width = $params -> slotWidth;
        $demiBorder -> slot_depth = $params -> slotDepth;
        $demiBorder -> weight = $params -> weight;
        $demiBorder -> price = $params -> price;
        $demiBorder -> currency = $params -> currency;
        $demiBorder -> line_in_width = $params -> lineInHeight;
        $demiBorder -> save();
        $err->setData($demiBorder);
        return $err;
    }

    function getCreateParams(Request $request){
        $err = new Error();
        $name = $request->input('name');
        $unit = $request->input('unit');
        $materialId = $request->input('materialId');
        $textureId = $request->input('textureId');
        if(is_null($name) || is_null($unit) || is_null($materialId) || is_null($textureId)){
            $err->setError(Errors::INVALID_PARAMS);
            return $err;
        }
        $params = new \stdClass();
        $params -> name = $name;
        $params -> materialId = $materialId;
        $params -> textureId = $textureId;
        $params -> unit = $unit;
        $params -> description = $request->input('description');
        $params -> lineOutWidth = $request->input('lineOutWidth');
        $params -> lineOutHeight = $request->input('lineOutHeight');
        $params -> pressureDrawWidth = $request->input('pressureDrawWidth');
        $params -> pressureDrawHeight = $request->input('pressureDrawHeight');
        $params -> mark = $request->input('mark');
        $params -> slotWidth = $request->input('slotWidth');
        $params -> slotDepth = $request->input('slotDepth');
        $params -> weight = $request->input('weight');
        $params -> price = $request->input('price');
        $params -> currency = $request->input('currency');
        $params -> lineInHeight = $request->input('lineInHeight');
        $err->setData($params);
        return $err;
    }

    public function edit($id){
        $err = new Error();
        $demiBorder = DemiBorder::where(IekModel::ID,$id)
            ->where(IekModel::CONDITION)
            ->with(['materials'=>function($q){
                $q->where(IekModel::CONDITION);
            }])
            ->with(['textures'=>function($q){
                $q->where(IekModel::CONDITION);
            }])
            ->first();
        if(is_null($demiBorder)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('无效的ID');
            return view('admin.systemSetting.product.lineEdit',['result'=>$err]);
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
        $data -> demiBorder = $demiBorder;
        $err->setData($data);
        return view('admin.systemSetting.product.lineEdit',['result'=>$err]);
    }

    public function modify(Request $request , $id){
        $err = new Error();
        try{
            DB::beginTransaction();
            DemiBorder::where(IekModel::ID,$id)
                ->update([
                    IekModel::IS_MODIFY => true
                ]);
            $demiBorder = self::createDemiBorder($request);
            if($demiBorder->statusCode != 0){
                throw new \Exception('rollback');
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            if(isset($demiBorder)){
                $err = $demiBorder;
            }
        }
        return view('message.formResult',['result'=>$err]);
    }

    public function lists(Request $request){
        $tableName = 'tblDemiBorders';
        $start = $request->input('start');
        $getAll = new PrivilegeController();
        $res = $getAll->getAllList($request , $tableName);
        if(is_null($start)){
            $params = new \stdClass();
            $params-> type = 'demiBorder';
            $params-> url = 'demiBorder';
            $field = $res;
            return view('tableData.index',compact('field', 'params'));
        }
        return response()->json($res);
    }

    public function delete(Request $request){
        $err = new Error();
        $ids = $request->input('ids');
        if(is_null($ids)){
            $err->setError(Errors::INVALID_PARAMS);
            return response()->json($err);
        }
        DB::beginTransaction();
        try{
            $temporary = ProductTemporary::where(IekModel::CONDITION)->pluck(IekModel::DATA);
            foreach($ids as $id){
                $use_demiBorder = DemiBorder::whereHas('borderPatternDemi.borderPattern.border.productBorder')
                    ->with('borderPatternDemi.borderPattern.border')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_demiBorder)){
                    $res = $this->limitDemi($use_demiBorder);
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
                    $re = DemiBorder::where(IekModel::ID,$id)
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
        if(!$use->borderPatternDemi->isEmpty()){
            $borderId = [];
            foreach($use->borderPatternDemi as $demi){
                if(!is_null($demi->borderPattern)){
                    if(!is_null($demi->borderPattern->border)) {
                        $borderId[] = $demi->borderPattern->border->id;
                    }
                }
            }
            return $borderId;
        }
    }

    public function recover(Request $request){
        $err = new Error();
        $ids = $request->input('ids');
        if(is_null($ids)){
            $err->setError(Errors::INVALID_PARAMS);
            return response()->json($err);
        }
        $model = new DemiBorder();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }

}