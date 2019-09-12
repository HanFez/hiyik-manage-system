<?php

namespace App\Http\Controllers\Operation;

use App\IekModel\Version1_0\IekModel;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\Constants\Errors;
use Illuminate\Support\Facades\DB;

class DeleteController extends Controller
{

    public static function DeleteAction($id = [],$model){
        if(count($id) == 0){
            return false;
        }
        DB::beginTransaction();
        try{
            $result = $model::whereIn(IekModel::ID,$id)
                ->update([
                    IekModel::REMOVED => true
                ]);
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            return false;
        }

        if($result != count($id)){
            DB::rollBack();
            return false;
        }
        return $result;
    }

    public static function coverAction($id = [],$model){
        if(count($id) == 0){
            return false;
        }
        DB::beginTransaction();
        try{
            $result = $model::whereIn(IekModel::ID,$id)
                ->update([
                    IekModel::REMOVED => false
                ]);
            DB::commit();
        }catch (\Exception $e) {
            DB::rollBack();
            return false;
        }

        if($result != count($id)){
            DB::rollBack();
            return false;
        }
        return $result;
    }

}