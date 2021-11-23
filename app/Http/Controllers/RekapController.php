<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\HistoriPenilaianPerformance;
use App\Models\Jabatan;
use App\Models\Jadwal;
use App\Models\PenilaianPerformance;
use App\Models\PenilaianPerilaku;
use Symfony\Component\HttpFoundation\Request;
use Hashids\Hashids;
use Illuminate\Support\Facades\DB;

class RekapController extends Controller
{
    public function index()
    {
        $filter = "";
        $hash = new Hashids();
        $dinilais = Jabatan::select('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.pengurangan', 'penilaians.id_penilaian', 'penilaians.catatan_penting', 'bandings.id_banding', 'bandings.status_banding', 'penilaians.id_jadwal', 'jadwals.nama_periode', 'jadwals.tanggal_mulai', 'jadwals.tanggal_akhir')
            ->join('users', 'jabatans.id_jabatan', '=', 'users.id_jabatan')
            ->join('bidangs', 'jabatans.id_bidang', '=', 'bidangs.id_bidang')
            ->join('strukturals', 'bidangs.id_struktural', 'strukturals.id_struktural')
            ->join('penilaians', 'users.id_user', '=', 'penilaians.id_pegawai')
            ->join('jadwals', 'penilaians.id_jadwal', '=', 'jadwals.id_jadwal')
            ->leftJoin('bandings', 'bandings.id_penilaian', '=', 'penilaians.id_penilaian')
            ->groupBy('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.id_jadwal', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.pengurangan', 'penilaians.id_penilaian', 'penilaians.catatan_penting', 'bandings.id_banding', 'bandings.status_banding', 'penilaians.id_jadwal', 'jadwals.nama_periode', 'jadwals.tanggal_mulai', 'jadwals.tanggal_akhir')
            ->get();
        $bidangs = Bidang::join('strukturals', 'strukturals.id_struktural', '=', 'bidangs.id_struktural')->get();
        $jadwals = Jadwal::all();
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

            $historyperformance = HistoriPenilaianPerformance::select(DB::raw("CASE
                WHEN tipe_performance = 'min' AND target>realisasi THEN 100
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
            if ($dinilais[$i]->status_penilaian == 'selesai') {
                $dinilais[$i]->status = 'selesai';
            } else {
                $dinilais[$i]->status = 'belum_selesai';
            }
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
        }
        return view('admin.rekap_penilaian', compact('dinilais','jadwals','bidangs','hash','filter'));
    }

    public function filtered(Request $request)
    {
        $hash = new Hashids();
        $bidangs = Bidang::join('strukturals', 'strukturals.id_struktural', '=', 'bidangs.id_struktural')->get();
        $jadwals = Jadwal::all();
        $filter = "filter";
        if ($request->jadwal != null && $request->struktural == null) {
            $dinilais = Jabatan::select('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.pengurangan', 'penilaians.id_penilaian', 'penilaians.catatan_penting', 'bandings.id_banding', 'bandings.status_banding', 'penilaians.id_jadwal', 'jadwals.nama_periode', 'jadwals.tanggal_mulai', 'jadwals.tanggal_akhir')
                ->join('users', 'jabatans.id_jabatan', '=', 'users.id_jabatan')
                ->join('bidangs', 'jabatans.id_bidang', '=', 'bidangs.id_bidang')
                ->join('strukturals', 'bidangs.id_struktural', 'strukturals.id_struktural')
                ->join('penilaians', 'users.id_user', '=', 'penilaians.id_pegawai')
                ->join('jadwals', 'penilaians.id_jadwal', '=', 'jadwals.id_jadwal')
                ->leftJoin('bandings', 'bandings.id_penilaian', '=', 'penilaians.id_penilaian')
                ->where('jadwals.id_jadwal', $hash->decode($request->jadwal))
                ->groupBy('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.id_jadwal', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.pengurangan', 'penilaians.id_penilaian', 'penilaians.catatan_penting', 'bandings.id_banding', 'bandings.status_banding', 'penilaians.id_jadwal', 'jadwals.nama_periode', 'jadwals.tanggal_mulai', 'jadwals.tanggal_akhir')
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

                $historyperformance = HistoriPenilaianPerformance::select(DB::raw("CASE
                WHEN tipe_performance = 'min' AND target>realisasi THEN 100
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
                if ($dinilais[$i]->status_penilaian == 'selesai') {
                    $dinilais[$i]->status = 'selesai';
                } else {
                    $dinilais[$i]->status = 'belum_selesai';
                }
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
            }
            return view('admin.rekap_penilaian', compact('dinilais','jadwals','bidangs','hash','filter'));
        } elseif ($request->struktural != null && $request->jadwal == null) {
            $dinilais = Jabatan::select('users.id_user', 'users.npk', 'users.nama', 'bidangs.id_bidang','jabatans.nama_jabatan', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.pengurangan', 'penilaians.id_penilaian', 'penilaians.catatan_penting', 'bandings.id_banding', 'bandings.status_banding', 'penilaians.id_jadwal', 'jadwals.nama_periode', 'jadwals.tanggal_mulai', 'jadwals.tanggal_akhir')
                ->join('users', 'jabatans.id_jabatan', '=', 'users.id_jabatan')
                ->join('bidangs', 'jabatans.id_bidang', '=', 'bidangs.id_bidang')
                ->join('strukturals', 'bidangs.id_struktural', 'strukturals.id_struktural')
                ->join('penilaians', 'users.id_user', '=', 'penilaians.id_pegawai')
                ->join('jadwals', 'penilaians.id_jadwal', '=', 'jadwals.id_jadwal')
                ->leftJoin('bandings', 'bandings.id_penilaian', '=', 'penilaians.id_penilaian')
                ->where('bidangs.id_bidang', $hash->decode($request->struktural))
                ->groupBy('users.id_user', 'users.npk', 'users.nama', 'bidangs.id_bidang','jabatans.nama_jabatan', 'penilaians.id_jadwal', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.pengurangan', 'penilaians.id_penilaian', 'penilaians.catatan_penting', 'bandings.id_banding', 'bandings.status_banding', 'penilaians.id_jadwal', 'jadwals.nama_periode', 'jadwals.tanggal_mulai', 'jadwals.tanggal_akhir')
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

                $historyperformance = HistoriPenilaianPerformance::select(DB::raw("CASE
                WHEN tipe_performance = 'min' AND target>realisasi THEN 100
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
                if ($dinilais[$i]->status_penilaian == 'selesai') {
                    $dinilais[$i]->status = 'selesai';
                } else {
                    $dinilais[$i]->status = 'belum_selesai';
                }
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
            }
            return view('admin.rekap_penilaian', compact('dinilais','jadwals','bidangs','hash','filter'));
        }elseif($request->jadwal != null && $request->struktural != null)
        {
            $dinilais = Jabatan::select('users.id_user', 'users.npk', 'users.nama', 'bidangs.id_bidang','jabatans.nama_jabatan', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.pengurangan', 'penilaians.id_penilaian', 'penilaians.catatan_penting', 'bandings.id_banding', 'bandings.status_banding', 'penilaians.id_jadwal', 'jadwals.nama_periode', 'jadwals.tanggal_mulai', 'jadwals.tanggal_akhir')
                ->join('users', 'jabatans.id_jabatan', '=', 'users.id_jabatan')
                ->join('bidangs', 'jabatans.id_bidang', '=', 'bidangs.id_bidang')
                ->join('strukturals', 'bidangs.id_struktural', 'strukturals.id_struktural')
                ->join('penilaians', 'users.id_user', '=', 'penilaians.id_pegawai')
                ->join('jadwals', 'penilaians.id_jadwal', '=', 'jadwals.id_jadwal')
                ->leftJoin('bandings', 'bandings.id_penilaian', '=', 'penilaians.id_penilaian')
                ->where('bidangs.id_bidang', $hash->decode($request->struktural))
                ->where('jadwals.id_jadwal', $hash->decode($request->jadwal))
                ->groupBy('users.id_user', 'users.npk', 'users.nama', 'bidangs.id_bidang', 'jabatans.nama_jabatan', 'penilaians.id_jadwal', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.pengurangan', 'penilaians.id_penilaian', 'penilaians.catatan_penting', 'bandings.id_banding', 'bandings.status_banding', 'penilaians.id_jadwal', 'jadwals.nama_periode', 'jadwals.tanggal_mulai', 'jadwals.tanggal_akhir')
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

                $historyperformance = HistoriPenilaianPerformance::select(DB::raw("CASE
                WHEN tipe_performance = 'min' AND target>realisasi THEN 100
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
                if ($dinilais[$i]->status_penilaian == 'selesai') {
                    $dinilais[$i]->status = 'selesai';
                } else {
                    $dinilais[$i]->status = 'belum_selesai';
                }
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
            }
            
            return view('admin.rekap_penilaian', compact('dinilais','jadwals','bidangs','hash','filter'));
        }else{
            return redirect()->route('rekap-penilaian')->with('gagal', 'Tidak ada filter');
        }
    }
}
