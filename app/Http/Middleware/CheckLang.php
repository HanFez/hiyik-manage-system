<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Closure;

class CheckLang
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next) {
        if($request->hasHeader('accept-language')) {
            $lang = $request->header('accept-language');
            App::setLocale($this->getLocale($lang));
        }
        $resp = $next($request);
        return $resp;
    }

    public function terminate($request, $response) {
    }

    public function getLocale($header) {
        $reg = explode(';', $header);
        $lang = explode(',', $reg[0])[1];
        return $lang;
    }
}
