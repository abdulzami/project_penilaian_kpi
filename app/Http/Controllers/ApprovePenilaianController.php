<?php

namespace App\Http\Controllers;

use App\Models\Banding;
use App\Models\HistoriPenilaianPerformance;
use App\Models\Jabatan;
use App\Models\Jadwal;
use App\Models\Penilaian;
use App\Models\PenilaianPerformance;
use App\Models\PenilaianPerilaku;
use App\Models\User;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovePenilaianController extends Controller
{
    public function get_need_approve($id_jabatan)
    {
        $sekarang = Carbon::now();
        $sekarang = $sekarang->toDateString();
        $jadwal = Jadwal::select('id_jadwal')->whereRaw('? between tanggal_mulai and tanggal_akhir', $sekarang)->first();
        $penilaian = Jabatan::select('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.id_penilaian', 'penilaians.catatan_penting', 'bandings.id_banding', 'bandings.status_banding')
            ->join('users', 'jabatans.id_jabatan', '=', 'users.id_jabatan')
            ->join('bidangs', 'jabatans.id_bidang', '=', 'bidangs.id_bidang')
            ->join('strukturals', 'bidangs.id_struktural', 'strukturals.id_struktural')
            ->join('penilaians', 'users.id_user', '=', 'penilaians.id_pegawai')
            ->leftJoin('bandings', 'bandings.id_penilaian', '=', 'penilaians.id_penilaian')
            ->where('jabatans.id_jabatan', $id_jabatan)
            ->where('penilaians.status_penilaian', 'menunggu_verifikasi')
            ->where('penilaians.id_jadwal', $jadwal->id_jadwal)
            ->groupBy('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.id_jadwal', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.id_penilaian', 'penilaians.catatan_penting', 'bandings.id_banding', 'bandings.status_banding')
            ->get();
        for ($i = 0; $i < sizeof($penilaian); $i++) {
            $performances = PenilaianPerformance::select(DB::raw("CASE
                WHEN tipe_performance = 'min' AND target>realisasi THEN 100*bobot/100
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
        return $penilaian;
    }

    public function get_need_approve2($id_jabatan)
    {
        $sekarang = Carbon::now();
        $sekarang = $sekarang->toDateString();
        $jadwal = Jadwal::select('id_jadwal')->whereRaw('? between tanggal_mulai and tanggal_akhir', $sekarang)->first();
        $penilaian = Jabatan::select('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.id_penilaian', 'penilaians.pengurangan', 'penilaians.catatan_penting', 'bandings.id_banding', 'bandings.status_banding')
            ->join('users', 'jabatans.id_jabatan', '=', 'users.id_jabatan')
            ->join('bidangs', 'jabatans.id_bidang', '=', 'bidangs.id_bidang')
            ->join('strukturals', 'bidangs.id_struktural', 'strukturals.id_struktural')
            ->join('penilaians', 'users.id_user', '=', 'penilaians.id_pegawai')
            ->leftJoin('bandings', 'bandings.id_penilaian', '=', 'penilaians.id_penilaian')
            ->where('jabatans.id_jabatan', $id_jabatan)
            ->where('bandings.status_banding', 'diterima_mv')
            ->where('penilaians.id_jadwal', $jadwal->id_jadwal)
            ->groupBy('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.id_jadwal', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.id_penilaian', 'penilaians.pengurangan', 'penilaians.catatan_penting', 'bandings.id_banding', 'bandings.status_banding')
            ->get();
        for ($i = 0; $i < sizeof($penilaian); $i++) {
            $performances = PenilaianPerformance::select(DB::raw("CASE
                WHEN tipe_performance = 'min' AND target>realisasi THEN 100*bobot/100
                WHEN tipe_performance = 'min' THEN ((target/realisasi)*100)*bobot/100
                WHEN tipe_performance = 'max' THEN ((realisasi/target) * 100)*bobot/100
                END AS skor"))
                ->join('kpi_performances', 'kpi_performances.id_performance', '=', 'penilaian_performances.id_performance')
                ->where('id_penilaian', $penilaian[$i]->id_penilaian)->get();

            $historyperformance = HistoriPenilaianPerformance::select(DB::raw("CASE
                WHEN tipe_performance = 'min' AND target>realisasi THEN 100*bobot/100
                WHEN tipe_performance = 'min' THEN ((target/realisasi)*100)*bobot/100
                WHEN tipe_performance = 'max' THEN ((realisasi/target) * 100)*bobot/100
                END AS skor"))
                ->join('kpi_performances', 'kpi_performances.id_performance', '=', 'histori_penilaian_performances.id_performance')
                ->where('id_penilaian', $penilaian[$i]->id_penilaian)->get();
            $historyperformance = $historyperformance->sum('skor');
            $historyperformance = $historyperformance * 70 / 100;

            $performances = $performances->sum('skor');
            $performances = $performances * 70 / 100;
            $perilakus = PenilaianPerilaku::select(DB::raw('SUM(nilai_perilaku *20 * 100/6/100)*30/100 AS skor_akhir'))->where('id_penilaian', $penilaian[$i]->id_penilaian)->get();
            $perilakus = (float)$perilakus[0]->skor_akhir;
            $total = round(($performances + $perilakus), 5);
            $total_histori = round(($historyperformance + $perilakus), 5);

            if ($penilaian[$i]->pengurangan == null && $penilaian[$i]->status_banding == null) {
                $penilaian[$i]->total_sebelum_pengurangan = "no";
                $penilaian[$i]->total_sebelum_banding = "belum_diajukan";
                $penilaian[$i]->total = $total;
                $penilaian[$i]->capaian = $total . "%";
            } elseif ($penilaian[$i]->pengurangan == null && $penilaian[$i]->status_banding == 'proses') {
                $penilaian[$i]->total_sebelum_pengurangan = "no";
                $penilaian[$i]->total_sebelum_banding = "proses";
                $penilaian[$i]->total = $total;
                $penilaian[$i]->capaian = $total . "%";
            } elseif ($penilaian[$i]->pengurangan == null && $penilaian[$i]->status_banding == 'diterima_mv') {
                $penilaian[$i]->total_sebelum_pengurangan = "no";
                $penilaian[$i]->total_sebelum_banding = $total_histori;
                $penilaian[$i]->total = $total;
                $penilaian[$i]->capaian = $total . "%";
            } elseif ($penilaian[$i]->pengurangan != null && $penilaian[$i]->status_banding == null) {
                $penilaian[$i]->total_sebelum_pengurangan = $total;
                $penilaian[$i]->total_sebelum_banding = "belum_diajukan";
                $penilaian[$i]->total = $total - ($total * $penilaian[$i]->pengurangan / 100);
                $penilaian[$i]->capaian = $total - ($total * $penilaian[$i]->pengurangan / 100) . "%";
            } elseif ($penilaian[$i]->pengurangan != null && $penilaian[$i]->status_banding == 'proses') {
                $penilaian[$i]->total_sebelum_pengurangan = $total;
                $penilaian[$i]->total_sebelum_banding = $total - ($total * $penilaian[$i]->pengurangan / 100);
                $penilaian[$i]->total = $total - ($total * $penilaian[$i]->pengurangan / 100);
                $penilaian[$i]->capaian = $total - ($total * $penilaian[$i]->pengurangan / 100) . "%";
            } elseif ($penilaian[$i]->pengurangan != null && $penilaian[$i]->status_banding == 'diterima_mv') {
                $penilaian[$i]->total_sebelum_pengurangan =  $total_histori;
                $penilaian[$i]->total_sebelum_banding =  $total_histori - ($total_histori * $penilaian[$i]->pengurangan / 100);
                $penilaian[$i]->total = $total - ($total * $penilaian[$i]->pengurangan / 100);
                $penilaian[$i]->capaian = $total - ($total * $penilaian[$i]->pengurangan / 100) . "%";
            }
        }
        return $penilaian;
    }

    public function show_approve_penilaian()
    {
        $hash = new Hashids();
        $jabatanapasaja = array();
        $penilaians = array();
        $penilaians2 = array();
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
            $penilaian_need_approve = $this->get_need_approve($id_jabatan);
            if ($penilaian_need_approve->isEmpty()) {
            } else {
                array_push($penilaians, $penilaian_need_approve);
            }
        }
        
        if ($penilaians) {
            $penilaians_temp = array();
            array_push($penilaians_temp, $penilaians[0][0]);
            if(sizeof($penilaians) == 2){
                foreach ($penilaians[1] as $di_approve_2) {
                    array_push($penilaians_temp, $di_approve_2);
                }
                $penilaians = $penilaians_temp;
            }else{
                $penilaians = $penilaians[0];
            }
            
        }

        foreach ($jabatanapasaja as $id_jabatan) {
            $penilaian_need_approve2 = $this->get_need_approve2($id_jabatan);
            if ($penilaian_need_approve2->isEmpty()) {
            } else {
                array_push($penilaians2, $penilaian_need_approve2);
            }
        }
        
        if ($penilaians2) {
            $penilaians_temp2 = array();
            array_push($penilaians_temp2, $penilaians2[0][0]);
            if(sizeof($penilaians2) == 2){
                foreach ($penilaians2[1] as $di_approve_2_2) {
                    array_push($penilaians_temp2, $di_approve_2_2);
                }
                $penilaians2 = $penilaians_temp2;
            }else{
                $penilaians2 = $penilaians2[0];
            }
            
        }

        return view('pegawai.atasan_penilai.approve_penilaian', compact('penilaians', 'penilaians2', 'hash'));
    }

    public function review_penilaian($id)
    {
        $hash = new Hashids();
        $id_penilaian = $hash->decode($id);
        $penilaian = Penilaian::select('id_penilaian', 'id_pegawai', 'catatan_penting')->find($id_penilaian);
        for ($i = 0; $i < sizeof($penilaian); $i++) {
            $performances = PenilaianPerformance::select(DB::raw("CASE
                WHEN tipe_performance = 'min' AND target>realisasi THEN 100*bobot/100
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
        $id_pegawai = $penilaian[0]->id_pegawai;
        $pegawai = User::find($id_pegawai);
        $catatan_penting = $penilaian[0]->catatan_penting;
        $total = $penilaian[0]->total;
        return view('pegawai.atasan_penilai.review_penilaian', compact('catatan_penting', 'hash', 'total', 'pegawai', 'id'));
    }

    public function approve_penilaian(Request $request, $id)
    {
        $hash = new Hashids();
        $id_penilaian = $hash->decode($id);
        request()->validate(
            [
                'pengurangan_nilai' => 'required|numeric|max:100|min:1',
            ]
        );
        try {
            Penilaian::where('id_penilaian', $id_penilaian)->update([
                'pengurangan' => $request->pengurangan_nilai,
                'status_penilaian' => 'terverifikasi'
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Gagal aprrove penilaian');
        }
        return redirect('approve-penilaian')->with('success', 'Sukses approve penilaian');
    }

    public function approve_penilaian_langsung($id)
    {
        $hash = new Hashids();
        $id_penilaian = $hash->decode($id);
        $banding = Banding::where('id_penilaian', $id_penilaian[0])->get();
        if ($banding->isEmpty()) {
            $status = 'terverifikasi';
        } else {
            if ($banding[0]->status_banding == 'diterima') {
                $status = 'selesai';
            } else {
                return back()->with('gagal', 'Gagal aprrove penilaian');
            }
        }
        try {
            Penilaian::where('id_penilaian', $id_penilaian)->update([
                'status_penilaian' => $status
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Gagal aprrove penilaian');
        }
        return redirect('approve-penilaian')->with('success', 'Sukses approve penilaian');
    }

    public function approve_banding_penilaian($id)
    {
        $hash = new Hashids();
        $id_penilaian = $hash->decode($id);
        try {
            Penilaian::where('id_penilaian', $id_penilaian)->update([
                'status_penilaian' => 'selesai'
            ]);
            Banding::where('id_penilaian', $id_penilaian)->update([
                'status_banding' => 'diterima'
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Gagal aprrove penilaian');
        }
        return redirect('approve-penilaian')->with('success', 'Sukses approve penilaian');
    }

    public function approve_penilaian_dinilai($id)
    {
        $hash = new Hashids();
        $id_penilaian = $hash->decode($id);
        try {
            Penilaian::where('id_penilaian', $id_penilaian)->update([
                'status_penilaian' => 'selesai'
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Gagal aprrove penilaian');
        }
        return redirect('dashboard')->with('success', 'Sukses approve penilaian');
    }
}
