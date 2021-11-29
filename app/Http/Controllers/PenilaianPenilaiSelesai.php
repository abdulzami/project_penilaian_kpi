<?php

namespace App\Http\Controllers;

use App\Models\HistoriPenilaianPerformance;
use App\Models\Jabatan;
use App\Models\Jadwal;
use App\Models\PenilaianPerformance;
use App\Models\PenilaianPerilaku;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenilaianPenilaiSelesai extends Controller
{
    public function show_selesai()
    {
        $hash = new Hashids();
        $user = Auth::user();

        $sekarang = Carbon::now();
        $sekarang = $sekarang->toDateString();
        $jadwal = Jadwal::select('id_jadwal')->whereRaw('? between tanggal_mulai and tanggal_akhir', $sekarang)->first();
        $dinilais = Jabatan::select('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.pengurangan', 'penilaians.id_penilaian', 'penilaians.catatan_penting', 'bandings.id_banding', 'bandings.status_banding')
            ->join('users', 'jabatans.id_jabatan', '=', 'users.id_jabatan')
            ->join('bidangs', 'jabatans.id_bidang', '=', 'bidangs.id_bidang')
            ->join('strukturals', 'bidangs.id_struktural', 'strukturals.id_struktural')
            ->join('penilaians', 'users.id_user', '=', 'penilaians.id_pegawai')
            ->leftJoin('bandings', 'bandings.id_penilaian', '=', 'penilaians.id_penilaian')
            ->where('jabatans.id_penilai', $user->id_jabatan)
            ->where('penilaians.status_penilaian', 'selesai')
            ->where('penilaians.id_jadwal', $jadwal->id_jadwal)
            ->groupBy('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.id_jadwal', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.pengurangan', 'penilaians.id_penilaian', 'penilaians.catatan_penting', 'bandings.id_banding', 'bandings.status_banding')
            ->get();
        for ($i = 0; $i < sizeof($dinilais); $i++) {
            $performances = PenilaianPerformance::select(DB::raw("CASE
                WHEN tipe_performance = 'min' AND target>realisasi THEN 100*bobot/100
                WHEN tipe_performance = 'min' THEN ((target/realisasi)*100)*bobot/100
                WHEN tipe_performance = 'max' THEN ((realisasi/target) * 100)*bobot/100
                END AS skor"))
                ->join('kpi_performances', 'kpi_performances.id_performance', '=', 'penilaian_performances.id_performance')
                ->where('id_penilaian', $dinilais[$i]->id_penilaian)->get();
            $performances = $performances->sum('skor');
            $performances = $performances * 70 / 100;

            $historyperformance = HistoriPenilaianPerformance::select(DB::raw("CASE
                WHEN tipe_performance = 'min' AND target>realisasi THEN 100*bobot/100
                WHEN tipe_performance = 'min' THEN ((target/realisasi)*100)*bobot/100
                WHEN tipe_performance = 'max' THEN ((realisasi/target) * 100)*bobot/100
                END AS skor"))
                ->join('kpi_performances', 'kpi_performances.id_performance', '=', 'histori_penilaian_performances.id_performance')
                ->where('id_penilaian', $dinilais[$i]->id_penilaian)->get();
            $historyperformance = $historyperformance->sum('skor');
            $historyperformance = $historyperformance * 70 / 100;

            $perilakus = PenilaianPerilaku::select(DB::raw('SUM(nilai_perilaku *20 * 100/6/100)*30/100 AS skor_akhir'))->where('id_penilaian', $dinilais[$i]->id_penilaian)->get();
            $perilakus = (float)$perilakus[0]->skor_akhir;
            $total = round(($performances + $perilakus), 5);
            $total_histori = round(($historyperformance + $perilakus), 5);
            $dinilais[$i]->performance = $performances;
            $dinilais[$i]->historyperformance = $historyperformance;
            $dinilais[$i]->perilaku = $perilakus;

            if ($dinilais[$i]->pengurangan == null && $dinilais[$i]->status_banding == 'diterima') {
                $dinilais[$i]->total_sebelum_pengurangan = "no";
                $dinilais[$i]->total_sebelum_banding = $total_histori;
                $dinilais[$i]->total = $total;
                $dinilais[$i]->capaian = $total . "%";
            } elseif ($dinilais[$i]->pengurangan == null && $dinilais[$i]->status_banding == 'ditolak') {
                $dinilais[$i]->total_sebelum_pengurangan = "no";
                $dinilais[$i]->total_sebelum_banding = "ditolak";
                $dinilais[$i]->total = $total;
                $dinilais[$i]->capaian = $total . "%";
            } elseif ($dinilais[$i]->pengurangan == null && $dinilais[$i]->status_banding == null) {
                $dinilais[$i]->total_sebelum_pengurangan = "no";
                $dinilais[$i]->total_sebelum_banding = "tidak_banding";
                $dinilais[$i]->total = $total;
                $dinilais[$i]->capaian = $total . "%";
            } elseif ($dinilais[$i]->pengurangan != null && $dinilais[$i]->status_banding == 'diterima') {
                $dinilais[$i]->total_sebelum_pengurangan = $total_histori;
                $dinilais[$i]->total_sebelum_banding = $total_histori - ($total_histori * $dinilais[$i]->pengurangan / 100);
                $dinilais[$i]->total = $total - ($total * $dinilais[$i]->pengurangan / 100);
                $dinilais[$i]->capaian = $total - ($total * $dinilais[$i]->pengurangan / 100) . "%";
            } elseif ($dinilais[$i]->pengurangan != null && $dinilais[$i]->status_banding == 'ditolak') {
                $dinilais[$i]->total_sebelum_pengurangan =  $total;
                $dinilais[$i]->total_sebelum_banding = "ditolak";
                $dinilais[$i]->total = $total - ($total * $dinilais[$i]->pengurangan / 100);
                $dinilais[$i]->capaian = $total - ($total * $dinilais[$i]->pengurangan / 100) . "%";
            } elseif ($dinilais[$i]->pengurangan != null && $dinilais[$i]->status_banding == null) {
                $dinilais[$i]->total_sebelum_pengurangan =  $total;
                $dinilais[$i]->total_sebelum_banding = "tidak_banding";
                $dinilais[$i]->total = $total - ($total * $dinilais[$i]->pengurangan / 100);
                $dinilais[$i]->capaian = $total - ($total * $dinilais[$i]->pengurangan / 100) . "%";
            }
            // if ($dinilais[$i]->pengurangan) {
            //     $dinilais[$i]->total_awal = $total;
            //     $dinilais[$i]->total = $total - ($total * 10 / 100);
            //     $dinilais[$i]->capaian = $total - ($total * 10 / 100) . " %";
            // } 
            // else {
            //     $dinilais[$i]->total_awal = "no";
            //     $dinilais[$i]->total = $total;
            //     $dinilais[$i]->capaian = $total . " %";
            // }

            // if($dinilais[$i]->status_banding == 'diterima' && $dinilais[$i]->pengurangan){
            //     $dinilais[$i]->total_sebelum_banding =$total_sebelum_banding - ($total_sebelum_banding * 10 / 100);
            // }elseif($dinilais[$i]->status_banding == 'diterima' && $dinilais[$i]->pengurangan == null)
            // {
            //     $dinilais[$i]->total_sebelum_banding =$total_sebelum_banding;   
            // }
            // else{
            //     $dinilais[$i]->total_sebelum_banding = "no";
            // }
        }

        return view('pegawai.penilai.selesai', compact('dinilais', 'hash'));
    }
}
