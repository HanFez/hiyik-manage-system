<?php

namespace App\Http\Controllers\Table;

use App\Http\Controllers\Operation\DeleteController;
use App\IekModel\Version1_0\IekModel;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\SystemSetting;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\Constants\Errors;
use Illuminate\Support\Facades\DB;

class SystemSettingController extends Controller
{
    /**
     * system setting list
     */
    public function systemSettingList(){
        $err = new Error();
        $systems = SystemSetting::orderBy(IekModel::CREATED,'desc')->get();
        foreach($systems as $system){
            $system->content = json_decode($system->content);
        }
        $err->setData($systems);
        return view('admin.systemSetting.systemSettings',['result'=>$err]);
    }
    /**
     * add parents element
     */
    public function addParentElement(){
        $err = new Error();
        $name = request()->input('name');
        if(is_null($name)){
            return $this->viewReturn(Errors::NOT_EMPTY,'名字不能为空','name');
        }else{
            $res = SystemSetting::where(IekModel::NAME,$name)
                ->where(IekModel::CONDITION)
                ->count();
            if($res){
                return $this->viewReturn(Errors::EXIST,'该名字已存在','name');
            }
        }
        $description = request()->input('description');
        if(is_null($description)){
            return $this->viewReturn(Errors::NOT_EMPTY,'描述不能为空','description');
        }
        $content = request()->input('content');
        if(is_null($content)){
            return $this->viewReturn(Errors::NOT_EMPTY,'内容不能为空','content');
        }
        DB::beginTransaction();
        try{
            $content = json_encode($content);
            $system = new SystemSetting();
            $system->name = $name;
            $system->description = $description;
            $system->content = $content;
            $re = $system->save();

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'成功','失败',$re);
    }
    /**
     * show systemSetting with given type
     *
     * @param Request $request
     * @return mixed
     */
    public function getSystemSetting(Request $request){
        $err = new Error();
        $type = $request->input('type');//操作类型，第一次进入只是查看，类型为null，点击添加或修改或删除等才不为null
        if(is_null($type)){
            $sys = SystemSetting::orderby(IekModel::ID,'desc')->get();
        }else{
            $sys = SystemSetting::where(IekModel::NAME,$type)->get();
            if($sys->isEmpty()){
                $err->setError(Errors::INVALID_PARAMS);
                $err->setMessage('invalid type');
                return view('admin.systemSetting.systemSettings',['system'=>$err]);
            }
        }
        foreach ($sys as $con){
            $con -> content = json_decode($con->content);
        }
        $err->setData($sys);
        return view('admin.systemSetting.systemSettings',['result'=>$err]);
    }

    /**
     * modify or add systemSetting
     *
     * @param Request $request
     * @return mixed
     */
    public function saveSystemSettingParent(Request $request){
        $err = new Error();
        $id = $request->input('id');
        $content = $request->input('content');

        $checkData = $this->checkSystemSettingParent($request);
        if($checkData){
            return $checkData;
        }
        if(is_null($id)){//add
            DB::beginTransaction();
            try{
                $content[0][IekModel::ACTIVE] = true;
                $content[0][IekModel::REMOVED] = false;
                $content[0][IekModel::CREATED] = date("Y-m-d H:i:s");
                $content[0][IekModel::UPDATED] = date("Y-m-d H:i:s");
                $content[0]['is_default'] = false;
                $content = json_encode($content);
                $result = new SystemSetting();
                $result->name = $request->input('name');
                $result->description = $request->input('description');
                $result->content = $content;
                $re = $result->save();
                DB::commit();
            }catch (\Exception $e){
                DB::rollBack();
                $err->setError(Errors::UNKNOWN_ERROR);
                $err->setMessage($e->getMessage());
                return view('message.formResult',['result'=>$err]);
            }
            return $this->curd(Errors::OK,Errors::FAILED,'保存成功','保存失败',$re);
        }else{//modify
            DB::beginTransaction();
            try{
                $re = SystemSetting::where(IekModel::ID,$id)
                    ->update([
                        IekModel::NAME => $request->input('name'),
                        IekModel::DESC => $request->input('description')
                    ]);
                DB::commit();
            }catch (\Exception $e){
                DB::rollBack();
                $err->setError(Errors::UNKNOWN_ERROR);
                $err->setMessage($e->getMessage());
                return view('message.formResult',['result'=>$err]);
            }
            return $this->curd(Errors::OK,Errors::FAILED,'修改成功','修改失败',$re);
        }
    }

