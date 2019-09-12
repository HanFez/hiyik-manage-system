<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/14
 * Time: 16:31
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Product\Material;
use App\IekModel\Version1_0\Product\PosterShowDefine;
use App\IekModel\Version1_0\Product\PosterShowMaterialDefine;
use Illuminate\Support\Facades\DB;

class ShowMaterialDefineController extends Controller
{
/**
     * ShowMaterialDefine's add page
     */
    public function add(){
        $materials = Material::where(IekModel::CONDITION)->get();
        $shows = PosterShowDefine::where(IekModel::CONDITION)->get();
        return view('product.define.showMaterialDefine',['materials'=>$materials,'shows'=>$shows]);
    }
    /**
     * ShowMaterialDefine's add data deal
     */
    public function create(){
        $err = new Error();
        $req = request()->all();
        if($req['isDefault'] == "") $req['isDefault'] = false;
        //验证
        $verify = $this->verify($req,null);
        if($verify) return $verify;
        $model = new Material();
        $limit = self::limitOther($model,$req['materialId']);
        if(is_null($limit)){
            $err->setError(Errors::NOT_FOUND);
            $err->setMessage('材料关系不完整，请先补充材料纹理关联数据再来添加。');
            return view('message.formResult',['result'=>$err]);
        }
        try{
            DB::beginTransaction();
            $core = new PosterShowMaterialDefine();
            $core->material_id = $req['materialId'];
            $core->show_id = $req['showId'];
            $core->amount = $req['amount'];
            $core->is_default = $req['isDefault'];
            $core->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"保存成功","保存失败",$core);
    }
    /**
     * ShowMaterialDefine's all data list
     */
    public function listSMD(){
        $model = new PosterShowMaterialDefine();
        $type = 'show-material-define';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * ShowMaterialDefine's data detail
     */
    public function showSMD(){
        //
    }
    /**
     * ShowMaterialDefine's edit page
     */
    public function edit($id){
        $action = 'edit';
        $smd = PosterShowMaterialDefine::find($id);
        $materials = Material::where(IekModel::CONDITION)->get();
        $shows = PosterShowDefine::where(IekModel::CONDITION)->get();
        return view('product.define.showMaterialDefine',compact('materials','shows','action','smd'));
    }
    /**
     * ShowMaterialDefine's edit data deal
     */
    public function update($id){
        $err = new Error();
        $req_u = request()->all();
        if($req_u['isDefault'] == "") $req_u['isDefault'] = false;
        //验证
        $verify = $this->verify($req_u,$id);
        if($verify) return $verify;
        $model = new Material();
        $limit = self::limitOther($model,$req_u['materialId']);
        if(is_null($limit)){
            $err->setError(Errors::NOT_FOUND);
            $err->setMessage('材料关系不完整，请先补充材料纹理关联数据再来添加。');
            return view('message.formResult',['result'=>$err]);
        }
        try{
            DB::beginTransaction();
            $core = PosterShowMaterialDefine::where(IekModel::ID,$id)
                ->update([
                    IekModel::MATERIAL_ID => $req_u['materialId'],
                    IekModel::SHOW_ID => $req_u['showId'],
                    IekModel::AMOUNT => $req_u['amount'],
                    IekModel::IS_DEFAULT => $req_u['isDefault']
                ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"修改成功","修改失败",$core);
    }
    /**
     * delete ShowMaterialDefine's record
     */
    public function del(){
        $model = new PosterShowMaterialDefine();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover ShowMaterialDefine's record
     */
    public function cover(){
        $model = new PosterShowMaterialDefine();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * validate
     */
    public function verify($req,$id){
        $sid = $req['showId'];
        $mid= $req['materialId'];
        if(is_null($sid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择装饰",$sid);
        }
        if(is_null($mid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择材料",$mid);
        }
        $sm = PosterShowMaterialDefine::where(IekModel::SHOW_ID,$sid)
            ->where(IekModel::MATERIAL_ID,$mid)
            ->first();
        if(is_null($id)){
            if(!is_null($sm)){
                return  $this->viewReturn(Errors::EXIST,"该记录已添加",$sid);
            }
        }else{
            if(!is_null($sm) && $sm->id != $id){
                return $this->viewReturn(Errors::EXIST,"该记录已添加",$sid);
            }
        }
    }
}
?>