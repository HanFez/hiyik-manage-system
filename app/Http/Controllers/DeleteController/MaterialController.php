<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 5/3/17
 * Time: 2:04 PM
 */

namespace App\Http\Controllers\Table;


use App\Http\Controllers\Admin\PrivilegeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Material;
use App\IekModel\Version1_0\ProductTemporary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
    public function lists(Request $request){
        $tableName = 'tblMaterials';
        $start = $request->input('start');
        $getAll = new PrivilegeController();
        $res = $getAll->getAllList($request , $tableName);
        if(is_null($start)){
            $params = new \stdClass();
            $params-> type = 'material';
            $params-> url = 'material';
            $field = $res;
            return view('tableData.index',compact('field', 'params'));
        }
        return response()->json($res);
    }
    public function create(Request $request){
        $material = self::createMaterial($request);
        return view('message.formResult',['result'=>$material]);
    }

    public function createMaterial(Request $request){
        $err = new Error();
        $name = $request->input('name');
        if(is_null($name)){
            $err->setError(Errors::INVALID_PARAMS);
            return $err;
        }
        $material = new Material();
        $material -> name = $name;
        $material -> description = $request->input('description');
        $material -> save();
        return $err;
    }

    public function edit($id){
        $err = new Error();
        $material = Material::find($id);
        if(is_null($material)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('无效的ID');
            return view('admin.systemSetting.product.materialEdit',['result'=>$err]);
        }
        $err->setData($material);
        return view('admin.systemSetting.product.materialEdit',['result'=>$err]);
    }

    public function modify(Request $request , $id){
        $err = new Error();
        try{
            DB::beginTransaction();
            Material::where(IekModel::ID,$id)
                ->update([
                    IekModel::IS_MODIFY => true
                ]);
            $material = self::createMaterial($request);
            if($material->statusCode != 0){
                throw new \Exception('rollback');
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            if(!is_null($material)){
                $err = $material;
            }
        }
        return view('message.formResult',['result'=>$err]);
    }


    /**
     * 删除材料
     */
    public function deleteMaterial(){
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
                $use_border = Material::whereHas('demiBorder.borderPatternDemi.borderPattern.border.productBorder')
                    ->with('demiBorder.borderPatternDemi.borderPattern.border')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_border)){
                    $res = $this->limitBorder($use_border);
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
                }
                $use_back = Material::whereHas('demiBack.backPatternDemi.backPattern.back.productBack')
                    ->with('demiBack.backPatternDemi.backPattern.back')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_back)){
                    $res = $this->limitBack($use_back);
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
                }
                $use_core = Material::whereHas('demiCore.corePatternDemi.corePattern.core.productCore')
                    ->with('demiCore.corePatternDemi.corePattern.core')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_core)){
                    $res = $this->limitCore($use_core);
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
                }
                $use_frame = Material::whereHas('demiFrame.framePatternDemi.framePattern.frame.productFrame')
                    ->with('demiFrame.framePatternDemi.framePattern.frame')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_frame)){
                    $res = $this->limitFrame($use_frame);
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
                }
                $use_front = Material::whereHas('demiFront.frontPatternDemi.frontPattern.front.productFront')
                    ->with('demiFront.frontPatternDemi.frontPattern.front')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_front)){
                    $res = $this->limitFront($use_front);
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
                }
                if(!is_null($use_border) || !is_null($use_back) || !is_null($use_core) || !is_null($use_frame) ||
                    !is_null($use_front)){
                    $err->setError(Errors::INVALID_PARAMS);
                    $err->setMessage('有产品正在使用这条数据，请不要删除');
                    return response()->json($err);
                }else{
                    $re = Material::where(IekModel::ID,$id)
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
    public function limitBorder($use){
        if(!$use->demiBorder->isEmpty()){
            $borderId = [];
            foreach($use->demiBorder as $demi){
                if(!$demi->borderPatternDemi->isEmpty()){
                    foreach($demi->borderPatternDemi as $value){
                        if(!is_null($value->borderPattern)){
                            if(!is_null($value->borderPattern->border)) {
                                $borderId[] = $value->borderPattern->border->id;
                            }
                        }
                    }
                }
            }
        }
        return $borderId;
    }
    public function limitBack($use){
        if(!$use->demiBack->isEmpty()){
            $backId = [];
            foreach($use->demiBack as $demi){
                if(!$demi->backPatternDemi->isEmpty()){
                    foreach($demi->backPatternDemi as $value){
                        if(!is_null($value->backPattern)){
                            if(!is_null($value->backPattern->back)) {
                                $backId[] = $value->backPattern->back->id;
                            }
                        }
                    }
                }
            }
        }
        return $backId;
    }
    public function limitCore($use){
        if(!$use->demiCore->isEmpty()){
            $coreId = [];
            foreach($use->demiCore as $demi){
                if(!$demi->corePatternDemi->isEmpty()){
                    foreach($demi->corePatternDemi as $value){
                        if(!is_null($value->corePattern)){
                            if(!is_null($value->corePattern->core)) {
                                $coreId[] = $value->corePattern->core->id;
                            }
                        }
                    }
                }
            }
        }
        return $coreId;
    }
    public function limitFrame($use){
        if(!$use->demiFrame->isEmpty()){
            $frameId = [];
            foreach($use->demiFrame as $demi){
                if(!$demi->framePatternDemi->isEmpty()){
                    foreach($demi->framePatternDemi as $value){
                        if(!is_null($value->framePattern)){
                            if(!is_null($value->framePattern->frame)) {
                                $frameId[] = $value->framePattern->frame->id;
                            }
                        }
                    }
                }
            }
        }
        return $frameId;
    }
    public function limitFront($use){
        if(!$use->demiFront->isEmpty()){
            $frontId = [];
            foreach($use->demiFront as $demi){
                if(!$demi->frontPatternDemi->isEmpty()){
                    foreach($demi->frontPatternDemi as $value){
                        if(!is_null($value->frontPattern)){
                            if(!is_null($value->frontPattern->front)) {
                                $frontId[] = $value->frontPattern->front->id;
                            }
                        }
                    }
                }
            }
        }
        return $frontId;
    }
    /**
     * 恢复材料
     */
    public function recoverMaterial(){
        $model = new Material();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
}