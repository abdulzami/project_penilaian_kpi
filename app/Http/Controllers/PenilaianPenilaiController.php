<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Jadwal;
use App\Models\KpiPerformance;
use App\Models\User;
use Carbon\Carbon;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPUnit\TextUI\XmlConfiguration\Group;

class PenilaianPenilaiController extends Controller
{
    public function show_belum_dinilai()
    {
        $hash = new Hashids();
        $user = Auth::user();

        $sekarang = Carbon::now();
        $sekarang = $sekarang->toDateString();
        $jadwal = Jadwal::select('id_jadwal')->whereRaw('? between tanggal_mulai and tanggal_akhir', $sekarang)->first();
        $dinilais = Jabatan::
         select('users.id_user','users.nama','jabatans.nama_jabatan','penilaians.status_penilaian','strukturals.nama_struktural','bidangs.nama_bidang')
        ->join('users','jabatans.id_jabatan','=','users.id_jabatan')
        ->join('bidangs','jabatans.id_bidang','=','bidangs.id_bidang')
        ->join('strukturals','bidangs.id_struktural','strukturals.id_struktural')
        ->leftJoin('penilaians','users.id_user','=','penilaians.id_pegawai')
        ->where('jabatans.id_penilai',$user->id_jabatan)
        ->where('penilaians.status_penilaian','belum_dinilai')
        ->where('penilaians.id_jadwal',$jadwal->id_jadwal)
        ->groupBy('users.id_user','users.nama','jabatans.nama_jabatan','penilaians.id_jadwal','penilaians.status_penilaian','strukturals.nama_struktural','bidangs.nama_bidang')
        ->get();
        return view('pegawai.penilai.belum_dinilai',compact('dinilais','hash'));
    }
}
