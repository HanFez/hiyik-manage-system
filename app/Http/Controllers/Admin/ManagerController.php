<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\IndexController;
use App\Http\Controllers\TraitRequestParams;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Employee;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\ManagerRole;
use App\IekModel\Version1_0\Role;
use App\IekModel\Version1_0\IekModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Manager;
use Illuminate\Support\Facades\Hash;

class ManagerController extends Controller
{
    use TraitRequestParams;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $manager_filed = Manager::tableSchema();
        $employee_field = Employee::tableSchema();
        foreach($employee_field as $k => $v){
            if($v->column_name !== 'name'){
                unset($employee_field[$k]);
            }
        }
        $field = array_merge($manager_filed,$employee_field);
        $draw = $this->getRequestParam(request(),'draw');
        $skip = $this->getRequestParam(request(),'start');
        $take = $this->getRequestParam(request(),'length');
        $orders = $this->getRequestParam(request(),'order');
        $columns = $this->getRequestParam(request(),'columns');
        $results = Manager::join('tblEmployees','tblManagers.id','tblEmployees.id')
            ->select('tblEmployees.name','tblManagers.*')
            ->get();
        $total = count($results);
        $data = new \stdClass();
        $data->recordsTotal = $total;
        $data->recordsFiltered = $total;
        $data->draw = $draw;
        if($draw>=1){
            foreach($orders as $order){
                $manager = Manager::join('tblEmployees','tblManagers.id','tblEmployees.id')
                                ->select('tblEmployees.name','tblManagers.*')
                                ->orderBy('tblManagers.'.$columns[$order['column']]['data'],$order['dir']);
            }
            $result = $manager->skip($skip)->take($take)->get();
        }else{
            $result = $results;
        }
        $data->data = $result;
        if(!is_null($skip) && !is_null($take) && isset($skip) && isset($take) && isset($total)){
            return response()->json($data);
        }else{
            $params = new \stdClass();
            $params->type = 'manager';
            $params->url = 'manager';
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
        $err = new Error();
        $employees = Employee::doesntHave('manager')->get();
        $type = 'add';
        $err->type = $type;
        $err->setData($employees);
        return view('admin.addManager',compact('err'));
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
        if($this->checkFiled()){
            return $this->checkFiled();
        }
        $manager = $request->except('_token');
        DB::beginTransaction();
        try{
            $manager['password'] = Hash::make($manager['password']);
            $re = Manager::create($manager);
            $admin = Manager::where(IekModel::CONDITION)->find('2222');
            /*if(!is_null($admin)){
                Manager::where(IekModel::ID,'2222')
                    ->update([
                        IekModel::REMOVED => true
                    ]);
            }*/
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult', ['result' => $err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'保存成功','保存失败',$re);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $err = new Error();
        $type = 'edit';
        $err->type = $type;
        $err->id = $id;
        return view('admin.addManager',compact('err'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $err = new Error();
        if($this->checkPassword()){
            return $this->checkPassword();
        }
        $manager = $request->except('_token');
        DB::beginTransaction();
        try{
            $manager['password'] = Hash::make($manager['password']);
            unset($manager['confirmPassword']);
            $re = Manager::where(IekModel::ID,$id)->update($manager);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult', ['result' => $err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'修改成功','修改失败',$re);
    }

    /**
     * 批量删除
     */
    public function del(){
        $model = new Manager();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }

    /**
     * 批量恢复
     */
    public function recover(){
        $model = new Manager();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }

    /**
     * check whether the filed is empty
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function checkFiled(){
        $manager_id = $this->getRequestParam(request(),'id');
        $password = $this->getRequestParam(request(),'password');
        $confirmPassword = $this->getRequestParam(request(),'confirmPassword');
        if(is_null($manager_id)){
           return $this->viewReturn(Errors::INVALID_PARAMS,'Please enter the manager id!','id');
        }
        if(is_null($password)){
           return $this->viewReturn(Errors::INVALID_PARAMS,'Please enter the password!','password');
        }
        if(!is_null($confirmPassword)){
            if($confirmPassword !== $password){
                return $this->viewReturn(Errors::INVALID_PARAMS,'Two passwords are not consistent','confirmPassword');
            }
        }else{
            return $this->viewReturn(Errors::INVALID_PARAMS,'Please enter the confirmPassword!','confirmPassword!');
        }
    }

    /**
     * 验证密码
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function checkPassword(){
        $password = $this->getRequestParam(request(),'password');
        $confirmPassword = $this->getRequestParam(request(),'confirmPassword');
        if(is_null($password)){
            return $this->viewReturn(Errors::INVALID_PARAMS,'Please enter the password!','password');
        }
        if(!is_null($confirmPassword)){
            if($confirmPassword !== $password){
                return $this->viewReturn(Errors::INVALID_PARAMS,'Two passwords are not consistent','confirmPassword');
            }
        }else{
            return $this->viewReturn(Errors::INVALID_PARAMS,'Please enter the confirmPassword!','confirmPassword!');
        }
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * get manager's roles
     */
    public function managerRelationRole($id){
        $err = new Error();
        $ckManager = Manager::isExists($id);
        if(!$ckManager){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('Invalid manager id !');
            return view('message.formResult', ['result' => $err]);
        }
        $managerRole_field = ManagerRole::tableSchema();
        $role_field = Role::tableSchema();
        foreach($role_field as $k => $v){
            if($v->column_name == 'id') unset($role_field[$k]);
            if($v->column_name == 'is_active') unset($role_field[$k]);
            if($v->column_name == 'is_removed') unset($role_field[$k]);
            if($v->column_name == 'created_at') unset($role_field[$k]);
            if($v->column_name == 'updated_at') unset($role_field[$k]);
            if($v->column_name == 'name') $v->column_name = 'role_name';
        }
        $field = array_merge($role_field,$managerRole_field);
        $draw = $this->getRequestParam(request(),'draw');
        $skip = $this->getRequestParam(request(),'start');
        $take = $this->getRequestParam(request(),'length');
        $orders = $this->getRequestParam(request(),'order');
        $columns = $this->getRequestParam(request(),'columns');
        $results = ManagerRole::join('tblRoles','tblManagerRoles.role_id','tblRoles.id')
                        ->select('tblManagerRoles.*', 'tblRoles.name as role_name', 'tblRoles.description')
                        ->where(IekModel::MANAGER_ID,$id)
                        ->get();
        $total = count($results);
        $data = new \stdClass();
        $data->recordsTotal = $total;
        $data->recordsFiltered = $total;
        $data->draw = $draw;
        if($draw>=1){
            foreach($orders as $order){
                $results = ManagerRole::join('tblRoles','tblManagerRoles.role_id','tblRoles.id')
                    ->select('tblManagerRoles.*', 'tblRoles.name as role_name', 'tblRoles.description')
                    ->where(IekModel::MANAGER_ID,$id)
                    ->orderBy('tblManagerRoles.'.$columns[$order['column']]['data'],$order['dir']);
            }
            $result = $results->skip($skip)->take($take)->get();
        }else{
            $result = $results;
        }
        $data->data = $result;
        if(!is_null($skip) && !is_null($take)){
            foreach ($result as $key => $value) {
                $result[$key] = IekModel::doTrans($value, 'role_name', 'role');
            }
            return response()->json($data);
        }else{
            $params = new \stdClass();
            $params-> type = 'manager-role';
            $params-> url = 'manager/relation/'.$id;
            return view('tableData.index',compact('field','params'));
        }
    }

    /**
     * get manager list
     *
     * @return mixed
     */
    public function getManagerMessage(){
        $managers = Manager::with('employee')->get();
        return $managers;
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * 管理员角色分配页
     */
    public function managerRoles(){
        $err = new Error();
        $manager = $this->getManagerMessage();
        $roles = new RoleController();
        $roles = $roles->roles();
        $err->manager = $manager;
        $err->roles = $roles;
        return view('admin.allotRole', ['result' => $err]);

    }

    /**
     * get manager roles
     *
     * @param $id
     * @return mixed
     */
    public function getManagerRoles($id){
        $err = new Error();
        $roles = ManagerRole::where(IekModel::MANAGER_ID,$id)
            ->with('roles')
            ->where(IekModel::REMOVED,false)
            ->get();
        $err->setData($roles);
        return response()->json($err);
    }


    /**
     * add role to manager
     *
     * @param Request $request
     * @param $id
     * @return mixed
     */
    public function allotRole(Request $request , $id){
        $err = new Error();
        $checkManager = Manager::checkManager($id);
        if(!$checkManager){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('invalid manager id');
            return view('message.formResult', ['result' => $err]);
        }
        $roles = $request->input('roles');
        if(is_null($roles) || !is_array($roles) || count($roles) == 0){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('roles can not null');
            return view('message.formResult', ['result' => $err]);
        }
        DB::beginTransaction();
        try{
            foreach ($roles as $role){
                $result = ManagerRole::where(IekModel::MANAGER_ID,$id)
                    ->where(IekModel::ROLE_ID,$role)
                    ->update([
                        IekModel::REMOVED => false
                    ]);
                if(!$result){
                    $result = new ManagerRole();
                    $result->{IekModel::MANAGER_ID} = $id;
                    $result->{IekModel::ROLE_ID} = $role;
                    $result->save();
                }
            }

            /*$rolesId = Role::all()->pluck('id');
            //比较键值，返回差集，与第一个数组比较
            $rolesId = array_diff($rolesId->toArray(),$roles);
            ManagerRole::where(IekModel::MANAGER_ID,$id)
                ->whereIn(IekModel::ROLE_ID,$rolesId)
                ->update([
                    IekModel::REMOVED => true
                ]);*/
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'成功','失败',$result);
    }

    /**
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * delete manager role
     */
    public function delManagerRole(){
        $model = new ManagerRole();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    public function coverManagerRole(){
        $model = new ManagerRole();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
}
