<?php

namespace App\Http\Middleware;

use App\Models\Jadwal;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;

class Cek_berlangsung
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
        $sekarang = Carbon::now();
        $sekarang = $sekarang->toDateString();
        $jadwal = Jadwal::whereRaw('? between tanggal_mulai and tanggal_akhir', $sekarang)->get();
        
        if($jadwal->isEmpty()){
            $berlangsung ="tidak";
        }else{
            $berlangsung = "ya";
        }

        if ($berlangsung == $role) {
            return $next($request);
        }else {
            return back()->with('gagal', 'Saat ini belum mengadakan penialaian');;
        }
        

        return $next($request);
    }
}
