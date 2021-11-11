<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Jadwal;
use App\Models\PenilaianPerformance;
use App\Models\PenilaianPerilaku;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PenilaianPenilaiMenungguVerifikasi extends Controller
{
    public function show_menunggu_verifikasi()
    {
        $hash = new Hashids();
        $user = Auth::user();

        $sekarang = Carbon::now();
        $sekarang = $sekarang->toDateString();
        $jadwal = Jadwal::select('id_jadwal')->whereRaw('? between tanggal_mulai and tanggal_akhir', $sekarang)->first();
        $dinilais = Jabatan::select('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.id_penilaian', 'penilaians.catatan_penting')
            ->join('users', 'jabatans.id_jabatan', '=', 'users.id_jabatan')
            ->join('bidangs', 'jabatans.id_bidang', '=', 'bidangs.id_bidang')
            ->join('strukturals', 'bidangs.id_struktural', 'strukturals.id_struktural')
            ->join('penilaians', 'users.id_user', '=', 'penilaians.id_pegawai')
            ->where('jabatans.id_penilai', $user->id_jabatan)
            ->where('penilaians.status_penilaian', 'menunggu_verifikasi')
            ->where('penilaians.id_jadwal', $jadwal->id_jadwal)
            ->groupBy('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.id_jadwal', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.id_penilaian', 'penilaians.catatan_penting')
            ->get();
        for ($i = 0; $i < sizeof($dinilais); $i++) {
            $performances = PenilaianPerformance::select(DB::raw("CASE
                WHEN tipe_performance = 'min' AND target>realisasi THEN 100
                WHEN tipe_performance = 'min' THEN ((target/realisasi)*100)*bobot/100
                WHEN tipe_performance = 'max' THEN ((realisasi/target) * 100)*bobot/100
                END AS skor"))
                ->join('kpi_performances', 'kpi_performances.id_performance', '=', 'penilaian_performances.id_performance')
                ->where('id_penilaian', $dinilais[$i]->id_penilaian)->get();
            $performances = $performances->sum('skor');
            $performances = $performances * 70 / 100;
            $perilakus = PenilaianPerilaku::select(DB::raw('SUM(nilai_perilaku *20 * 100/6/100)*30/100 AS skor_akhir'))->where('id_penilaian', $dinilais[$i]->id_penilaian)->get();
            $perilakus = (float)$perilakus[0]->skor_akhir;
            $total = round(($performances + $perilakus), 5);
            $dinilais[$i]->performance = $performances;
            $dinilais[$i]->perilaku = $perilakus;
            $dinilais[$i]->total = $total;
            $dinilais[$i]->capaian = $total . " %";
        }
        return view('pegawai.penilai.menunggu_verifikasi', compact('dinilais', 'hash'));
    }
}
