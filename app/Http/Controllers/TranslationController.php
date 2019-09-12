<?php
/**
 * Created by PhpStorm.
 * User: xj
 * Date: 11/10/16
 * Time: 9:58 AM
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TranslationController extends Controller
{
    use TraitRequestParams;

    public function translation(Request $request)
    {
        $param = $this -> getTranslationParam($request);
        if(is_null($param)) {
            return null;
        } else {
            $response = 'var trans_'.$param.' = ';
            $trans = trans($param);
            if(is_array($trans)) {
                return response($response.json_encode($trans));
            } else {
                return response($response.'{}');
            }
        }
    }

    public function getTranslationParam(Request $request)
    {
        $trans = $this -> getRequestParam($request, 'trans');
        return $trans;
    }

}
