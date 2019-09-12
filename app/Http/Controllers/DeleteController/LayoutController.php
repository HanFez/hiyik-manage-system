<?php

namespace App\Http\Controllers\Table;

use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Layout;
use App\IekModel\Version1_0\LayoutPoint;
use App\IekModel\Version1_0\SystemImage;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class LayoutController extends Controller
{
    /**
     * 查看布局
     */
    public function layoutList(){
        $model = new Layout();
        $type = 'layout';
        $getList = new IndexController();
        $result = $getList->tableList($model , $type);
        return $result;
    }
    /**
     * 添加布局页
     */
    public function layoutAdd(){
        return view('admin.systemSetting.product.layout');
    }
    /**
     * 添加布局
     */
    public function createLayout(){
        $err = new Error();
        if($this->checkForm()){
            return $this->checkForm();
        }

        DB::beginTransaction();
        try{
            $layout = $this->createParam();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'添加成功','添加失败',$layout);
    }
    /**
     * 保存数据
     */
    public function createParam(){
        $err = new Error();
        $input = request()->except('_token');
        foreach($input as $data){
            $layout = new Layout();
            $layout->name = $data['name'];
            $layout->amount = $data['amount'];
            $layout->gap = $data['gap'];
            $layout->description = $data['description'];
            $layout->image_id = $data['imageId'];
            $layout->save();
            if(!is_null($data['point'])){
                foreach($data['point'] as $point){
                    $layoutPoint = new LayoutPoint();
                    $layoutPoint-> x = $point['x'];
                    $layoutPoint-> y = $point['y'];
                    $layoutPoint->layout_id = $layout->id;
                    $layoutPoint->save();
                    $err->layoutPoint = $layoutPoint;
                }
            }
            $err->layout = $layout;
        }
        return $err;
    }
    /**
     * 验证表单
     */
    public function checkForm(){
        $input = request()->except('_token');
        if(is_null($input['data']['name']) || empty($input['data']['name'])){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请输入布局名称','name');
        }
        if(is_null($input['data']['imageId']) || empty($input['data']['imageId'])){
            return $this->viewReturn(Errors::INVALID_PARAMS,'请上传布局图片','imageId');
        }
    }

    /**
     * 修改布局页
     */
    public function layoutEdit($id){
        $layout = Layout::with('systemImage.norms')
            ->with('point')
            ->find($id);
        return view('admin.systemSetting.product.layout',compact('layout'));
    }
    /**
     * 修改布局
     */
    public function modifyLayout($id){
        $err = new Error();
        DB::beginTransaction();
        try{
            Layout::where(IekModel::ID,$id)
                ->update([
                    IekModel::REMOVED => true
                ]);
            $layout = $this->createParam();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        return $this->curd(Errors::OK,Errors::FAILED,'修改成功','修改失败',$layout);
    }
    /**
     * 删除布局
     */
    public function deleteLayout(){
        $model = new Layout();
        $del = new IndexController();
        $result = $del->tableDelete($model);
        return $result;
    }
    /**
     * 恢复布局
     */
    public function recoverLayout(){
        $model = new Layout();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }
    /**
     * 上传布局图片
     */
    public function uploadLayout(){
        $err = new Error();
        $file = request()->file('file');
        if(request()->hasFile('file')){
            if($file->isValid()){
                $folder    = 'files/systems/';
                $extension = $file->getClientOriginalExtension();
                $md5       = hash('md5',File::get($file));
                $width     = getimagesize($file->getRealPath())[0];
                $height    = getimagesize($file->getRealPath())[1];
                $length    = $file->getClientSize();
                $file_name = $file->getClientOriginalName();
                $uri       = $folder.$md5.'.'.$extension;
                $params    = new SystemImage();
                $params->extension = $extension;
                $params->md5       = $md5;
                $params->width     = $width;
                $params->height    = $height;
                $params->length    = $length;
                $params->file_name = $file_name;
                $params->uri       = $uri;
                if(!Storage::disk('ftp')->exists($uri)){
                    Storage::disk('ftp')->put($uri, File::get($file));
                    $re = $params->save();
                    if($re){
                        $err->setData($params);
                        $err->setError(Errors::OK);
                        $err->setMessage('上传成功');
                        return view('message.formResult',['result'=>$err]);
                    }else{
                        $err->setData($params);
                        $err->setError(Errors::FAILED);
                        $err->setMessage('上传失败');
                        return view('message.formResult',['result'=>$err]);
                    }
                }else{
                    $id = SystemImage::where(IekModel::URI, $uri)->pluck(IekModel::ID)->first();
                    if(is_null($id)) {
                        $re = $params->save();
                        if($re){
                            $err->setError(Errors::OK);
                            $err->setMessage('上传成功');
                        }else{
                            $err->setError(Errors::FAILED);
                            $err->setMessage('上传失败');
                        }
                    } else {
                        $params->id = $id;
                        $err->setError(Errors::EXIST);
                        $err->setMessage('图片已存在');
                    }
                    $err->setData($params);
                    return view('message.formResult',['result'=>$err]);
                }
            }else{
                $err->setError(Errors::UNKNOWN_ERROR);
                return view('message.formResult',['result'=>$err]);
            }
        }else{
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('文件不存在');
            return view('message.formResult',['result'=>$err]);
        }
    }
    /**
     * 获取图片
     * @return mixed
     */
    public function layoutImage($id){
        $folder = 'files/systems/';
        $exists = Storage::disk('ftp')->exists($folder.$id);
        if($exists){
            $img = Storage::disk('ftp')->get($folder.$id);
            return response($img)->header('Content-Type', 'image/jpeg');
        }
    }
}
