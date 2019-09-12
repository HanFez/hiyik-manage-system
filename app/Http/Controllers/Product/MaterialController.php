<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/11/27
 * Time: 15:17
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ManageLogs;
use App\IekModel\Version1_0\Product\Material;
use App\IekModel\Version1_0\Reason;
use Illuminate\Support\Facades\DB;

class MaterialController extends Controller
{
/**
     * Material's add page
     */
    public function addMaterial(){
        return view('product.produce.material');
    }
    /**
     * Material's add data deal
     */
    public function createMaterial(){
        $req = request()->all();
        //验证
        $verify = $this->verify($req,null);
        if($verify) return $verify;
        try{
            DB::beginTransaction();
            $material = new Material();
            $material->name = $req['name'];
            $material->description = $req['des'];
            $material->serial_no = $req['serial_no'];
            $material->weight = $req['weight'];
            $material->weight_unit = $req['weightUnit'];
            $material->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'添加成功','添加失败',$material);
    }
    /**
     * Material's all data list
     */
    public function listMaterial(){
        $model = new Material();
        $type = 'material1';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * Material's data detail
     */
    public function showMaterial(){
        //
    }
    /**
     * Material's edit page
     */
    public function editMaterial($id){
        $action = 'edit';
        $material = Material::where(IekModel::CONDITION)->find($id);
        return view('product.produce.material',['action'=>$action,'material'=>$material]);
    }
    /**
     * Material's edit data deal
     */
    public function updateMaterial($id){
        $req = request()->all();
        //验证
        $verify = $this->verify($req,$id);
        if($verify) return $verify;
        try{
            DB::beginTransaction();
            $material = Material::where(IekModel::ID,$id)
                ->update([
                    IekModel::NAME => $req['name'],
                    IekModel::DESC => $req['des'],
                    IekModel::SERIAL_NO => $req['serial_no'],
                    IekModel::WEIGHT => $req['weight'],
                    IekModel::WEIGHT_UNIT => $req['weightUnit']
                ]);
            /*$reason = Reason::insertReason('修改材料','modify');
            $log = new ManageLogs();
            $log -> operator_id = session('login.id');
            $log -> reason_id = $reason;
            $log -> table_name = $material->getDataTable();
            $log -> row_id =$id;
            $log -> content = json_encode(\App\IekModel\Version1_0\Product\Material::getRecords([IekModel::ID => $id]));
            $log -> save();*/

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'修改成功','修改失败',$material);
    }
    /**
     * delete Material's record
     */
    public function delMaterial(){
        $model = new Material();
        $getList = new IndexController();
        $result = $getList->tableDelete($model);
        return $result;
    }
    /**
     * recover Material's record
     */
    public function coverMaterial(){
        $model = new Material();
        $getList = new IndexController();
        $result = $getList->tableRecover($model);
        return $result;
    }
    /**
     * validate
     */
    public function verify($req,$id){
        $name = $req['name'];
        $des = $req['des'];
        $serial = $req['serial_no'];
        $weight = $req['weight'];
        $unit = $req['weightUnit'];
        if(is_null($name)){
            return $this->viewReturn(Errors::NOT_EMPTY,"材料名称不能为空",$name);
        }
        if(is_null($des)){
            return $this->viewReturn(Errors::NOT_EMPTY,"材料描述不能为空",$des);
        }
        if(is_null($serial)){
            return $this->viewReturn(Errors::NOT_EMPTY,"材料序列号不能为空",$serial);
        }
        if(is_null($weight)){
            return $this->viewReturn(Errors::NOT_EMPTY,"重量不能为空",$weight);
        }
        if(is_null($unit)){
            return $this->viewReturn(Errors::NOT_EMPTY,"重量单位不能为空",$unit);
        }
        $has_name = Material::where(IekModel::SERIAL_NO,$serial)
            ->where(IekModel::CONDITION)
            ->first();
        if(!is_null($has_name)){
            if(is_null($id)){
                return $this->viewReturn(Errors::EXIST,"此材料已存在",$serial);
            }else{
                if($has_name->id !== $id){
                    return $this->viewReturn(Errors::EXIST,"此材料已存在",$serial);
                }
            }
        }
    }
}
?>