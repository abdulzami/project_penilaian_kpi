<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Jadwal;
use App\Models\KpiPerformance;
use App\Models\KpiPerilaku;
use App\Models\Penilaian;
use App\Models\PenilaianPerformance;
use App\Models\PenilaianPerilaku;
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
        $dinilais = Jabatan::select('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.id_penilaian')
            ->join('users', 'jabatans.id_jabatan', '=', 'users.id_jabatan')
            ->join('bidangs', 'jabatans.id_bidang', '=', 'bidangs.id_bidang')
            ->join('strukturals', 'bidangs.id_struktural', 'strukturals.id_struktural')
            ->join('penilaians', 'users.id_user', '=', 'penilaians.id_pegawai')
            ->where('jabatans.id_penilai', $user->id_jabatan)
            ->where('penilaians.status_penilaian', 'belum_dinilai')
            ->where('penilaians.id_jadwal', $jadwal->id_jadwal)
            ->groupBy('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.id_jadwal', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.id_penilaian')
            ->get();

        return view('pegawai.penilai.belum_dinilai', compact('dinilais', 'hash'));
    }
    //start performance
    public function create_penilaian_kpi_performance($id)
    {
        $hash = new Hashids();
        $id_penilaian = $hash->decode($id);
        $penilaian = Penilaian::find($id_penilaian);
        $id_pegawai = $penilaian[0]->id_pegawai;
        $pegawai = User::find($id_pegawai);
        $id_jabatan = $pegawai->id_jabatan;
        $kpiperformances = KpiPerformance::where('id_jabatan', $id_jabatan)->get();

        $penilaianperformance = PenilaianPerformance::where('id_penilaian', $id_penilaian[0])
            ->join('kpi_performances', 'kpi_performances.id_performance', '=', 'penilaian_performances.id_performance')
            ->get();
        if ($penilaianperformance->isEmpty()) {
            return view('pegawai.penilai.create_penilaian_kpi_performance', compact('kpiperformances', 'hash', 'pegawai', 'id'));
        } else {
            return view('pegawai.penilai.edit_penilaian_kpi_performance', compact('penilaianperformance', 'hash', 'pegawai', 'id'));
        }
    }

    public function store_penilaian_kpi_performance(Request $request, $id)
    {
        $hash = new Hashids();
        $id_penilaian = $hash->decode($id);
        $penilaian = Penilaian::find($id_penilaian);
        $id_pegawai = $penilaian[0]->id_pegawai;
        $pegawai = User::find($id_pegawai);
        $id_jabatan = $pegawai->id_jabatan;
        $kpiperformances = KpiPerformance::where('id_jabatan', $id_jabatan)->get();
        try {
            foreach ($kpiperformances as $kpiperformance) {
                $nameinput = $id . $hash->encode($kpiperformance->id_performance);
                if (str_contains($request->$nameinput, ',')) {
                    $request->$nameinput = str_replace(',', '.', $request->$nameinput);
                }
                PenilaianPerformance::create([
                    'id_penilaian' => $id_penilaian[0],
                    'id_performance' => $kpiperformance->id_performance,
                    'realisasi' => $request->$nameinput,
                ]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            $pperformance = PenilaianPerformance::where('id_penilaian',$id_penilaian);
            $pperformance->delete();
            return back()->with('gagal', 'Gagal melakukan penilaian kpi performance. Tidak boleh ada yang kosong atau format salah');
        }
        return back()->with('success', 'Sukses melakukan penilaian kpi performance');
    }

    public function update_penilaian_kpi_performance(Request $request, $id)
    {
        $hash = new Hashids();
        $id_penilaian = $hash->decode($id);
        $penilaianperformances = PenilaianPerformance::where('id_penilaian', $id_penilaian[0])->get();
        try {
            foreach ($penilaianperformances as $penilaianperformance) {
                $nameinput = $id . $hash->encode($penilaianperformance->id_performance);
                if (str_contains($request->$nameinput, ',')) {
                    $request->$nameinput = str_replace(',', '.', $request->$nameinput);
                }
                PenilaianPerformance::where('id_penilaian_performance', $penilaianperformance->id_penilaian_performance)->update([
                    'realisasi' => $request->$nameinput
                ]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Gagal ubah penilaian kpi performance. Tidak boleh ada yang kosong atau format salah');
        }
        return back()->with('success', 'Sukses ubah penilaian kpi performance');
    }
    //end performance

    //start perilaku
    public function create_penilaian_kpi_perilaku($id)
    {
        $hash = new Hashids();
        $id_penilaian = $hash->decode($id);
        $penilaian = Penilaian::find($id_penilaian);
        $id_pegawai = $penilaian[0]->id_pegawai;
        $pegawai = User::find($id_pegawai);
        $perilakus = KpiPerilaku::select('id_perilaku', 'nama_kpi', 'ekselen', 'baik', 'cukup', 'kurang', 'kurang_sekali')->get();
        $penilaianperilakus = KpiPerilaku::select('kpi_perilakus.id_perilaku', 'nama_kpi', 'ekselen', 'baik', 'cukup', 'kurang', 'kurang_sekali', 'nilai_perilaku')
            ->join('penilaian_perilakus', 'penilaian_perilakus.id_perilaku', '=', 'kpi_perilakus.id_perilaku')
            ->where('penilaian_perilakus.id_penilaian',$id_penilaian)
            ->get();
        if ($penilaianperilakus->isEmpty()) {
            return view('pegawai.penilai.create_penilaian_kpi_perilaku', compact('perilakus', 'hash', 'pegawai', 'id'));
        } else {
            return view('pegawai.penilai.edit_penilaian_kpi_perilaku', compact('penilaianperilakus', 'hash', 'pegawai', 'id'));
        }
    }

    public function store_penilaian_kpi_perilaku(Request $request, $id)
    {
        $hash = new Hashids();
        $id_penilaian = $hash->decode($id);
        $perilakus = KpiPerilaku::select('id_perilaku', 'nama_kpi', 'ekselen', 'baik', 'cukup', 'kurang', 'kurang_sekali')->get();
        try {
            foreach ($perilakus as  $perilaku) {
                $nameinput = $id . $hash->encode($perilaku->id_perilaku);
                if ($request->$nameinput < 1 || $request->$nameinput > 5) {
                    $pperilaku = PenilaianPerilaku::where('id_penilaian',$id_penilaian);
                    $pperilaku->delete();
                    return back()->with('gagal', 'Gagal melakukan penilaian kpi perilaku. Tidak boleh ada yang kosong atau format salah');
                }
                PenilaianPerilaku::create([
                    'id_penilaian' => $id_penilaian[0],
                    'id_perilaku' => $perilaku->id_perilaku,
                    'nilai_perilaku' => $request->$nameinput,
                ]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            $pperilaku = PenilaianPerilaku::where('id_penilaian',$id_penilaian);
            $pperilaku->delete();
            return back()->with('gagal', 'Gagal melakukan penilaian kpi perilaku. Tidak boleh ada yang kosong atau format salah');
        }
        return back()->with('success', 'Sukses melakukan penilaian kpi perilaku');
    }

    public function update_penilaian_kpi_perilaku(Request $request, $id)
    {
        $hash = new Hashids();
        $id_penilaian = $hash->decode($id);
        $penilaianperilakus = PenilaianPerilaku::where('id_penilaian', $id_penilaian[0])->get();
        try {
            foreach ($penilaianperilakus as $penilaianperilaku) {
                $nameinput = $id . $hash->encode($penilaianperilaku->id_perilaku);
                if ($request->$nameinput < 1 || $request->$nameinput > 5) {
                    return back()->with('gagal', 'Gagal melakukan penilaian kpi perilaku. Tidak boleh ada yang kosong atau format salah');
                }
                PenilaianPerilaku::where('id_penilaian_perilaku', $penilaianperilaku->id_penilaian_perilaku)->update([
                    'nilai_perilaku' => $request->$nameinput
                ]);
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Gagal ubah penilaian kpi performance. Tidak boleh ada yang kosong atau format salah');
        }
        return back()->with('success', 'Sukses ubah penilaian kpi performance');
    }
    //end perilaku

    public function penilaian_catatan_penting($id)
    {
        $hash = new Hashids();
        $id_penilaian = $hash->decode($id);
        $penilaian = Penilaian::select('id_pegawai','catatan_penting')->find($id_penilaian);
        $id_pegawai = $penilaian[0]->id_pegawai;
        $pegawai = User::find($id_pegawai);
        $catatan_penting = $penilaian[0]->catatan_penting;
        return view('pegawai.penilai.catatan_penting',compact('catatan_penting', 'hash', 'pegawai','id'));
    }

    public function update_penilaian_catatan_penting(Request $request,$id)
    {
        $hash = new Hashids();
        $id_penilaian = $hash->decode($id);
        request()->validate(
            [
                'catatan_penting' => 'max:255',
            ]
        );
        try {
            Penilaian::where('id_penilaian', $id_penilaian)->update([
                'catatan_penting' => $request->catatan_penting
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Gagal ubah catatan penting');
        }
        return back()->with('success', 'Sukses ubah catatan penting');
    }
}
