<?php

namespace App\Http\Middleware;

use App\Models\Jabatan;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Cek_dinilai
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

       $jabatan = Jabatan::where('id_jabatan',$user->id_jabatan)->first();
        if($jabatan->id_penilai)
        {
            $ada_penilai = "ya";
        }else{
            $ada_penilai = "tidak";
        }
        if ($ada_penilai == $role) {
            return $next($request);
        }else {
            return redirect('/')->with('error', 'Anda tidak dinilai !');
        }
    }
}
