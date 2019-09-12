<?php

namespace App\Http\Controllers\Table;

use App\IekModel\Version1_0\Constants\Errors;
use App\IekModel\Version1_0\Error;
use App\IekModel\Version1_0\IekModel;
use App\IekModel\Version1_0\Reason;
use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\IekModel\Version1_0\Folder;

class FolderController extends Controller
{
    /**
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
     * get folder list.
     */
    public function getFolderList(){
        $err = new Error();
        $take = request() -> input('take');
        $skip = request() -> input('skip');
        $isDeleted = request() -> input('isDeleted');
        $folders = Folder::with(['folderTitle.title.description'=>function($query){
                $query->where(IekModel::REMOVED,false);
            }])
            ->with(['folderDescription.description.description'=>function($query){
                $query->where(IekModel::REMOVED,false);
            }])
            ->with(['personFolder.person.personNick'=>function($query){
                $query->where(IekModel::CONDITION)
                    ->with('nick');
            }]);
        if($isDeleted !== 'true'){
            $folders = $folders->where(Folder::ACTIVE, true);
        }else{
            $folders = $folders->where(Folder::ACTIVE, false);
        }
        $folders = $folders ->get();
        $err -> total = $folders->count();
        if($take != null && $skip != null){
            $folders = $folders->slice($skip,$take);
        }
        $err->setData($folders);
        $reason = Reason::where(Reason::TYPE,'unForbidden')
            ->orWhere(Reason::TYPE,'forbidden')
            ->where(Reason::CONDITION)
            ->get();
        $err->reasons = $reason;
        $err -> skip = $skip;
        $err -> take = $take;
        $err -> isDeleted = $isDeleted;

        return view('admin.folder.folderList', ['result'=>$err]);
    }
}
