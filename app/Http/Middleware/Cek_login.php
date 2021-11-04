<?php

namespace App\Http\Middleware;

use App\Models\Penilai;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Cek_login
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
     
        if ($user->level == $role) {
            return $next($request);
        } 
        else {
            return redirect('/')->with('error', 'Anda tidak punya akses !');
        }
    }
}
