<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
class User{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next){
        // if(session('role') == null){
        //     return redirect('/');
        // }

        // if(session('role') == 'admin'){
        //     return redirect('admin.dashboard');
        // }
        return $next($request);
    }
}
