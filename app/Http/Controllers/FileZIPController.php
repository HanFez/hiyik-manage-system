<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 12/22/17
 * Time: 4:46 PM
 */

namespace App\Http\Controllers;


use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Constants\RealProductStatus;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ProductTraceability\RealProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class FileZIPController extends Controller
{
    use TraitFileZip;
    public function downloadProductsCheck(Request $request){
        $realProductIds = $request->input('realProductIds');
        $realProductIds = json_decode($realProductIds);
        return response()->json($this->downloadProductsMake($realProductIds));
    }


}