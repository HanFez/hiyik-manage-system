<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2018/11/27
 * Time: 15:18
 */
namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Product\Texture;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class TextureController extends Controller
{
/**
     * Texture's add page
     */
    public function addTexture(){
        return view('product.produce.texture');
    }
    /**
     * Texture's add data deal
     */
    public function createTexture(){
        $err = new Error();
        $req = request()->all();
        //处理图片信息
        $files = $req['files'];
        $make_file = $this->makeFiles($files);
        $exist = Texture::where(IekModel::URI,$make_file['uri'])->first();
        if($exist){
            $err->setError(Errors::EXIST);
            $err->setMessage("该纹理已存在");
            return response()->json($err);
        }
        try{
            DB::beginTransaction();
            $texture = new Texture();
            $texture->file_name = $make_file['file_name'];
            $texture->extension = $make_file['extension'];
            $texture->md5 = $make_file['md5'];
            $texture->length = $make_file['length'];
            $texture->width = $make_file['width'];
            $texture->height = $make_file['height'];
            $texture->uri = $make_file['uri'];
            $texture->phy_width = $req['width'];
            $texture->phy_height = $req['height'];
            $texture->save();
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        if($texture){
            $err->setError(Errors::OK);
            $err->setMessage('保存成功');
        }else{
            $err->setError(Errors::FAILED);
            $err->setMessage('保存失败');
        }
        return response()->json($err);
        //return $this->curd(Errors::OK,Errors::FAILED,'保存成功','保存失败',$texture);
    }
    /**
     * Texture's all data list
     */
    public function listTexture(){
        $model = new Texture();
        $type = 'texture1';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * Texture's data detail
     */
    public function showTexture(){
        //
    }
    /**
     * Texture's edit page
     */
    public function editTexture($id){
        $action = 'edit';
        $texture = Texture::where(IekModel::CONDITION)->find($id);
        return view('product.produce.texture',['action'=>$action,'texture'=>$texture]);
    }
    /**
     * Texture's edit data deal
     */
    public function updateTexture($id){
        $err = new Error();
        $req_u = request()->all();
        try{
            DB::beginTransaction();
            $texture = Texture::where(IekModel::ID,$id)
                ->update([
                    'file_name' => $req_u['fileName'],
                    'phy_width' => $req_u['width'],
                    'phy_height' => $req_u['height']
                ]);
            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
            return view('message.formResult',['result'=>$err]);
        }
        if($texture){
            $err->setError(Errors::OK);
            $err->setMessage('修改成功');
        }else{
            $err->setError(Errors::FAILED);
            $err->setMessage('修改失败');
        }
        return response()->json($err);
        //return $this->curd(Errors::OK,Errors::FAILED,'保存成功','保存失败',$texture);
    }
    /**
     * delete Texture's record
     */
    public function delTexture(){
        $model = new Texture();
        $getList = new IndexController();
        $result = $getList->tableDelete($model);
        return $result;
    }
    /**
     * recover Texture's record
     */
    public function coverTexture(){
        $model = new Texture();
        $getList = new IndexController();
        $result = $getList->tableRecover($model);
        return $result;
    }
    /**
     * 验证图片信息
     * 重组图片格式
     */
    public function makeFiles($files){
        $folder = 'files/textures/';
        $arr['extension'] = $files->getClientOriginalExtension();
        $arr['md5'] = hash('md5',File::get($files));//给文件一个校验码
        $arr['width'] = getimagesize($files->getRealPath())[0];
        $arr['height'] = getimagesize($files->getRealPath())[1];
        $arr['length'] = $files->getClientSize();
        $arr['file_name'] = $files->getClientOriginalName();
        $arr['uri']  = $folder.$arr['md5'].'.'.$arr['extension'];
        return $arr;
    }

    /**
     * 上传图片
     */
    public function uploadTexture(){
        $err = new Error();
        $file = request()->file('file');
        if(request()->hasFile("file")) {
            if($file->isValid()){
                $uri = $this->makeFiles($file);
                if(!Storage::disk('ftp')->exists($uri['uri'])){
                    $ste = Storage::disk('ftp')->put($uri['uri'], File::get($file));
                    if($ste){
                        $err->setError(Errors::OK);
                        $err->setMessage('上传成功');
                        return response()->json($err);
                    }else{
                        $err->setError(Errors::FAILED);
                        $err->setMessage('上传失败');
                        return response()->json($err);
                    }
                }else{

                    $err -> setError(Errors::EXIST);
                    $err -> setMessage('图片已存在');
                    return response()->json($err);
                }
            }else{
                $err->setError(Errors::UNKNOWN_ERROR);
                return response()->json($err);
            }
        }else{
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('文件不存在');
            return response()->json($err);
        }
    }
}
?>