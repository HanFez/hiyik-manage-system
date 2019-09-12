<?php

namespace App\Http\Controllers\Table;

use App\Http\Controllers\Operation\DeleteController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Tag;
use stdClass;
use Illuminate\Support\Facades\DB;

class TagController extends Controller
{
    /**
     * get official tag list
     */
    public function tagList(){
        $err = new Error();
        $tags = Tag::where(IekModel::OFFICIAL,true)
            ->orderBy(IekModel::CREATED,'desc')
            ->get();
        $err->setData($tags);
        return view('admin.systemSetting.systemTags',['result'=>$err]);
    }

    /**
     * add tag parent and child
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function createTag(Request $request){
        $err = new Error();
        $check = $this->checkAdd();
        if($check){
            return $check;
        }
        $pid = request()->input('parent_id');
        if(is_null($pid)){
            $params = [];
            $params[IekModel::NAME] = $request->input('name');
            $params[IekModel::DESC]= $request->input('description');
            $params[IekModel::OFFICIAL] = true;
            $params[IekModel::LEVEL] = $request->input('level');
            $params[IekModel::HITS] = $request->input('hits');
        }else{
            $checkpid = Tag::where(IekModel::ID,$pid)->count();
            if(!$checkpid){
                return $this->viewReturn(Errors::INVALID_PARAMS,'无效的父ID','parent_id');
            }
            $params = [];
            $params[IekModel::NAME] = $request->input('name');
            $params[IekModel::DESC]= $request->input('description');
            $params[IekModel::OFFICIAL] = true;
            $params[IekModel::LEVEL] = $request->input('level');
            $params[IekModel::HITS] = $request->input('hits');
            $params[IekModel::PARENT_ID] = $request->input('parent_id');
        }
        DB::beginTransaction();
        try{
            $result = Tag::insert($params);
            if($request->input('level') == 1){
                $result = Tag::where(IekModel::ID,$pid)
                    ->where(IekModel::LEVEL,1)
                    ->update([
                        IekModel::PARENT_ID => $pid
                    ]);
            }
            DB::commit();
//            中间逻辑代码 DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
//            接收异常处理并回滚 DB::rollBack();
        }
        return $this->curd(Errors::OK,Errors::FAILED,'成功','失败',$result);
    }

    /**
     * check param of add
     * @return \Illuminate\View\View
     */
    public function checkAdd(){
        $name = request()->input('name');
        $des = request()->input('description');
        $level = request()->input('level');
        $hot = request()->input('hits');
        if(is_null($name)){
            return $this->viewReturn(Errors::NOT_EMPTY,'名字不能为空','name');
        }else{
            $nck = Tag::where(IekModel::NAME,$name)->count();
            if($nck){
                return $this->viewReturn(Errors::EXIST,'该名字已存在','name');
            }
        }
        if(is_null($des)){
            return $this->viewReturn(Errors::NOT_EMPTY,'描述不能为空','description');
        }
        if(is_null($level)){
            return $this->viewReturn(Errors::NOT_EMPTY,'等级不能为空','level');
        }
        if(is_null($hot)){
            return $this->viewReturn(Errors::NOT_EMPTY,'热度不能为空','hits');
        }
    }
    /**
     * modify tag parent and child
     *
     * @param Request $request
     * @return mixed
     */
    public function modifyTag(Request $request){
        $err = new Error();
        $checkEdit = $this->checkEdit();
        if($checkEdit){
            return $checkEdit;
        }
        $tagId = $request->input('id');
        $name = $request->input('name');
        $description = $request->input('description');
        $parentId = $request->input('parent_id');
        $level = $request->input('level');
        $hits = $request->input('hits');
        DB::beginTransaction();
        try{
            $result = Tag::where(IekModel::ID,$tagId)
                ->update([
                    IekModel::NAME => $name,
                    IekModel::DESC => $description,
                    IekModel::PARENT_ID => $parentId,
                    IekModel::LEVEL => $level,
                    IekModel::HITS => $hits
                ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'成功','失败',$result);
    }
    /**
     * check param of add
     * @return \Illuminate\View\View
     */
    public function checkEdit(){
        $id = request()->input('id');
        $name = request()->input('name');
        $des = request()->input('description');
        $level = request()->input('level');
        $hot = request()->input('hits');
        $parentId = request()->input('parent_id');
        $idck = Tag::where(IekModel::ID,$id)->count();
        if(!$idck){
            return $this->viewReturn(Errors::INVALID_PARAMS,'无效的ID','id');
        }
        if(is_null($name)){
            return $this->viewReturn(Errors::NOT_EMPTY,'名字不能为空','name');
        }else{
            $nck = Tag::checkNameRepeat($name,$id);
            if(!$nck){
                return $this->viewReturn(Errors::EXIST,'该名字已存在','name');
            }
        }
        if(is_null($des)){
            return $this->viewReturn(Errors::NOT_EMPTY,'描述不能为空','description');
        }
        if(is_null($level)){
            return $this->viewReturn(Errors::NOT_EMPTY,'等级不能为空','level');
        }
        if(is_null($hot)){
            return $this->viewReturn(Errors::NOT_EMPTY,'热度不能为空','hits');
        }
        if(!is_null($parentId)){
            $pidck = Tag::where(IekModel::PARENT_ID,$parentId)->count();
            if(!$pidck){
                return $this->viewReturn(Errors::INVALID_PARAMS,'无效的父ID','parent_id');
            }
        }
    }
    /**
     * delete tags
     *
     * @param Request $request
     * @return mixed
     */
    public function deleteTag(Request $request){
        $err = new Error();
        $id = $request->input('id');
        DB::beginTransaction();
        try{
            $re = Tag::where(IekModel::ID,$id)
                ->update([
                    IekModel::REMOVED => true
                ]);
            $err->setData($re);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
        }
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
     * cover tags
     *
     * @param Request $request
     * @return mixed
     */
    public function coverTag(Request $request){
        $err = new Error();
        $id = $request->input('id');
        DB::beginTransaction();
        try{
            $re = Tag::where(IekModel::ID,$id)
                ->update([
                    IekModel::REMOVED => false
                ]);
            DB::commit();
            $err->setData($re);
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
        }
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
     * get tags by given type
     *
     * @param Request $request
     * @param $type
     * @return mixed
     */
    public function getTags(Request $request , $type = 'all'){
        $err = new Error();
        $is_active = $request->input('$isActive');
        if($is_active != 'true'){
            $is_active = false;
        }
        if($type != 'all'){
            $parentId = Tag::where(IekModel::NAME,$type)
                ->where(IekModel::CONDITION)
                ->first();
            if(is_null($parentId)){
                $err->setError(Errors::INVALID_PARAMS);
                $err->setMessage('invalid type');
                return view('message.formResult',['result'=>$err]);
            }
            $Tags = Tag::where(IekModel::PARENT_ID,$parentId->id);
            if($is_active){
                $Tags = $Tags->where(IekModel::ACTIVE,true);
            }
            $Tags = $Tags->where(IekModel::OFFICIAL,true)->get();
            $tagContent = new stdClass();
            $tagContent->parent = $parentId;
            $tagContent->tags = $Tags;
            $err->setData($tagContent);
        }else{
            if($is_active){
                $Tags = Tag::where(IekModel::ACTIVE,true)
                    ->where(IekModel::OFFICIAL,true)
                    ->get();
            }else{
                $Tags = Tag::where(IekModel::OFFICIAL,true)->get();
            }
            $err->setData($Tags);
        }
        return view('admin.systemSetting.systemTags',['result'=>$err]);
    }

    /**
     * get tag by id
     *
     * @param $id
     * @return mixed
     */
    public function getTagById($id){
        $err = new Error();
        $tag = Tag::where(IekModel::ID,$id)->first();
        $parent = Tag::where(IekModel::ID,$tag->id)->first();
        if($tag->isEmpty()){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('invalid tag id');
            return view('message.formResult',['result'=>$err]);
        }
        $tagContent = new stdClass();
        $tagContent -> parent = $parent;
        $tagContent -> tags = $tag;
        $err -> setData($tagContent);
        return response()->json($err);
    }
}
