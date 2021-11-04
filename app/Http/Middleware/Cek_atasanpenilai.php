<?php

namespace App\Http\Middleware;

use App\Models\Jabatan;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Cek_atasanpenilai
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

        $a = array();

        $dinilais = Jabatan::where('id_penilai',$user->id_jabatan)->get();
        foreach($dinilais as $dinilai){
            $haha = Jabatan::where('id_penilai',$dinilai->id_jabatan)->get();
            if($haha->isEmpty()){
                array_push($a,"tidak");
            }else{
                array_push($a,"ya");
            }
        }

        if(in_array("ya",$a)){
            $atasan_penilai = "ya";
        }else{
            $atasan_penilai = "tidak";
        }
        if ($atasan_penilai == $role) {
            return $next($request);
        }else {
            return redirect('/')->with('error', 'Anda tidak punya akses !');
        }
    }
}
