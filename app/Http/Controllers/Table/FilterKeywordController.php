<?php

namespace App\Http\Controllers\Table;

use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\FilterKeyword;

class FilterKeywordController extends Controller
{
    public static function addFilterWords(Request $request){
        $err = new Error();
        $filters = $request->input('filters');
        if(is_null($filters) || empty($filters)){
            $err->setError(Errors::INVALID_PARAMS);
            $err->setMessage('filters can not null');
            return $err;
        }
        foreach ($filters as $filter){
            FilterKeyword::firstOrCreate(['word'=>$filter,'replace_with'=>'*']);
        }
        return $err;
    }
}
