<?php

namespace App\Http\Middleware;

use App\Models\Penilaian;
use Closure;
use Hashids\Hashids;
use Illuminate\Http\Request;

class Cek_pengajuan_banding
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $hash = new Hashids();
        $id_penilaian = $hash->decode($request->route('id'));
        $penilaian = Penilaian::select('penilaians.status_penilaian','bandings.status_banding')->leftJoin('bandings','bandings.id_penilaian','=','penilaians.id_penilaian')
        ->find($id_penilaian);
        if($penilaian[0]->status_penilaian == 'terverifikasi' && $penilaian[0]->status_banding == null)
        {
            return $next($request);
        }else{
            return back()->with('gagal', 'Tidak bisa');
        }
    }
}
