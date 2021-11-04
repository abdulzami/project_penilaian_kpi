<?php

namespace App\Providers;

use App\Models\Jabatan;
use App\Models\Jadwal;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Blade::if('dinilai', function () {
            $user = Auth::user();

            $jabatan = Jabatan::where('id_jabatan', $user->id_jabatan)->first();
            if ($jabatan->id_penilai) {
                $ada_penilai = "ya";
            } else {
                $ada_penilai = "tidak";
            }
            return $ada_penilai == "ya";
        });

        Blade::if('penilai', function () {
            $user = Auth::user();
            $penilai = Jabatan::where('id_penilai', $user->id_jabatan)->get();
            if ($penilai->isEmpty()) {
                $ada_penilai = "tidak";
            } else {
                $ada_penilai = "ya";
            }
            return $ada_penilai == "ya";
        });

        Blade::if('penilaian_berlangsung', function () {
            $sekarang = Carbon::now();
            $sekarang = $sekarang->toDateString();
            $jadwal = Jadwal::whereRaw('? between tanggal_mulai and tanggal_akhir', $sekarang)->get();
            
            if($jadwal->isEmpty()){
                $berlangsung ="tidak";
            }else{
                $berlangsung = "ya";
            }
            return $berlangsung == "ya";
        });

        Blade::if('atasanpenilai', function () {
            $user = Auth::user();

            $a = array();

            $dinilais = Jabatan::where('id_penilai', $user->id_jabatan)->get();
            foreach ($dinilais as $dinilai) {
                $haha = Jabatan::where('id_penilai', $dinilai->id_jabatan)->get();
                if ($haha->isEmpty()) {
                    array_push($a, "tidak");
                } else {
                    array_push($a, "ya");
                }
            }

            if (in_array("ya", $a)) {
                $atasan_penilai = "ya";
            } else {
                $atasan_penilai = "tidak";
            }
            return $atasan_penilai=="ya";
        });
    }
}
