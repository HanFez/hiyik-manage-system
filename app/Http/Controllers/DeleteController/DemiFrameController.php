<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 5/2/17
 * Time: 8:55 PM
 */

namespace App\Http\Controllers\Table;


use App\Http\Controllers\Admin\PrivilegeController;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\DemiFrame;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Material;
use App\IekModel\Version1_0\ProductTemporary;
use App\IekModel\Version1_0\Texture;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DemiFrameController
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
        return view('admin.systemSetting.product.frameAdd',['result'=>$err]);
    }

    public function create(Request $request){
        $demiFrameCreate = self::createDemiFrame($request);
        return view('message.formResult',['result'=>$demiFrameCreate]);
    }

    public function createDemiFrame(Request $request){
        $err = new Error();
        $params = self::getCreateParams($request);
        if($params -> statusCode != 0){
            return $params;
        }
        $checkMark = DemiFrame::checkMark($params->data->mark);
        if($checkMark){
            $err->setError(Errors::EXIST);
            $err->setMessage("该编号已使用");
            $err->setData('mark');
            return $err;
        }
        $params = $params->data;
        $demiFrame = new DemiFrame();
        $demiFrame -> name = $params -> name;
        $demiFrame -> material_id = $params -> materialId;
        $demiFrame -> mark = $params -> mark;
        $demiFrame -> unit = $params -> unit;
        $demiFrame -> weight = $params -> weight;
        $demiFrame -> texture_id = $params -> textureId;
        $demiFrame -> repeat_width = $params -> repeatWidth;
        $demiFrame -> repeat_height = $params -> repeatHeight;
        $demiFrame -> thick = $params -> thick;
        $demiFrame -> price = $params -> price;
        $demiFrame -> currency = $params -> currency;
        $demiFrame -> description = $params -> description;
        $demiFrame -> save();
        $err->setData($demiFrame);
        return $err;

    }

    public function getCreateParams(Request $request){
        $err = new Error();
        $name = $request->input('name');
        $materialId = $request->input('materialId');
        $mark = $request->input('mark');
        $unit = $request->input('unit');
        $textureId = $request->input('textureId');
        if(is_null($name) || is_null($materialId) || is_null($mark) || is_null($unit) || is_null($textureId)){
            $err->setError(Errors::INVALID_PARAMS);
            return $err;
        }
        $params = new \stdClass();
        $params -> name = $name;
        $params -> materialId = $materialId;
        $params -> mark = $mark;
        $params -> unit = $unit;
        $params -> weight = $request->input('weight');
        $params -> textureId = $textureId;
        $params -> repeatWidth = $request->input('repeatWidth');
        $params -> repeatHeight = $request->input('repeatHeight');
        $params -> thick = $request->input('thick');
        $params -> price = $request->input('price');
        $params -> currency = $request->input('currency');
        $params -> description = $request->input('description');
        $err->setData($params);
        return $err;
    }

    public function edit($id){
        $err = new Error();
        $demiFrame = DemiFrame::where(IekModel::ID,$id)
            ->where(IekModel::CONDITION)
            ->with(['materials'=>function($q){
                $q->where(IekModel::CONDITION);
            }])
            ->with(['textures'=>function($q){
                $q->where(IekModel::CONDITION);
            }])
            ->first();
        if(is_null($demiFrame)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('无效的ID');
            return view('admin.systemSetting.product.frameEdit',['result'=>$err]);
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
        $data -> demiFrame = $demiFrame;
        $err->setData($data);
        return view('admin.systemSetting.product.frameEdit',['result'=>$err]);
    }

    public function modify(Request $request , $id){
        $err = new Error();
        try{
            DB::beginTransaction();
            DemiFrame::where(IekModel::ID,$id)
                ->update([
                    IekModel::IS_MODIFY => true
                ]);
            $demiFrame = self::createDemiFrame($request);
            if($demiFrame->statusCode != 0){
                throw new \Exception('rollback');
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            if(isset($demiFrame)){
                $err = $demiFrame;
            }
        }
        return view('message.formResult',['result'=>$err]);
    }

    public function lists(Request $request){
        $tableName = 'tblDemiFrames';
        $start = $request->input('start');
        $getAll = new PrivilegeController();
        $res = $getAll->getAllList($request , $tableName);
        if(is_null($start)){
            $params = new \stdClass();
            $params-> type = 'demiFrame';
            $params-> url = 'demiFrame';
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
                $use_demiFrame = DemiFrame::whereHas('framePatternDemi.framePattern.frame.productFrame')
                    ->with('framePatternDemi.framePattern.frame')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_demiFrame)){
                    $res = $this->limitDemi($use_demiFrame);
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
                    $re = DemiFrame::where(IekModel::ID,$id)
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
        if(!$use->framePatternDemi->isEmpty()){
            $frameId = [];
            foreach($use->framePatternDemi as $demi){
                if(!is_null($demi->framePattern)){
                    if(!is_null($demi->framePattern->frame)) {
                        $frameId[] = $demi->framePattern->frame->id;
                    }
                }
            }
            return $frameId;
        }
    }
    public function recover(Request $request){
        $err = new Error();
        $ids = $request->input('ids');
        if(is_null($ids)){
            $err->setError(Errors::INVALID_PARAMS);
            return response()->json($err);
        }
        $model = new DemiFrame();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
}