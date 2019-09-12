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
use App\IekModel\Version1_0\Product\Facade;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class FacadeController extends Controller
{
/**
     * Facade's add page
     */
    public function addFacade(){
        return view('product.produce.facade');
    }
    /**
     * Facade's add data deal
     */
    public function createFacade(){
        //
    }
    /**
     * Facade's all data list
     */
    public function listFacade(){
        $model = new Facade();
        $type = 'facade';
        $getList = new IndexController();
        $result = $getList->tableList($model,$type);
        return $result;
    }
    /**
     * Facade's data detail
     */
    public function showFacade(){
        //
    }
    /**
     * Facade's edit page
     */
    public function editFacade($id){
        $action = 'edit';
        $facade = Facade::where(IekModel::CONDITION)->find($id);
        return view('product.produce.facade',['action'=>$action,'facade'=>$facade]);
    }
    /**
     * Facade's edit data deal
     */
    public function updateFacade(){
        //
    }
    /**
     * delete Facade's record
     */
    public function delFacade(){
        $model = new Facade();
        $getList = new IndexController();
        $result = $getList->tableDelete($model);
        return $result;
    }
    /**
     * recover Facade's record
     */
    public function coverFacade(){
        $model = new Facade();
        $getList = new IndexController();
        $result = $getList->tableRecover($model);
        return $result;
    }
    /**
     * 上传图片
     */
    public function uploadFacade(){
        $err = new Error();
        $file = request()->file('facade');
        if(request()->hasFile("facade")) {
            if($file->isValid()){
                $folder    = 'files/facades/';
                $extension = $file->getClientOriginalExtension();
                $md5       = hash('md5',File::get($file));//给文件一个校验码
                $width     = getimagesize($file->getRealPath())[0];
                $height    = getimagesize($file->getRealPath())[1];
                $length    = $file->getClientSize();
                $file_name = $file->getClientOriginalName();
                $uri       = $folder.$md5.'.'.$extension;
                $params    = new Facade();
                $params -> extension = $extension;
                $params -> md5       = $md5;
                $params -> width     = $width;
                $params -> height    = $height;
                $params -> length    = $length;
                $params -> file_name = $file_name;
                $params -> uri       = $uri;
                if(!Storage::disk('ftp')->exists($uri)){
                    Storage::disk('ftp')->put($uri, File::get($file));
                    $re = $params->save();
                    if($re){
                        //$err->setData($params);
                        $err->setError(Errors::OK);
                        $err->setMessage('上传成功');
                        return response()->json($err);
                    }else{
                        //$err->setData($params);
                        $err->setError(Errors::FAILED);
                        $err->setMessage('上传失败');
                        return response()->json($err);
                    }
                }else{
                    $id = Facade::where(IekModel::URI, $uri)
                        ->where(IekModel::CONDITION)
                        ->pluck(IekModel::ID)
                        ->first();
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
                        //$params -> id = $id;
                        $err -> setError(Errors::EXIST);
                        $err -> setMessage('图片已存在');
                    }
                    //$err -> setData($params);
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