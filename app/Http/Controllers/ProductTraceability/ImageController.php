<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/12/17
 * Time: 5:33 PM
 */

namespace app\Http\Controllers\ProductTraceability;


use App\Http\Controllers\Controller;
use App\Http\Controllers\Table\ImageFileController;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\ProductTraceability\Image;
use App\IekModel\Version1_0\ProductTraceability\ImageNorm;
use App\IekModel\Version1_0\ProductTraceability\Norm;
use Illuminate\Http\Request;

class ImageController extends ImageFileController
{

    public static $ImageFolder = 'files/TBProducts';
    public static $NativeFolder = 'introduce';
    public static $NormFolder = 'TBProductNorms';
    public static $IID = 'image_id';
    public static $IsTone = false;
    public static $ImageNormModel = ImageNorm::class;
    public static $ImageModel = Image::class;
    public static $NormModel = Norm::class;

    public function handleTBProductImageFile(Request $request){
        $err = new Error();
        $err = $this->handleImageFileSave($request);
        return response()->json($err);
    }
}