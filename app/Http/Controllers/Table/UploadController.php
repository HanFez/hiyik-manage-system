<?php
/**
 * Created by PhpStorm.
 * User: HanFei
 * Date: 2017/5/16
 * Time: 20:18
 */
namespace App\Http\Controllers\Table;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller{

    const PUBLICATION = 'Publications/';
    const AVATAR = 'Avatars';

    public function saveFile(Request $request) {
        $md5 = null;$request->input('md5');
        $fileName = $request->file('fileName')->getClientOriginalName();


        if($request->hasFile('fileName')) {
            if($request->file('fileName')->isValid()) {
                $realPath = $request->file('fileName')->getRealPath();
                $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                $fileContents = file_get_contents($realPath);
                if($md5 == null) {
                    $md5 = hash('md5', $fileContents);
                }
                Storage::put(self::PUBLICATION.$md5.'.'.$fileExt, $fileContents);
                return response($fileContents, 200, ['Content-Type' => 'image/jpeg']);
            }

        }
    }
}