    public function checkSystemSettingParent(Request $request){
        $id = $request->input('id');
        $content = $request->input('content');
        $name = $request->input('name');
        $description = $request->input('description');
        if(is_null($id)){//add
            if(is_null($name)){
                return $this->viewReturn(Errors::NOT_EMPTY,'名字不能为空','name');
            }else{
                $nck = SystemSetting::where(IekModel::CONDITION)->where(IekModel::NAME,$name)->count();
                if($nck){
                    return $this->viewReturn(Errors::EXIST,'该名字已使用','name');
                }
            }
            if(is_null($description)){
                return $this->viewReturn(Errors::NOT_EMPTY,'描述不能为空','description');
            }
            if(is_null($content) || count($content) == 0){
                return $this->viewReturn(Errors::NOT_EMPTY,'内容不能为空','content');
            }
        }else{//modify
            $sck = SystemSetting::isExist($id);
            if(!$sck){
                return $this->viewReturn(Errors::INVALID_PARAMS,'不存在的ID','id');
            }
            if(is_null($name)){
                return $this->viewReturn(Errors::NOT_EMPTY,'名字不能为空','name');
            }else{
                $nck = SystemSetting::checkSystemSettingName($name,$id);
                if(!$nck){
                    return $this->viewReturn(Errors::EXIST,'该名字已使用','name');
                }
            }
            if(is_null($description)){
                return $this->viewReturn(Errors::NOT_EMPTY,'描述不能为空','description');
            }
        }
    }
    /**
     * modify or add systemSetting child
     *
     * @param Request $request
     * @return mixed
     */
    public function saveSystemSettingChild(Request $request){
        $err = new Error();
        $id = $request->input('id');
        $content = $request->input('content');
        $index = $request->input('index');
        //check $content data here
        $systemContentCheck = $this->checkSystemSettingChild($request);
        if(!is_null($systemContentCheck)){
            return view('message.formResult',['result'=>$systemContentCheck]);
        }
        if(is_null($index)){//add
            DB::beginTransaction();
            try{
                $child = SystemSetting::where(IekModel::ID,$id)->value('content');
                $child = json_decode($child);
                $content[IekModel::ACTIVE] = true;
                $content[IekModel::REMOVED] = false;
                $content[IekModel::CREATED] = date("Y-m-d H:i:s");
                $content[IekModel::UPDATED] = date("Y-m-d H:i:s");
                $content['is_default'] = false;
                $content = $this->arrayToObject($content);
                $child[] = $content;

                $systemSetting = new SystemSetting();
                $re = $systemSetting->where(IekModel::ID,$id)
                    ->update([
                        IekModel::CONTENT => json_encode($child)
                    ]);
                DB::commit();
            }catch (\Exception $e){
                DB::rollBack();
                $err->setError(Errors::UNKNOWN_ERROR);
                $err->setMessage($e->getMessage());
                return view('message.formResult',['result'=>$err]);
            }
            return $this->curd(Errors::OK,Errors::FAILED,'保存成功','保存失败',$re);
        }else{//modify
            DB::beginTransaction();
            try{
                $cont = SystemSetting::where(IekModel::ID,$id)->value('content');
                $cont = json_decode($cont);
                if(!isset($cont[$index])){
                    $err->setError(Errors::INVALID_PARAMS);
                    $err->setMessage('无效的索引');
                    return view('message.formResult',['result'=>$err]);
                }
                $content[IekModel::ACTIVE] = $cont[$index]->is_active;
                $content[IekModel::REMOVED] = $cont[$index]->is_removed;
                $content[IekModel::CREATED] = $cont[$index]->created_at;
                $content[IekModel::UPDATED] = date("Y-m-d H:i:s");
                $content['is_default'] = $cont[$index]->is_default;
                $content = $this->arrayToObject($content);
                $cont[$index] = $content;

                $re = SystemSetting::where(IekModel::ID,$id)
                    ->update([
                        IekModel::CONTENT => json_encode($cont)
                    ]);
                DB::commit();
            }catch (\Exception $e){
                DB::rollBack();
                $err->setError(Errors::UNKNOWN_ERROR);
                $err->setMessage($e->getMessage());
                return view('message.formResult',['result'=>$err]);
            }
            return $this->curd(Errors::OK,Errors::FAILED,'修改成功','修改失败',$re);
        }
    }

