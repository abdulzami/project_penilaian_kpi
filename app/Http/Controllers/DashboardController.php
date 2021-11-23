<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
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
use App\Http\Controllers\ApprovePenilaianController;
use Error;
use ErrorException;

class DashboardController extends Controller
{
    public function show_approve_penilaian()
    {
        $hash = new Hashids();
        $jabatanapasaja = array();
        $penilaians = array();
        $user = Auth::user();
        $cek = Jabatan::find($user->id_jabatan);
        if ($cek->id_penilai == null) {
            $diapproves = Jabatan::where('id_penilai', $user->id_jabatan)->get();
            foreach ($diapproves as $diapprove) {
                array_push($jabatanapasaja, $diapprove->id_jabatan);
            }
        }

        $dinilais = Jabatan::where('id_penilai', $user->id_jabatan)->get();
        foreach ($dinilais as $dinilai) {
            $jabatans = Jabatan::where('id_penilai', $dinilai->id_jabatan)->get();
            array_push($jabatanapasaja, $jabatans[0]->id_jabatan);
        }

        foreach ($jabatanapasaja as $id_jabatan) {
            $ap = new ApprovePenilaianController();
            $penilaian_need_approve = $ap->get_need_approve($id_jabatan);
            if ($penilaian_need_approve->isEmpty()) {
            } else {
                array_push($penilaians, $penilaian_need_approve[0]);
            }
        }

        return $penilaians;
    }

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
            return view('admin.dashboard', compact('nama_periode'));
        } elseif (Auth::user()->level == 'pegawai') {
            if (!$jadwal->isEmpty()) {
                $penilaian = Penilaian::select(
                    'penilaians.id_penilaian',
                    'penilaians.id_pegawai',
                    'penilaians.id_jadwal',
                    'penilaians.id_pegawai',
                    'penilaians.id_penilai',
                    'penilaians.status_penilaian',
                    'penilaians.catatan_penting',
                    'penilaians.pengurangan',
                    'bandings.id_banding',
                    'bandings.status_banding',
                    'bandings.alasan_tolak'
                )
                    ->where('id_pegawai', Auth::user()->id_user)->where('id_jadwal', $jadwal[0]->id_jadwal)
                    ->leftJoin('bandings', 'bandings.id_penilaian', '=', 'penilaians.id_penilaian')
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
                    $kpiperformances = "";
                } else {
                    $penilaian = $penilaian[0];
                    $kpiperformances = PenilaianPerformance::select('kpi_performances.kategori', 'kpi_performances.tipe_performance', 'kpi_performances.indikator_kpi', 'kpi_performances.definisi', 'kpi_performances.target', 'kpi_performances.satuan', 'kpi_performances.bobot', 'penilaian_performances.realisasi', 'histori_penilaian_performances.realisasi as realisasi_lama')
                        ->leftjoin('kpi_performances', 'kpi_performances.id_performance', '=', 'penilaian_performances.id_performance')
                        ->leftJoin('histori_penilaian_performances', 'histori_penilaian_performances.id_penilaian', '=', 'penilaian_performances.id_penilaian')
                        ->where('penilaian_performances.id_penilaian', $penilaian->id_penilaian)->get();
                }
            } else {
                $penilaian = "";
                $kpiperformances = "";
            }

            if (!$jadwal->isEmpty()) {
                $belum_dinilai = Jabatan::select('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.id_penilaian', 'penilaians.catatan_penting')
                    ->join('users', 'jabatans.id_jabatan', '=', 'users.id_jabatan')
                    ->join('bidangs', 'jabatans.id_bidang', '=', 'bidangs.id_bidang')
                    ->join('strukturals', 'bidangs.id_struktural', 'strukturals.id_struktural')
                    ->join('penilaians', 'users.id_user', '=', 'penilaians.id_pegawai')
                    ->where('jabatans.id_penilai',  Auth::user()->id_jabatan)
                    ->where('penilaians.status_penilaian', 'belum_dinilai')
                    ->where('penilaians.id_jadwal', $jadwal[0]->id_jadwal)
                    ->groupBy('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.id_jadwal', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.id_penilaian', 'penilaians.catatan_penting')
                    ->count();

                $menunggu_verifikasi = Jabatan::select('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.id_penilaian', 'penilaians.catatan_penting')
                    ->join('users', 'jabatans.id_jabatan', '=', 'users.id_jabatan')
                    ->join('bidangs', 'jabatans.id_bidang', '=', 'bidangs.id_bidang')
                    ->join('strukturals', 'bidangs.id_struktural', 'strukturals.id_struktural')
                    ->join('penilaians', 'users.id_user', '=', 'penilaians.id_pegawai')
                    ->where('jabatans.id_penilai',  Auth::user()->id_jabatan)
                    ->where('penilaians.status_penilaian', 'menunggu_verifikasi')
                    ->where('penilaians.id_jadwal', $jadwal[0]->id_jadwal)
                    ->groupBy('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.id_jadwal', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.id_penilaian', 'penilaians.catatan_penting')
                    ->count();

                $selesai = Jabatan::select('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.id_penilaian', 'penilaians.catatan_penting')
                    ->join('users', 'jabatans.id_jabatan', '=', 'users.id_jabatan')
                    ->join('bidangs', 'jabatans.id_bidang', '=', 'bidangs.id_bidang')
                    ->join('strukturals', 'bidangs.id_struktural', 'strukturals.id_struktural')
                    ->join('penilaians', 'users.id_user', '=', 'penilaians.id_pegawai')
                    ->where('jabatans.id_penilai',  Auth::user()->id_jabatan)
                    ->where('penilaians.status_penilaian', 'selesai')
                    ->where('penilaians.id_jadwal', $jadwal[0]->id_jadwal)
                    ->groupBy('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.id_jadwal', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.id_penilaian', 'penilaians.catatan_penting')
                    ->count();

                $banding_penilaian = Jabatan::select('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.id_penilaian', 'penilaians.catatan_penting')
                    ->join('users', 'jabatans.id_jabatan', '=', 'users.id_jabatan')
                    ->join('bidangs', 'jabatans.id_bidang', '=', 'bidangs.id_bidang')
                    ->join('strukturals', 'bidangs.id_struktural', 'strukturals.id_struktural')
                    ->join('penilaians', 'users.id_user', '=', 'penilaians.id_pegawai')
                    ->where('jabatans.id_penilai',  Auth::user()->id_jabatan)
                    ->where('penilaians.status_penilaian', 'terverifikasi')
                    ->where('penilaians.id_jadwal', $jadwal[0]->id_jadwal)
                    ->groupBy('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.id_jadwal', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.id_penilaian', 'penilaians.catatan_penting')
                    ->count();
                try {
                    $membutuhkan_verifikasi = $this->show_approve_penilaian();
                } catch (ErrorException $e) {
                    $membutuhkan_verifikasi = "";
                }
                return view('pegawai.dashboard', compact('nama_periode', 'menunggu_verifikasi', 'belum_dinilai', 'selesai', 'banding_penilaian', 'penilaian', 'membutuhkan_verifikasi', 'kpiperformances', 'hash'));
            }else{
                return view('pegawai.dashboard');
            }
        } else {
            return redirect('/')->with('error', 'Anda tidak punya akses !');
        }
    }
}
