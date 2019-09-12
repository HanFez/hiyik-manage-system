<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\IndexController;
use App\Http\Controllers\TraitRequestParams;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\Privilege;
use App\IekModel\Version1_0\Role;
use App\IekModel\Version1_0\RolePrivilege;
use App\IekModel\Version1_0\IekModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Controller;

class RoleController extends Controller
{
    use TraitRequestParams;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = new Role();
        $field = $roles->tableSchema();
        $draw = $this->getRequestParam(request(),'draw');
        $skip = $this->getRequestParam(request(),'start');
        $take = $this->getRequestParam(request(),'length');
        $orders = $this->getRequestParam(request(),'order');
        $columns = $this->getRequestParam(request(),'columns');
        $role = $roles->orderBy(IekModel::ID,'desc')->get();
        foreach($role as $v){
            $results[] = $v;
        }
        $total = count($results);
        $data = new \stdClass();
        $data->recordsTotal = $total;
        $data->recordsFiltered = $total;
        $data->draw = $draw;
        if($draw>=1){
            foreach($orders as $order){
                $role = $roles->orderBy($columns[$order['column']]['data'],$order['dir']);
            }
            $result = $role->skip($skip)->take($take)->get();
        }else{
            $result = $results;
        }
        $data->data = $result;
        if(!is_null($skip) && !is_null($take)){
            foreach ($result as $key => $value) {
                $result[$key] = IekModel::doTrans($value, 'name', 'role');
            }
            return response()->json($data);
        }else{
            $params = new \stdClass();
            $params->type = 'role';
            $params->url = 'role';
            return view('tableData.index',compact('result','field','params'));
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $role = new Role();
        $field = $role->tableSchema();
        foreach($field as $k => $v){
            if($v->column_name == 'id') unset($field[$k]);
        }
        return view('tableData.add',compact('field'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $err = new Error();
        if($this->checkField()){
            return $this->checkField();
        }
        $roles = $request->except('_token');
        try{
            DB::beginTransaction();
            $re = Role::create($roles);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'添加成功','添加失败',$re);
    }

    /**
     * Show the form for editing the specified resource.
     * 传给前台的是字段名、属性、类型和内容
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $roles = new Role();
        $field = $roles->tableSchema();
        foreach($field as $k => $v){
            if($v->column_name == 'id') unset($field[$k]);
        }
        $result = Role::find($id);
        return view('tableData.edit',compact('result','field'));
    }

    /**
     * Update the specified resource in storage.
     * 后台接收的是角色id和is_removed的值
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $err = new Error();
        $roleData = $request->except('_token');
        try{
            DB::beginTransaction();
            $re = Role::where(IekModel::ID,$id)->update($roleData);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'修改成功','修改失败',$re);
    }

    /**
     * 批量删除
     */
    public function del(){
        $model = new Role();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }

    /**
     * 批量恢复
     */
    public function recover(){
        $model = new Role();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }

    /**
     * 验证字段
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function checkField(){
        $name = $this->getRequestParam(request(),'name');
        $description = $this->getRequestParam(request(),'description');
        if(is_null($name)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'Please enter the name!','name');
        }
        if(is_null($description)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'Please enter the description!','description');
        }
    }

    /**
     * 角色和权限关系显示页面
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     */
    public function roleRelationPrivilege($id){
        $err = new Error();
        $checkRole = Role::isExists($id);
        if(!$checkRole){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('invalid role id');
            return view('message.formResult',['result'=>$err]);
        }
        $role = new Role();
        $role_field = $role->tableSchema();
        foreach($role_field as $k => $v){
            if($v->column_name == 'id')unset($role_field[$k]);
            if($v->column_name == 'is_active')unset($role_field[$k]);
            if($v->column_name == 'is_removed')unset($role_field[$k]);
            if($v->column_name == 'created_at')unset($role_field[$k]);
            if($v->column_name == 'updated_at')unset($role_field[$k]);
        }
        $rolePrivilege = new RolePrivilege();
        $rolePrivilege_field = $rolePrivilege->tableSchema();

        $privilege = new Privilege();
        $privilege_field = $privilege->tableSchema();
        foreach($privilege_field as $k => $v){
            if($v->column_name == 'id')unset($privilege_field[$k]);
            if($v->column_name == 'is_active')unset($privilege_field[$k]);
            if($v->column_name == 'is_removed')unset($privilege_field[$k]);
            if($v->column_name == 'created_at')unset($privilege_field[$k]);
            if($v->column_name == 'updated_at')unset($privilege_field[$k]);
        }
        $field = array_merge($role_field,$rolePrivilege_field,$privilege_field);
        $draw = $this->getRequestParam(request(),'draw');
        $skip = $this->getRequestParam(request(),'start');
        $take = $this->getRequestParam(request(),'length');
        $orders = $this->getRequestParam(request(),'order');
        $columns = $this->getRequestParam(request(),'columns');
        $results = $rolePrivilege
            ->join('tblRoles','tblRoles.id','tblRolePrivileges.role_id')
            ->join('tblPrivileges','tblRolePrivileges.privilege_id','tblPrivileges.id')
            ->select('tblRoles.name','tblRoles.description','tblPrivileges.table_name',
                'tblPrivileges.behavior','tblRolePrivileges.*')
            ->where(IekModel::ROLE_ID,$id)
            ->get();
        $total = count($results);
        $data = new \stdClass();
        $data->recordsTotal = $total;
        $data->recordsFiltered = $total;
        $data->draw = $draw;
        if($draw>=1){
            foreach($orders as $order){
                $results = $rolePrivilege
                    ->join('tblRoles','tblRoles.id','tblRolePrivileges.role_id')
                    ->join('tblPrivileges','tblRolePrivileges.privilege_id','tblPrivileges.id')
                    ->select('tblRoles.name','tblRoles.description','tblPrivileges.table_name',
                        'tblPrivileges.behavior','tblRolePrivileges.*')
                    ->where(IekModel::ROLE_ID,$id)
                    ->orderBy('tblRolePrivileges.'.$columns[$order['column']]['data'],$order['dir']);
            }
            $result = $results->skip($skip)->take($take)->get();
        }else{
            $result = $results;
        }
        $data->data = $result;
        //dd($data);
        if(!is_null($skip) && !is_null($take)){
            foreach ($result as $key => $value) {
                $result[$key] = IekModel::doTrans($value, 'name', 'role');
            }
            return response()->json($data);
        }else{
            $params = new \stdClass();
            $params-> type = 'role-privilege';
            $params-> url = 'role/relation/'.$id;
            return view('tableData.index',compact('result','field','params'));
        }
    }

    /**
     * get role list
     *
     * @return array mixed
     */
    public function roles(){
        $roles = Role::where(IekModel::REMOVED,false)->get();
        return $roles;
    }

    public function getRoleList(){
        $err = new Error();
        $tableName = Role::getTables();
        $roles = $this->roles();
        $err->setData($roles);
        $err->tables = $tableName;
        return view('admin.allotPrivilege', ['result' => $err]);
    }

    /**
     * allot privilege to role
     *
     * @param Request $request
     * @param $rid
     * @return mixed
     */
    public function allotPrivilege(Request $request , $rid){
        $err = new Error();
        $roleCHeck = Role::isExists($rid);
        if(!$roleCHeck){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('invalid role id');
            return view('message.formResult',['result'=>$err]);
        }
        $privileges = $request->input('behaviors');
        $tableName = $request->input('tableName');

        if(is_null($privileges) || !is_array($privileges) || count($privileges) == 0){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('privilege can not null');
            return view('message.formResult',['result'=>$err]);
        }
        DB::beginTransaction();
        try{
            foreach ($privileges as $privilege){
                $privilegeId = Privilege::where(IekModel::TABLE_NAME,$tableName)
                    ->where(IekModel::BEHAVIOR,$privilege)
                    ->first();
                if(is_null($privilegeId)){
                    DB::rollBack();
                    $err->setError(Errors::INVALID_PARAMS);
                    $err->setMessage('invalid privilege');
                    return view('message.formResult', ['result' => $err]);
                }
                $result = RolePrivilege::where(IekModel::ROLE_ID,$rid)
                    ->where(IekModel::PRIVILEGE_ID,$privilegeId->id)
                    ->update([
                        IekModel::REMOVED => false
                    ]);
                if(!$result){
                    $result = new RolePrivilege();
                    $result->{IekModel::ROLE_ID} = $rid;
                    $result->{IekModel::PRIVILEGE_ID} = $privilegeId->id;
                    $result->save();
                }
            }

            /*$deleteBehaviors = Behavior::getConstants();
            $deleteBehaviors = array_diff($deleteBehaviors,$privileges);
            foreach ($deleteBehaviors as $deleteBehavior){
                $privilegeId = Privilege::where(IekModel::TABLE_NAME,$tableName)
                    ->where(IekModel::BEHAVIOR,$deleteBehavior)
                    ->first();
                if(!is_null($privilegeId)){
                       RolePrivilege::where(IekModel::ROLE_ID,$rid)
                        ->where(IekModel::PRIVILEGE_ID,$privilegeId->id)
                        ->update([
                            IekModel::REMOVED => true
                        ]);
                }
            }*/
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return view('message.formResult', ['result' => $err]);
    }


    /**
     * get role privilege table name
     *
     * @param $id
     * @return mixed
     */
    public function getRoleTable($id){
        $err = new Error();
        $roleCHeck = Role::isExists($id);
        if(!$roleCHeck){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('invalid role id');
            return view('message.formResult',['result'=>$err]);
        }

        $privilege = RolePrivilege::where(IekModel::ROLE_ID,$id)
            ->pluck(IekModel::PRIVILEGE_ID);
        $table = Privilege::whereIn(IekModel::ID,$privilege)
            ->pluck(IekModel::TABLE_NAME)
            ->unique()
            ->values();
        return response()->json($table);
    }

    /**
     * get role table privileges
     *
     * @param $id
     * @param $tableName
     * @return mixed
     */
    public function getRoleTablePrivilege($id , $tableName){
        $privilege = RolePrivilege::where(IekModel::ROLE_ID,$id)
            ->whereHas('privilege',function($query) use($tableName){
                $query->where(IekModel::TABLE_NAME,$tableName);
            })
            ->with('privilege')
            ->where(IekModel::REMOVED,false)
            ->get();
        return response()->json($privilege);
    }
    /**
 * delete role privilege
 */
    public function delPrivilege(){
        $model = new RolePrivilege();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * recover role privilege
     */
    public function recoverPrivilege(){
        $model = new RolePrivilege();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
}
