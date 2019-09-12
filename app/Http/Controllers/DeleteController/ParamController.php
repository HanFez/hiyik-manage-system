<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/3/6
 * Time: 11:50
 */
namespace App\Http\Controllers\Table;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Param;
use Illuminate\Support\Facades\DB;

class ParamController extends Controller
{
    /**
     * 参数列表
     */
    public function paramList(){
        $model = new Param();
        $type = 'param';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * 添加参数页面
     */
    public function paramAdd(){
        return view('admin.systemSetting.product.paramAdd');
    }
    /**
     *提交添加数据
     */
    public function create(){
        $err = new Error();
        DB::beginTransaction();
        try{
            $err = $this->createParam();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
        }
        return view('message.formResult',['result'=>$err]);
    }
    public function createParam(){
        $err = new Error();
        $input = request()->except('_token');
        $ckParam = $this->checkParam();
        if($ckParam->statusCode != 0){
            return $ckParam;
        }
        $param = new Param();
        $param -> name = $input['paramName'];
        $param -> type = $input['paramType'];
        $param -> description = $input['paramDescription'];
        $param -> save();
        $err->setData($param);

        return $err;
    }
    /**
     * 验证字段
     */
    public function checkParam(){
        $err = new Error();
        $name = request()->input('paramName');
        $type = request()->input('paramType');
        if(is_null($name) || empty($name)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('请输入名称');
            $err->setData('paramName');
            return $err;
        }
        if(is_null($type) || empty($type)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('请输入类型');
            $err->setData('paramType');
            return $err;
        }
        $ckParam = Param::checkNameAndType($name,$type);
        if($ckParam){
            $err->setError(Errors::EXIST);
            $err->setMessage('这个参数已经存在了');
            return $err;
        }
        return $err;
    }
    /**
     * 修改参数页面
     */
    public function paramEdit($id){
        $param = Param::where(IekModel::CONDITION)->find($id);
        return view('admin.systemSetting.product.paramEdit',compact('param'));
    }
    /**
     * 提交修改数据
     */
    public function modifyParam($id){
        $err = new Error();
        DB::beginTransaction();
        try{
            Param::where(IekModel::ID,$id)
                ->update([
                    IekModel::IS_MODIFY => true
                ]);
            $err = $this->createParam();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
        }
        return view('message.formResult',['result'=>$err]);
    }
    /**
     * 删除参数
     */
    public function deleteParam(){
        $model = new Param();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * 恢复参数
     */
    public function recoverParam(){
        $model = new Param();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
}
?>