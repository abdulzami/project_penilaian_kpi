<?php

namespace App\Http\Middleware;

use App\Models\Penilaian;
use Closure;
use Hashids\Hashids;
use Illuminate\Http\Request;

class Cek_catatan_penting
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
        $penilaian = Penilaian::find($id_penilaian);
        if($penilaian[0]->catatan_penting != null)
        {
            return $next($request);
        }else{
            return back()->with('gagal', 'Tidak punya catatan penting');
        }
    }
}
