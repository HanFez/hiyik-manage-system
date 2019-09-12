<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 5/3/17
 * Time: 3:15 PM
 */

namespace App\Http\Controllers\Table;


use App\Http\Controllers\Admin\PrivilegeController;
use App\Http\Controllers\Controller;
use App\Http\Controllers\IndexController;
use App\IekModel\Version1_0\Color;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ProductTemporary;
use App\IekModel\Version1_0\SystemImage;
use App\IekModel\Version1_0\Texture;
use App\IekModel\Version1_0\TextureImage;
use App\IekModel\Version1_0\TextureSegment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

class TextureController extends Controller
{
    public function lists(Request $request){
        $tableName = 'tblTextures';
        $start = $request->input('start');
        $getAll = new PrivilegeController();
        $res = $getAll->getAllList($request , $tableName);
        if(is_null($start)){
            $params = new \stdClass();
            $params-> type = 'texture';
            $params-> url = 'texture';
            $field = $res;
            return view('tableData.index',compact('field', 'params'));
        }
        return response()->json($res);
    }

    public function add(){
        $err = new Error();
        $color = Color::where(IekModel::CONDITION)
            ->where(IekModel::OFFICIAL,true)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        if($color->isEmpty()){
            $color = null;
        }
        $data = new \stdClass();
        $data -> color = $color;
        $err->setData($data);
        return view('admin.systemSetting.product.textureAdd',['result'=>$err]);
    }

    public function create(Request $request){
        $texture = self::createTexture($request);
        return view('message.formResult',['result'=>$texture]);
    }

    public function createTexture(Request $request){
        $err = new Error();
        $params = self::getCreateParams($request);
        if($params->statusCode != 0){
            return $params;
        }
        $params = $params->data;
        try{
            DB::beginTransaction();
            $texture = new Texture();
            $texture -> name = $params->name;
            $texture -> description = $params->description;
            $texture -> color_id = $params->colorId;
            $texture -> image_id = $params->imageId;
            $texture -> save();

            $segments = [];
            foreach ($params->segment as $segment){
                $textureSegment = [];
                $textureSegment['name'] = $segment->name;
                $textureSegment['image_id'] = $segment->imageId;
                $textureSegment['texture_id'] = $texture->{IekModel::ID};
                $segments[] = $textureSegment;
            }
            TextureSegment::insert($segments);
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
        }
        return $err;
    }

    public function getCreateParams(Request $request){
        $err = new Error();
        $name = $request->input('name');
        $segment = $request->input('segment');
        if(is_null($name) || is_null($segment)){
            $err->setError(Errors::INVALID_PARAMS);
            return $err;
        }
        $params = new \stdClass();
        $params -> description = $request->input('description');
        $params -> colorId = $request->input('colorId');
        $params -> imageId = $request->input('imageId');
        $params -> segment = json_decode(json_encode($segment));
        $params -> name = $name;
        $err->setData($params);
        return $err;
    }

    public function edit($id){
        $err = new Error();
        $texture = Texture::where(IekModel::ID,$id)
            ->with(['textureImages'=>function($q){
                $q->where(IekModel::CONDITION);
            }])
            ->with(['colors'=>function($q){
                $q->where(IekModel::CONDITION);
            }])
            ->with(['textureSegments.textureImage'=>function($q){
                $q->where(IekModel::CONDITION);
            }])
            ->first();
        if(is_null($texture)){
            $err->setError(Errors::INVALID_PARAMS);
            return view('admin.systemSetting.product.textureEdit',['result'=>$err]);
        }
        $color = Color::where(IekModel::CONDITION)
            ->where(IekModel::OFFICIAL,true)
            ->where(IekModel::IS_MODIFY,false)
            ->get();
        if($color->isEmpty()){
            $color = null;
        }
        $data = new \stdClass();
        $data->texture = $texture;
        $data->color = $color;
        $err->setData($data);
        return view('admin.systemSetting.product.textureEdit',['result'=>$err]);
    }