    /**
     * check systemSetting content data format
     *
     * @param Request $request
     * @return bool|string
     */
    public function checkSystemSettingChild(Request $request){
        $err = new Error();
        $id = $request->input('id');
        $content = $request->input('content');
        if(is_null($id)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('无效果的操作');
            return $err;
        }else{
            $sck = SystemSetting::isExist($id);
            if(!$sck){
                $err->setError(Errors::INVALID_PARAMS);
                $err->setMessage('无效的ID');
                return $err;
            }
        }
        if(is_null($content)){
            $err->setError(Errors::NOT_EMPTY);
            $err->setMessage('内容不能为空');
            return $err;
        }
    }
    /**
     * delete systemSetting
     *
     * @param $id
     * @return mixed
     */
    public function deleteParent($id){
        $err = new Error();
        $re = SystemSetting::where(IekModel::ID,$id)
            ->update([
                    IekModel::REMOVED => true
                ]);
        $err->setData($re);
        if($re){
            $err->setError(Errors::OK);
            $err->setMessage('删除成功');
        }else{
            $err->setError(Errors::FAILED);
            $err->setMessage('删除失败');
        }
        return view('message.formResult',['result'=>$err]);
    }

    /**
     * cover systemSetting
     *
     * @param $id
     * @return mixed
     */
    public function coverParent($id){
        $err = new Error();
        $re = SystemSetting::where(IekModel::ID,$id)
            ->update([
                IekModel::REMOVED => false
            ]);
        $err->setData($re);
        if($re){
            $err->setError(Errors::OK);
            $err->setMessage('恢复成功');
        }else{
            $err->setError(Errors::FAILED);
            $err->setMessage('恢复失败');
        }
        return view('message.formResult',['result'=>$err]);
    }

    /**
     * delete content
     *
     * @param $systemId
     * @param $index
     * @return mixed
     */
    public function deleteChild($systemId , $index){
        $err = new Error();
        $sys = SystemSetting::where(IekModel::ID,$systemId)->first();
        if(is_null($sys)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('invalid systemId');
            return view('message.formResult',['result'=>$err]);
        }
        $content = json_decode($sys->content);
        if(!isset($content[$index])){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('invalid index');
            return view('message.formResult',['result'=>$err]);
        }
        $content[$index]->is_removed = true;
        $result = SystemSetting::where(IekModel::ID,$systemId)
            ->update([
                IekModel::CONTENT=>json_encode($content)
            ]);
        if(!$result){
            $err->setError(Errors::UNKNOWN_ERROR);
        }
        return view('message.formResult',['result'=>$err]);
    }

    /**
     * cover content
     *
     * @param $systemId
     * @param $index
     * @return mixed
     */
    public function coverChild($systemId , $index){
        $err = new Error();
        $sys = SystemSetting::where(IekModel::ID,$systemId)->first();
        if(is_null($sys)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('invalid systemId');
            return view('message.formResult',['result'=>$err]);
        }
        $content = json_decode($sys->content);
        if(!isset($content[$index])){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('invalid index');
            return view('message.formResult',['result'=>$err]);
        }
        $content[$index] -> is_removed = false;
        $result = SystemSetting::where(IekModel::ID,$systemId)
            ->update([
                IekModel::CONTENT=>json_encode($content)
            ]);
        if(!$result){
            $err->setError(Errors::UNKNOWN_ERROR);
        }
        return view('message.formResult',['result'=>$err]);
    }

    /**
     * set default content
     *
     * @param $systemId
     * @param $index
     * @return mixed
     */
    public function setDefaultChild($systemId,$index){
        $err = new Error();
        $sys = SystemSetting::where(IekModel::ID,$systemId)->first();
        if(is_null($sys)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('invalid systemId');
            return view('message.formResult',['result'=>$err]);
        }
        $content = json_decode($sys->content);
        if(!isset($content[0]->is_default)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('invalid systemId , content not have default');
            return view('message.formResult',['result'=>$err]);
        }
        if(!isset($content[$index])){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('invalid index');
            return view('message.formResult',['result'=>$err]);
        }
        foreach ($content as $key=>$item){
            if($key == $index){
                $item -> is_default = true;
            }else{
                $item -> is_default = false;
            }
        }
        $result = SystemSetting::where(IekModel::ID,$systemId)
            ->update([
                IekModel::CONTENT => json_encode($content)
            ]);
        if(!$result){
            $err->setError(Errors::UNKNOWN_ERROR);
        }
        return view('message.formResult',['result'=>$err]);
    }

    /**
     * make an array to object
     *
     * @param array $arr
     * @return \stdClass
     */

    public function arrayToObject($arr = []){
        $obj = new \stdClass();
        foreach ($arr as $key => $val){
            $obj -> {$key} = $val;
        }
        return $obj;
    }

}
