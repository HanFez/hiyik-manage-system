<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/12/7
 * Time: 10:56
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Product\LineSize;
use App\IekModel\Version1_0\Product\Material;
use Illuminate\Support\Facades\DB;

class LineSizeController extends Controller
{
/**
     * lineSize's add page
     */
    public function add(){
        $materials = Material::where(IekModel::CONDITION)->get();
        return view('product.define.lineSize',['materials'=>$materials]);
    }
    /**
     * lineSize's add data deal
     */
    public function create(){
        $req = request()->all();
        //验证
        $verify = $this->verify($req,null);
        if($verify) return $verify;
        $length = explode(',',$req['phyLength']);
        $arr = [];
        foreach($length as $k => $v){
            $arr[$k] = intval($v);
        }
        $length = $arr;
        try{
            DB::beginTransaction();
            $lineSize = new LineSize();
            $lineSize->material_id = $req['materialId'];
            $lineSize->phy_length_max = $req['phyLengthMax'];
            $lineSize->phy_length_min = $req['phyLengthMin'];
            $lineSize->phy_length = json_encode($length);
            $lineSize->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"保存成功","保存失败",$lineSize);
    }
    /**
     * lineSize's all data list
     */
    public function listLineSize(){
        $model = new LineSize();
        $type = 'line-size';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * lineSize's data detail
     */
    public function showLineSize(){
        //
    }
    /**
     * lineSize's edit page
     */
    public function edit($id){
        $action = 'edit';
        $materials = Material::where(IekModel::CONDITION)->get();
        $lineSize = LineSize::find($id);
        return view('product.define.lineSize',compact('action','materials','lineSize'));
    }
    /**
     * lineSize's edit data deal
     */
    public function update($id){
        $req_u = request()->all();
        //验证
        $verify = $this->verify($req_u,$id);
        if($verify) return $verify;
        $str = substr($req_u['phyLength'],1,-1);
        $length = explode(',',$str);
        $arr = [];
        foreach($length as $k => $v){
            $arr[$k] = intval($v);
        }
        $length = $arr;
        try{
            DB::beginTransaction();
            $lineSize = LineSize::where(IekModel::ID,$id)
                ->update([
                    IekModel::MATERIAL_ID => $req_u['materialId'],
                    IekModel::LENGTH_MAX => $req_u['phyLengthMax'],
                    IekModel::LENGTH_MIN => $req_u['phyLengthMin'],
                    IekModel::PHY_LENGTH => json_encode($length)
                ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return response()->json($err);
        }
        return $this->curd(Errors::OK,Errors::FAILED,"修改成功","修改失败",$lineSize);
    }
    /**
     * delete lineSize's record
     */
    public function del(){
        $model = new LineSize();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover lineSize's record
     */
    public function cover(){
        $model = new LineSize();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * validate
     */
    public function verify($req,$id){
        $mid = $req['materialId'];
        $lengthMax = $req['phyLengthMax'];
        $lengthMin = $req['phyLengthMin'];
        $length = $req['phyLength'];
        if(is_null($mid)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请选择材料",$mid);
        }
        if(is_null($lengthMax)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请添加最大尺寸",$lengthMax);
        }
        if(is_null($lengthMin)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请添加最小尺寸",$lengthMin);
        }
        if(is_null($length)){
            return $this->viewReturn(Errors::NOT_EMPTY,"请添加可选长度",$length);
        }
        $has = LineSize::where(IekModel::MATERIAL_ID,$mid)
            ->where(IekModel::PHY_LENGTH,json_encode($length))
            ->first();
        if(!is_null($has)){
            if(is_null($id)){
                return $this->viewReturn(Errors::EXIST,"此记录已添加",$mid);
            }else{
                if($has->id != $id){
                    return $this->viewReturn(Errors::EXIST,"此记录已添加",$mid);
                }
            }
        }
    }
}
?>