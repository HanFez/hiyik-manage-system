<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\IndexController;
use App\Http\Controllers\TraitRequestParams;
use App\IekModel\Version1_0\Constants\Behavior;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Constants\TableModel;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Privilege;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Requests;
use App\Http\Controllers\Controller;

class PrivilegeController extends Controller
{
    use TraitRequestParams;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $model = new Privilege();
        $type = 'privilege';
        $getList = new IndexController();
        $result = $getList->tableList($model, $type);
        return $result;
    }

    /**
     * 批量删除
     */
    public function del(){
        $model = new Privilege();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }

    /**
     * 批量恢复
     */
    public function recover(){
        $model = new Privilege();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * 验证字段
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function checkField(){
        $behavior = $this->getRequestParam(request(),'behavior');
        $table_name = $this->getRequestParam(request(),'table_name');
        if(is_null($behavior)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'Please enter the behavior!','behavior');
        }
        if(is_null($table_name)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'Please enter the table_name!','table_name');
        }
    }

    /**
     * 分配动作列表页面
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function actionList(){
        $err = new Error();
        $privilege = new Privilege();
        $tableNames = $privilege->getTables();
        $tablePrivilege = Privilege::all();
        foreach ($tableNames as $tableName){
            foreach ($tablePrivilege as $privilege){
                if($privilege->table_name == $tableName->tablename){
                    $tableName->{$privilege->behavior} = $privilege;
                }
            }
        }
        $err->setData($tableNames);
        return view('admin.definedPrivilege', ['result' => $err]);
    }

    /**
     * get one table all record
     *
     * @param $tableName
     * @return mixed
     */
    public function getAll(Request $request , $tableName){
        $start = $request->input('start');
        $res = self::getAllList($request , $tableName);
        if(is_null($start)){
            $params = new \stdClass();
            $params-> type = 'tables';
            $params-> url = 'getAll/'.$tableName;
            $field = $res;
            return view('tableData.index',compact('field', 'params'));
        }
        return response()->json($res);
    }

    public function getAllList(Request $request , $tableName , $limit=null){
        $err = new Error();
        $model = TableModel::TABLE_MODEL[$tableName];
        $model = new $model;
        $privilege = new Privilege();
        $field = $model->tableSchema();
        $start = $request->input('start');
        $length = $request->input('length');
        $draw = $request->input('draw');
        if(is_null($start)){
            return $field;
        }
        $checkTable = $privilege->checkTable($tableName);
        if(!$checkTable){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('invalid tableName');
            return view('message.formResult',['result'=>$err]);
        }
        $list = $privilege->getAllRecord($tableName);
        $order = $request->input('order');
        $columns = $request->input('columns');
        if(!is_null($order)){
            foreach ($order as $column){
                if(!is_null($columns)){
                    $list = $list->orderBy($columns[$column['column']]['data'],$column['dir']);
                }
            }
        }
        if(!is_null($limit)){
            foreach ($limit as $k=>$v){
                $list = $list->where($k,$v);
            }
        }
        $list = $list->get();
        $total = $list -> count();
        if(!is_null($start) && !is_null($length)){
            $list = $list->slice($start,$length)->values();
        }
        $err->setData($list);
        $err->draw = $draw;
        $err->recordsTotal = $total;
        $err->recordsFiltered = $total;
        return $err;
    }

    /**
     * delete table record
     *
     * @param $tableName
     * @param $id
     * @return mixed
     */
    public function deleteRecord(Request $request , $tableName){
        $err = new Error();
        $ids = $request->input('ids');
        if(is_null($ids) || !is_array($ids) || count($ids) == 0){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('ids can not null');
            return view('message.formResult',['result'=>$err]);
        }
        $result = DB::table($tableName)
            ->whereIn(IekModel::ID,$ids)
            ->update([
                IekModel::REMOVED => true
            ]);
        $err->setData($result);
        if(!$result){
            $err->setError(Errors::UNKNOWN_ERROR);
        }
        return response()->json($err);
    }

    /**
     * cover table record
     *
     * @param $tableName
     * @param $id
     * @return mixed
     */
    public function coverRecord(Request $request , $tableName){
        $err = new Error();
        $ids = $request->input('ids');
        if(is_null($ids) || !is_array($ids) || count($ids) == 0){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('ids can not null');
            return view('message.formResult',['result'=>$err]);
        }
        $result = DB::table($tableName)
            ->whereIn(IekModel::ID,$ids)
            ->update([
                IekModel::REMOVED => false
            ]);
        $err->setData($result);
        if(!$result){
            $err->setError(Errors::UNKNOWN_ERROR);
        }
        return response()->json($err);
    }

    /**
     * 分配动作给每张表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function assignAction(Request $request){
        $err = new Error();
        $tableName = $request->input('tableName');
        $privilege = new Privilege();
        $tableCheck = $privilege->checkTable($tableName);
        if(!$tableCheck){
            $err->setError(Errors::INVALID_ACCOUNT);
            $err->setMessage('invalid tableName');
            return view('message.formResult', ['result' => $err]);
        }
        $behaviors = $request->input('behaviors');
        if(is_null($behaviors) || !is_array($behaviors) || count($behaviors) == 0){
            $err->setError(Errors::INVALID_ACCOUNT);
            $err->setMessage('behaviors can not null');
            return view('message.formResult', ['result' => $err]);
        }
        DB::beginTransaction();
        try{
            foreach ($behaviors as $behavior){
                $result = Privilege::where(IekModel::TABLE_NAME,$tableName)
                    ->where(IekModel::BEHAVIOR,$behavior)
                    ->update([
                        IekModel::REMOVED => false
                    ]);
                if(!$result){
                    $result = new Privilege();
                    $result->{IekModel::TABLE_NAME} = $tableName;
                    $result->{IekModel::BEHAVIOR} = $behavior;
                    $result->save();
                }
            }
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult', ['result' => $err]);
        }
        return view('message.formResult', ['result' => $err]);
        return response()->json($err);
    }

    /**
     * 检查该表是否已分配有动作
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function checkAction(){
        $err = new Error();
        $input = request()->input('table_name');
        $checkT = new Privilege();
        $tableCheck = $checkT->checkTable($input);
        if(!$tableCheck){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('invalid table name');
            return view('message.formResult',['result'=>$err]);
        }
        $privilege = Privilege::where(IekModel::TABLE_NAME,$input)
            ->orWhere(function ($query) {
                $query->where(IekModel::BEHAVIOR,'C')
                    ->where(IekModel::BEHAVIOR,'D')
                    ->where(IekModel::BEHAVIOR,'U')
                    ->where(IekModel::BEHAVIOR,'Q')
                    ->where(IekModel::BEHAVIOR,'R');
            })
            ->get();
        $count = count($privilege);
        if($count<5){
            foreach($privilege as $v){
                switch($v['behavior']){
                    case 'C':
                        $behavior[] = 'C';
                        break;
                    case 'D':
                        $behavior[] = 'D';
                        break;
                    case 'Q':
                        $behavior[] = 'Q';
                        break;
                    case 'R':
                        $behavior[] = 'R';
                        break;
                    case 'U':
                        $behavior[] = 'U';
                        break;
                    default:
                        break;
                }
            }
            $err->setData($behavior);
            return response()->json($err);
        }else{
            $err->setData(Behavior::getConstants());
            return response()->json($err);
        }
    }
}
