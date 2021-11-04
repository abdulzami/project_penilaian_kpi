<?php

namespace App\Http\Middleware;

use App\Models\Jabatan;
use App\Models\Penilai;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Cek_penilai
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

        $penilai = Jabatan::where('id_penilai',$user->id_jabatan)->get();
        if($penilai->isEmpty())
        {
            $ada_penilai = "tidak";
        }else
        {
            $ada_penilai = "ya";
        }
        if ($ada_penilai == $role) {
            return $next($request);
        }else {
            return redirect('/')->with('error', 'Anda tidak punya akses !');
        }
    }
}
