<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/13
 * Time: 15:31
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Product\Handle;
use App\IekModel\Version1_0\Product\Material;
use App\IekModel\Version1_0\Product\PosterCoreDefine;
use Illuminate\Support\Facades\DB;

class CoreDefineController extends Controller
{
/**
     * CoreDefine's add page
     */
    public function add(){
        $materials = Material::where(IekModel::CONDITION)->get();
        $handles = Handle::where(IekModel::CONDITION)->get();
        return view('product.define.coreDefine',['materials'=>$materials,'handles'=>$handles]);
    }
    /**
     * CoreDefine's add data deal
     */
    public function create(){
        $err = new Error();
        $req = request()->all();
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
            $core = new PosterCoreDefine();
            $core->material_id = $req['materialId'];
            $core->handle_id = $req['handleId'];
            $core->price = $req['price'];
            $core->price_unit = $req['priceUnit'];
            $core->currency = $req['currency'];
            $core->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"保存成功","保存失败",$core);
    }
    /**
     * CoreDefine's all data list
     */
    public function listCore(){
        $model = new PosterCoreDefine();
        $type = 'core-define';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * CoreDefine's data detail
     */
    public function showCore(){
        //
    }
    /**
     * CoreDefine's edit page
     */
    public function edit($id){
        $materials = Material::where(IekModel::CONDITION)->get();
        $handles = Handle::where(IekModel::CONDITION)->get();
        $action = 'edit';
        $core = PosterCoreDefine::find($id);
        return view('product.define.coreDefine',compact('materials','handles','action','core'));
    }
    /**
     * CoreDefine's edit data deal
     */
    public function update($id){
        $err = new Error();
        $req_u = request()->all();
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
            $core = PosterCoreDefine::where(IekModel::ID,$id)
                ->update([
                    IekModel::MATERIAL_ID => $req_u['materialId'],
                    IekModel::HANDLE_ID => $req_u['handleId'],
                    IekModel::PRICE => $req_u['price'],
                    IekModel::PRICE_UNIT => $req_u['priceUnit'],
                    IekModel::CURRENCY => $req_u['currency']
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
     * delete CoreDefine's record
     */
    public function del(){
        $model = new PosterCoreDefine();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover CoreDefine's record
     */
    public function cover(){
        $model = new PosterCoreDefine();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * validate
     */
    public function verify($req,$id){
        $mid = $req['materialId'];
        $hid = $req['handleId'];
        $unit = $req['priceUnit'];
        if(is_null($mid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择材料",$mid);
        }
        if(is_null($hid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择工艺",$hid);
        }
        if(is_null($unit)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请输入单位",$unit);
        }
        $has = PosterCoreDefine::where(IekModel::MATERIAL_ID,$mid)
            ->where(IekModel::HANDLE_ID,$hid)
            ->first();
        if(!is_null($has)){
            if(is_null($id)){
                return $this->viewReturn(Errors::EXIST,'此记录已添加',$mid);
            }else{
                if($has->id != $id){
                    return $this->viewReturn(Errors::EXIST,'此记录已添加',$mid);
                }
            }
        }
    }
}
?>