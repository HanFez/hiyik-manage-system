<?php

namespace App\Http\Middleware;

use App\IekModel\Version1_0\Manager;
use Closure;

class LoginStatus
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $name = session('login.name');
        $id = session('login.id');
        if(is_null($name) && is_null($id)){
            $pgsql = Manager::checkManager($id);
            if(!$pgsql){
                return response()->view('message.redirect', ['url' => 'login.html']);
            }
            return response()->view('message.redirect', ['url' => 'login.html']);
        }
        return $next($request);
    }
}
