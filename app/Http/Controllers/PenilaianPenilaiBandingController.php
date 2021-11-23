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

class PenilaianPenilaiBandingController extends Controller
{
    public function show_banding_penilaian()
    {
        $hash = new Hashids();
        $user = Auth::user();

        $sekarang = Carbon::now();
        $sekarang = $sekarang->toDateString();
        $jadwal = Jadwal::select('id_jadwal')->whereRaw('? between tanggal_mulai and tanggal_akhir', $sekarang)->first();
        $dinilais = Jabatan::select('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.id_penilaian', 'penilaians.catatan_penting', 'penilaians.pengurangan', 'bandings.id_banding', 'bandings.status_banding')
            ->join('users', 'jabatans.id_jabatan', '=', 'users.id_jabatan')
            ->join('bidangs', 'jabatans.id_bidang', '=', 'bidangs.id_bidang')
            ->join('strukturals', 'bidangs.id_struktural', 'strukturals.id_struktural')
            ->join('penilaians', 'users.id_user', '=', 'penilaians.id_pegawai')
            ->leftJoin('bandings', 'bandings.id_penilaian', '=', 'penilaians.id_penilaian')
            ->where('jabatans.id_penilai', $user->id_jabatan)
            ->where('penilaians.id_jadwal', $jadwal->id_jadwal)
            ->where('penilaians.status_penilaian', 'terverifikasi')
            ->where('bidangs.id_bidang', '!=', null)
            ->groupBy('users.id_user', 'users.npk', 'users.nama', 'jabatans.nama_jabatan', 'penilaians.id_jadwal', 'penilaians.status_penilaian', 'strukturals.nama_struktural', 'bidangs.nama_bidang', 'penilaians.id_penilaian', 'penilaians.catatan_penting', 'penilaians.pengurangan', 'bandings.id_banding', 'bandings.status_banding')
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
            $dinilais[$i]->perilaku = $perilakus;

            if ($dinilais[$i]->pengurangan == null && $dinilais[$i]->status_banding == null) {
                $dinilais[$i]->total_sebelum_pengurangan = "no";
                $dinilais[$i]->total_sebelum_banding = "belum_diajukan";
                $dinilais[$i]->total = $total;
                $dinilais[$i]->capaian = $total . "%";
            }elseif ($dinilais[$i]->pengurangan == null && $dinilais[$i]->status_banding == 'proses') {
                $dinilais[$i]->total_sebelum_pengurangan = "no";
                $dinilais[$i]->total_sebelum_banding = "proses";
                $dinilais[$i]->total = $total;
                $dinilais[$i]->capaian = $total . "%";
            }elseif ($dinilais[$i]->pengurangan == null && $dinilais[$i]->status_banding == 'diterima_mv') {
                $dinilais[$i]->total_sebelum_pengurangan = "no";
                $dinilais[$i]->total_sebelum_banding = $total_histori;
                $dinilais[$i]->total = $total;
                $dinilais[$i]->capaian = $total . "%";
            }elseif ($dinilais[$i]->pengurangan != null && $dinilais[$i]->status_banding == null) {
                $dinilais[$i]->total_sebelum_pengurangan = $total;
                $dinilais[$i]->total_sebelum_banding = "belum_diajukan";
                $dinilais[$i]->total = $total - ($total * $dinilais[$i]->pengurangan / 100);
                $dinilais[$i]->capaian = $total - ($total * $dinilais[$i]->pengurangan / 100) . "%";
            } elseif ($dinilais[$i]->pengurangan != null && $dinilais[$i]->status_banding == 'proses') {
                $dinilais[$i]->total_sebelum_pengurangan = $total;
                $dinilais[$i]->total_sebelum_banding = $total - ($total * $dinilais[$i]->pengurangan / 100);
                $dinilais[$i]->total = $total - ($total * $dinilais[$i]->pengurangan / 100);
                $dinilais[$i]->capaian = $total - ($total * $dinilais[$i]->pengurangan / 100) . "%";
            } elseif ($dinilais[$i]->pengurangan != null && $dinilais[$i]->status_banding == 'diterima_mv') {
                $dinilais[$i]->total_sebelum_pengurangan =  $total_histori;
                $dinilais[$i]->total_sebelum_banding =  $total_histori - ($total_histori * $dinilais[$i]->pengurangan / 100);
                $dinilais[$i]->total = $total - ($total * $dinilais[$i]->pengurangan / 100);
                $dinilais[$i]->capaian = $total - ($total * $dinilais[$i]->pengurangan / 100) . "%";
            }

            // if($dinilais[$i]->pengurangan == null && $dinilais[$i]->status_banding == 'proses')
            // {
            //     $dinilais[$i]->total_sebelum_pengurangan = $total;
            //     $dinilais[$i]->total = $total;
            //     $dinilais[$i]->capaian = $total . "%";
            // }elseif($dinilais[$i]->pengurangan != null && $dinilais[$i]->status_banding == 'proses')
            // {
            //     $dinilais[$i]->total_sebelum_pengurangan = $total;
            //     $dinilais[$i]->total = $total;
            //     $dinilais[$i]->capaian = $total . "%";
            // }

            // if ($dinilais[$i]->pengurangan) {
            //     $dinilais[$i]->total_awal = $total;
            //     $dinilais[$i]->total = $total - ($total * 10 / 100);
            //     $dinilais[$i]->capaian = $total - ($total * 10 / 100) . " %";
            // } else {
            //     $dinilais[$i]->total_awal = "no";
            //     $dinilais[$i]->total = $total;
            //     $dinilais[$i]->capaian = $total . " %";
            // }
        }
        return view('pegawai.penilai.banding_penilaian', compact('dinilais', 'hash'));
    }

    public function edit_kpi_performance($id)
    {
        $hash = new Hashids();
        $id_penilaian = $hash->decode($id);
        $penilaian = Penilaian::find($id_penilaian);
        $id_pegawai = $penilaian[0]->id_pegawai;
        $pegawai = User::find($id_pegawai);
        $penilaianperformance = PenilaianPerformance::where('id_penilaian', $id_penilaian[0])
            ->join('kpi_performances', 'kpi_performances.id_performance', '=', 'penilaian_performances.id_performance')
            ->get();
        return view('pegawai.penilai.edit_penilaian_kpi_performance_banding', compact('penilaianperformance', 'hash', 'pegawai', 'id'));
    }

    public function update_kpi_performance_setuju_pengajuan(Request $request, $id)
    {
        $hash = new Hashids();
        $id_penilaian = $hash->decode($id);
        $penilaianperformances = PenilaianPerformance::where('id_penilaian', $id_penilaian[0])->get();
        $penilaianperformance2 = $penilaianperformances;
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
        foreach ($penilaianperformance2 as $penilaianperformance) {
            HistoriPenilaianPerformance::create([
                'id_penilaian' => $penilaianperformance->id_penilaian,
                'id_performance' => $penilaianperformance->id_performance,
                'realisasi' => $penilaianperformance->realisasi,
            ]);
        }
        Banding::where('id_penilaian', $id_penilaian[0])->update([
            'status_banding' => 'diterima_mv'
        ]);
        return redirect('banding-penilaian')->with('success', 'Sukses setujui banding penilaian dan ubah penilaian kpi performance');
    }

    public function lihat_catatan($id)
    {
        $hash = new Hashids();
        $id_penilaian = $hash->decode($id);
        $penilaian = Penilaian::find($id_penilaian);
        $id_pegawai = $penilaian[0]->id_pegawai;
        $pegawai = User::find($id_pegawai);
        return view('pegawai.penilai.lihat_catatan', compact('penilaian', 'hash', 'pegawai', 'id'));
    }

    public function tolak_pengajuan(Request $request,$id)
    {
        request()->validate(
            [
                'alasan_tolak' => 'required|max:5000|min:4',
            ]
        );
        $hash = new Hashids();
        $id_penilaian = $hash->decode($id);
        try {
            Banding::where('id_penilaian', $id_penilaian[0])->update([
                'status_banding' => 'ditolak',
                'alasan_tolak' => $request->alasan_tolak
            ]);
            Penilaian::where('id_penilaian', $id_penilaian[0])->update([
                'status_penilaian' => 'selesai'
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Gagal tolak pengajuan');
        }
        return redirect('selesai')->with('success', 'Sukses tolak banding penilaian');
    }

    public function review_pengajuan($id)
    {
        $hash = new Hashids();
        $id_penilaian = $hash->decode($id);
        $penilaian = Penilaian::find($id_penilaian);
        $id_pegawai = $penilaian[0]->id_pegawai;
        $pegawai = User::find($id_pegawai);
        $banding = Banding::where('id_penilaian', $id_penilaian[0])->get();
        return view('pegawai.penilai.review_pengajuan', compact('banding', 'hash', 'pegawai', 'id'));
    }
}
