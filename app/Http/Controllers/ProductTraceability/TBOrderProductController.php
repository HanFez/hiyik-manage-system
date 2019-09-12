<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 3/7/18
 * Time: 9:11 AM
 */

namespace app\Http\Controllers\ProductTraceability;


use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Constants\ImportExcel;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\ProductTraceability\Product;
use App\IekModel\Version1_0\ProductTraceability\TBOrder;
use App\IekModel\Version1_0\ProductTraceability\TBOrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class TBOrderProductController extends Controller
{


}