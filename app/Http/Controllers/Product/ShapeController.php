<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/11/27
 * Time: 14:58
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Product\Shape;
use Illuminate\Support\Facades\DB;

class ShapeController extends Controller
{
/**
     * shape's add page
     */
    public function addShape(){
         return view('product.produce.shape');
    }
    /**
     * shape's add data deal
     */
    public function createShape(){
        $req = request()->all();
        //验证
        $verify = $this->verify($req,null);
        if($verify){
            return $verify;
        }
        if(isset($req['arr'])){
            $bezier = $req['arr']['bezier'];
            $viewportNew = $req['arr']['viewportNew'];
            $svg = $req['arr']['svg'];
        }else{
            $err = new Error();
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage("请输入图形数据");
            return view('message.formResult',['result'=>$err]);
        }
        try{
            DB::beginTransaction();
            $shape = new Shape();
            $shape->name = $req['name'];
            $shape->description = $req['des'];
            $shape->shape = $svg;
            $shape->bezier = $bezier;
            $shape->viewport = $viewportNew;
            $shape->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'添加成功','添加失败',$shape);
    }
    /**
     * shape's all data list
     */
    public function listShape(){
        $model = new Shape();
        $type = 'shape1';
        $getList = new IndexController();
        $result = $getList->tableList($model ,$type);
        return $result;
    }
    /**
     * shape's data detail
     */
    public function showShape(){
        //
    }
    /**
     * shape's edit page
     */
    public function editShape($id){
        $action = 'edit';
        $shape = Shape::find($id);
        return view('product.produce.shape',['action'=>$action,'shape'=>$shape]);
    }
    /**
     * shape's edit data deal
     */
    public function updateShape($id){
        $req_u = request()->all();
        //验证
        $verify = $this->verify($req_u,$id);
        if($verify){
            return $verify;
        }
        try{
            DB::beginTransaction();
            $shape = Shape::where(IekModel::ID,$id)
                ->where(IekModel::CONDITION)
                ->update([
                    IekModel::NAME => $req_u['name'],
                    IekModel::DESC => $req_u['des'],
                    IekModel::SHAPE => $req_u['shape'],
                    IekModel::BEZIER => $req_u['bezier'],
                    IekModel::VIEWPORT => $req_u['viewport'],
                ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err = new Error();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'修改成功','修改失败',$shape);
    }
    /**
     * delete shape's record
     */
    public function delShape(){
        $model = new Shape();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover shape's record
     */
    public function coverShape(){
        $model = new Shape();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * validate
     */
    public function verify($req,$id){
        $name = $req['name'];
        $des = $req['des'];
        if(is_null($name)){
            return $this->viewReturn(Errors::NOT_EMPTY,"形状名称不能为空",$name);
        }
        if(is_null($des)){
            return $this->viewReturn(Errors::NOT_EMPTY,"形状描述不能为空",$des);
        }
        $has_name = Shape::where(IekModel::NAME,$name)->first();
        if(!is_null($has_name)){
            if(is_null($id)){
                return $this->viewReturn(Errors::EXIST,"此形状名称已存在",$name);
            }else{
                if($has_name->id !== $id){
                    return $this->viewReturn(Errors::EXIST,"此形状名称已存在",$name);
                }
            }
        }
    }
}
?>