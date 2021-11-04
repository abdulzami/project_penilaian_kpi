<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\KpiPerformance;
use App\Models\User;
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
        // $dinilais = Jabatan::leftJoin('kpi_performances','jabatans.id_jabatan','=','kpi_performances.id_jabatan')
        // ->select('jabatans.id_jabatan','jabatans.nama_jabatan',DB::raw('SUM(kpi_performances.bobot) as total_bobot'))
        // ->groupBy('kpi_performances.id_jabatan','jabatans.id_jabatan','jabatans.nama_jabatan')
        // ->get();
        // return $dinilais;
        $dinilais = Jabatan::
         select('users.id_user','users.nama','jabatans.nama_jabatan','penilaians.id_penilaian',DB::raw('SUM(kpi_performances.bobot) as total_bobot_jabatan'))
        ->join('users','jabatans.id_jabatan','=','users.id_jabatan')
        ->leftJoin('penilaians','users.id_user','=','penilaians.id_pegawai')
        ->leftJoin('kpi_performances','users.id_jabatan','=','kpi_performances.id_jabatan')
        ->where('jabatans.id_penilai',$user->id_jabatan)
        ->where('penilaians.status_penilaian',null)
        ->groupBy('users.id_user','users.nama','jabatans.nama_jabatan','penilaians.id_penilaian')
        ->get();

        return $dinilais;
        return view('pegawai.penilai.belum_dinilai',compact('dinilais','hash'));
    }
}
