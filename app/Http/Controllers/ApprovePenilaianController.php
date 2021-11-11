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

class ApprovePenilaianController extends Controller
{
    public function get_need_approve($id_jabatan)
    {
        $sekarang = Carbon::now();
        $sekarang = $sekarang->toDateString();
        $jadwal = Jadwal::select('id_jadwal')->whereRaw('? between tanggal_mulai and tanggal_akhir', $sekarang)->first();
        $penilaian = Jabatan::select('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.id_penilaian', 'penilaians.catatan_penting')
            ->join('users', 'jabatans.id_jabatan', '=', 'users.id_jabatan')
            ->join('bidangs', 'jabatans.id_bidang', '=', 'bidangs.id_bidang')
            ->join('strukturals', 'bidangs.id_struktural', 'strukturals.id_struktural')
            ->join('penilaians', 'users.id_user', '=', 'penilaians.id_pegawai')
            ->where('jabatans.id_jabatan', $id_jabatan)
            ->where('penilaians.status_penilaian', 'menunggu_verifikasi')
            ->where('penilaians.id_jadwal', $jadwal->id_jadwal)
            ->groupBy('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.id_jadwal', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.id_penilaian', 'penilaians.catatan_penting')
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
        return $penilaian;
    }
    public function show_approve_penilaian()
    {
        $hash = new Hashids();
        $jabatanapasaja = array();
        $penilaians = array();
        $user = Auth::user();
        $cek = Jabatan::find($user->id_jabatan);
        if($cek->id_penilai == null)
        {
            $diapproves = Jabatan::where('id_penilai',$user->id_jabatan)->get();
            foreach($diapproves as $diapprove){
                array_push($jabatanapasaja,$diapprove->id_jabatan);
            }
        }
        
        $dinilais = Jabatan::where('id_penilai',$user->id_jabatan)->get();
        foreach($dinilais as $dinilai){
            $jabatans = Jabatan::where('id_penilai',$dinilai->id_jabatan)->get();
            array_push($jabatanapasaja,$jabatans[0]->id_jabatan);
        }

        foreach($jabatanapasaja as $id_jabatan)
        {
            $penilaian_need_approve = $this->get_need_approve($id_jabatan);
            if($penilaian_need_approve->isEmpty()){
                
            }else{
                array_push($penilaians,$penilaian_need_approve[0]);
            }
            
        }

        return view('pegawai.atasan_penilai.approve_penilaian', compact('penilaians', 'hash'));
    }
}