    public function modify(Request $request , $id){
        $err = new Error();
        try{
            DB::beginTransaction();
            Texture::where(IekModel::ID,$id)
                ->update([
                    IekModel::IS_MODIFY => true
                ]);
            $texture = self::createTexture($request);
            if($texture->statusCode != 0){
                throw new \Exception('rollback');
            }
            DB::commit();
        }catch (\Exception $e){
            DB::rollback();
            $err->setError(Errors::FAILED);
            $err->setMessage($e->getMessage());
        }
        return view('message.formResult',['result'=>$err]);
    }

    /**
     * 删除纹理
     */
    public function deleteTexture(){
        $err = new Error();
        $ids = request()->input('ids');
        if(is_null($ids)){
            $err->setError(Errors::INVALID_PARAMS);
            return response()->json($err);
        }
        DB::beginTransaction();
        try{
            $temporary = ProductTemporary::where(IekModel::CONDITION)->pluck(IekModel::DATA);
            foreach($ids as $id){
                $use_border = Texture::whereHas('demiBorder.borderPatternDemi.borderPattern.border.productBorder')
                    ->with('demiBorder.borderPatternDemi.borderPattern.border')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_border)){
                    $res = $this->limitBorder($use_border);
                    foreach($res as $re){
                        foreach($temporary as $tem){
                            $str = substr_count($tem,$re);
                            if($str>0){
                                $err->setError(Errors::INVALID_PARAMS);
                                $err->setMessage('有产品草稿正在使用这条数据，请不要删除');
                                return response()->json($err);
                            }
                        }
                    }
                }
                $use_back =  Texture::whereHas('demiBack.backPatternDemi.backPattern.back.productBack')
                    ->with('demiBack.backPatternDemi.backPattern.back')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_back)){
                    $res = $this->limitBack($use_back);
                    foreach($res as $re){
                        foreach($temporary as $tem){
                            $str = substr_count($tem,$re);
                            if($str>0){
                                $err->setError(Errors::INVALID_PARAMS);
                                $err->setMessage('有产品草稿正在使用这条数据，请不要删除');
                                return response()->json($err);
                            }
                        }
                    }
                }
                $use_frame = Texture::whereHas('demiFrame.framePatternDemi.framePattern.frame.productFrame')
                    ->with('demiFrame.framePatternDemi.framePattern.frame')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_frame)){
                    $res = $this->limitFrame($use_frame);
                    foreach($res as $re){
                        foreach($temporary as $tem){
                            $str = substr_count($tem,$re);
                            if($str>0){
                                $err->setError(Errors::INVALID_PARAMS);
                                $err->setMessage('有产品草稿正在使用这条数据，请不要删除');
                                return response()->json($err);
                            }
                        }
                    }
                }
                $use_front = Texture::whereHas('demiFront.frontPatternDemi.frontPattern.front.productFront')
                    ->with('demiFront.frontPatternDemi.frontPattern.front')
                    ->where(IekModel::CONDITION)
                    ->where(IekModel::IS_MODIFY,false)
                    ->find($id);
                if(!is_null($use_front)){
                    $res = $this->limitFront($use_front);
                    foreach($res as $re){
                        foreach($temporary as $tem){
                            $str = substr_count($tem,$re);
                            if($str>0){
                                $err->setError(Errors::INVALID_PARAMS);
                                $err->setMessage('有产品草稿正在使用这条数据，请不要删除');
                                return response()->json($err);
                            }
                        }
                    }
                }
                if(!is_null($use_border) || !is_null($use_back) || !is_null($use_frame) || !is_null($use_front)){
                    $err->setError(Errors::INVALID_PARAMS);
                    $err->setMessage('有产品正在使用这条数据，请不要删除');
                    return response()->json($err);
                }else{
                    $re = Texture::where(IekModel::ID,$id)
                        ->update([
                            IekModel::REMOVED => true
                        ]);
                    if($re){
                        $err->setError(Errors::OK);
                        $err->setMessage('删除成功');
                    }else{
                        $err->setError(Errors::FAILED);
                        $err->setMessage('删除失败');
                    }
                }
            }

            DB::commit();
        }catch (\Exception $e){
            DB::rollBack();
            $err->setError(Errors::UNKNOWN_ERROR);
            $err->setMessage($e->getMessage());
        }
        return response()->json($err);
    }
    public function limitBorder($use){
        if(!$use->demiBorder->isEmpty()){
            $borderId = [];
            foreach($use->demiBorder as $demi){
                if(!$demi->borderPatternDemi->isEmpty()){
                    foreach($demi->borderPatternDemi as $value){
                        if(!is_null($value->borderPattern)){
                            if(!is_null($value->borderPattern->border)) {
                                $borderId[] = $value->borderPattern->border->id;
                            }
                        }
                    }
                }
            }
        }
        return $borderId;
    }
    public function limitBack($use){
        if(!$use->demiBack->isEmpty()){
            $backId = [];
            foreach($use->demiBack as $demi){
                if(!$demi->backPatternDemi->isEmpty()){
                    foreach($demi->backPatternDemi as $value){
                        if(!is_null($value->backPattern)){
                            if(!is_null($value->backPattern->back)) {
                                $backId[] = $value->backPattern->back->id;
                            }
                        }
                    }
                }
            }
        }
        return $backId;
    }
    public function limitFrame($use){
        if(!$use->demiFrame->isEmpty()){
            $frameId = [];
            foreach($use->demiFrame as $demi){
                if(!$demi->framePatternDemi->isEmpty()){
                    foreach($demi->framePatternDemi as $value){
                        if(!is_null($value->framePattern)){
                            if(!is_null($value->framePattern->frame)) {
                                $frameId[] = $value->framePattern->frame->id;
                            }
                        }
                    }
                }
            }
        }
        return $frameId;
    }
    public function limitFront($use){
        if(!$use->demiFront->isEmpty()){
            $frontId = [];
            foreach($use->demiFront as $demi){
                if(!$demi->frontPatternDemi->isEmpty()){
                    foreach($demi->frontPatternDemi as $value){
                        if(!is_null($value->frontPattern)){
                            if(!is_null($value->frontPattern->front)) {
                                $frontId[] = $value->frontPattern->front->id;
                            }
                        }
                    }
                }
            }
        }
        return $frontId;
    }
    /**
     * 恢复纹理
     */
    public function recoverTexture(){
        $model = new Texture();
        $cover = new IndexController();
        $result = $cover->tableRecover($model);
        return $result;
    }

    /**
     * 上传纹理图片
     */
    public function uploadTextureImage(){
        $err = new Error();
        $file = request()->file('fileName');
        if(request()->hasFile("fileName")) {
            if($file->isValid()){
                $folder    = 'files/systems/';
                $extension = $file->getClientOriginalExtension();
                $md5       = hash('md5',File::get($file));//给文件一个校验码
                $width     = getimagesize($file->getRealPath())[0];
                $height    = getimagesize($file->getRealPath())[1];
                $length    = $file->getClientSize();
                $file_name = $file->getClientOriginalName();
                $uri       = $folder.$md5.'.'.$extension;
                $params    = new SystemImage();
                $params -> extension = $extension;
                $params -> md5       = $md5;
                $params -> width     = $width;
                $params -> height    = $height;
                $params -> length    = $length;
                $params -> file_name = $file_name;
                $params -> uri       = $uri;
                $params -> description = '纹理和纹理段上传图片';
                if(!Storage::disk('ftp')->exists($uri)){
                    Storage::disk('ftp')->put($uri, File::get($file));
                    $re = $params->save();
                    if($re){
                        $err->setData($params);
                        $err->setError(Errors::OK);
                        $err->setMessage('上传成功');
                        return response()->json($err);
                    }else{
                        $err->setData($params);
                        $err->setError(Errors::FAILED);
                        $err->setMessage('上传失败');
                        return response()->json($err);
                    }
                }else{
                    $id = TextureImage::where(IekModel::URI, $uri)
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
                        $params -> id = $id;
                        $err -> setError(Errors::EXIST);
                        $err -> setMessage('图片已存在');
                    }
                    $err -> setData($params);
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
    /**
     * 获取图片
     * @return mixed
     */
    public function textureImage($id){
        $folder = 'files/systems/';
        $exists = Storage::disk('ftp')->exists($folder.$id);
        if($exists){
            $img = Storage::disk('ftp')->get($folder.$id);
            return response($img)->header('Content-Type', 'image/jpeg');
        }
    }
}