<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Penilai;
use App\Models\Penilaian;
use App\Models\PenilaianPerformance;
use App\Models\PenilaianPerilaku;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {

        if (!Auth::check()) {
            return redirect('/');
        }

        $hash = new Hashids();
        $sekarang = Carbon::now();
        $sekarang = $sekarang->toDateString();
        $jadwal = Jadwal::whereRaw('? between tanggal_mulai and tanggal_akhir', $sekarang)->get();
        if ($jadwal->isEmpty()) {
            $nama_periode = "";
        } else {
            $nama_periode = $jadwal[0]->nama_periode;
        }
        // $id = Auth::user()->id_user;

        if (Auth::user()->level == 'admin') {
            return view('admin.home', compact('nama_periode'));
        } elseif (Auth::user()->level == 'pegawai') {
            if (!$jadwal->isEmpty()) {
                $penilaian = Penilaian::select('penilaians.id_penilaian','penilaians.id_pegawai','penilaians.id_jadwal'
                ,'penilaians.id_pegawai','penilaians.id_penilai','penilaians.status_penilaian','penilaians.catatan_penting'
                ,'penilaians.pengurangan','bandings.id_banding','bandings.status_banding')
                ->where('id_pegawai', Auth::user()->id_user)->where('id_jadwal', $jadwal[0]->id_jadwal)
                ->leftJoin('bandings','bandings.id_penilaian','=','penilaians.id_penilaian')
                ->get();
                for ($i = 0; $i < sizeof($penilaian); $i++) {
                    $performances = PenilaianPerformance::select(DB::raw("CASE
                        WHEN tipe_performance = 'min' AND target>realisasi THEN 100
                        WHEN tipe_performance = 'min' THEN ((target/realisasi)*100)*bobot/100
                        WHEN tipe_performance = 'max' THEN ((realisasi/target) * 100)*bobot/100
                        END AS skor"))
                        ->join('kpi_performances', 'kpi_performances.id_performance', '=', 'penilaian_performances.id_performance')
                        ->where('id_penilaian', $penilaian[$i]->id_penilaian)->get();
                    $performances = $performances->sum('skor');
                    $performances = $performances * 70 / 100;
                    $perilakus = PenilaianPerilaku::select(DB::raw('SUM(nilai_perilaku *20 * 100/6/100)*30/100 AS skor_akhir'))->where('id_penilaian', $penilaian[$i]->id_penilaian)->get();
                    $perilakus = (float)$perilakus[0]->skor_akhir;
                    $total = round(($performances + $perilakus), 5);
                    $penilaian[$i]->performance = $performances;
                    $penilaian[$i]->perilaku = $perilakus;
                    $penilaian[$i]->total = $total;
                    $penilaian[$i]->capaian = $total . " %";
                }
                if ($penilaian->isEmpty()) {
                    return view('pegawai.home', compact('nama_periode'));
                } else {
                    $penilaian = $penilaian[0];
                    $kpiperformances = PenilaianPerformance::leftjoin('kpi_performances', 'kpi_performances.id_performance', '=', 'penilaian_performances.id_performance')->where('id_penilaian', $penilaian->id_penilaian)->get();
                    return view('pegawai.home', compact('nama_periode', 'penilaian', 'kpiperformances','hash'));
                }
            }else{
                return view('pegawai.home');
            }
        } else {
            return redirect('/')->with('error', 'Anda tidak punya akses !');
        }
    }
}
