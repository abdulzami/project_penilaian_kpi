<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Jadwal;
use Carbon\Carbon;
use DateInterval;
use DatePeriod;
use DateTime;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    function getDatesFromRange($start, $end, $format = 'Y-m-d')
    {
        $array = array();
        $interval = new DateInterval('P1D');

        $realEnd = new DateTime($end);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($start), $interval, $realEnd);

        foreach ($period as $date) {
            $array[] = $date->format($format);
        }

        return $array;
    }
    public function index()
    {
        $hash = new Hashids();
        $jadwals = Jadwal::get();
        return view('admin.data_jadwal', compact('jadwals', 'hash'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.tambah_jadwal');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(
            [
                'nama_periode' => 'required|max:100|min:4',
                'tanggal_mulai' => 'required|date',
                'tanggal_akhir' => 'required|date',
            ]
        );
        if ($request->tanggal_akhir > $request->tanggal_mulai) {

            $tanggal_mulai = $request->tanggal_mulai;
            $tanggal_akhir = $request->tanggal_akhir;

            $a = array();
            $b = array();
            $c = array();

            $tanggal2 = $this->getDatesFromRange($tanggal_mulai, $tanggal_akhir);
            $lihat_bobot = Jabatan::leftJoin('kpi_performances', 'jabatans.id_jabatan', '=', 'kpi_performances.id_jabatan')
                ->select('jabatans.id_jabatan', DB::raw('SUM(kpi_performances.bobot) as total_bobot'))
                ->where('jabatans.id_penilai', '!=', null)
                ->groupBy('jabatans.id_jabatan')
                ->get();

            $lihat_user = Jabatan::leftJoin('users', 'jabatans.id_jabatan', '=', 'users.id_jabatan')
                ->select('jabatans.id_jabatan', DB::raw('COUNT(users.id_jabatan) as total_user'))
                ->groupBy('jabatans.id_jabatan')
                ->get();


            foreach ($tanggal2 as $tanggal) {
                $jadwal = Jadwal::whereRaw('? between tanggal_mulai and tanggal_akhir', $tanggal)->get();
                if ($jadwal->isEmpty()) {
                    array_push($a, "tidak");
                } else {
                    array_push($a, "ya");
                }
            }

            foreach ($lihat_bobot as $lb) {
                if ($lb->total_bobot == 100) {
                    array_push($b, "ya");
                } else {
                    array_push($b, "tidak");
                }
            }

            foreach ($lihat_user as $lu) {
                if ($lu->total_user > 0) {
                    array_push($c, "ya");
                } else {
                    array_push($c, "tidak");
                }
            }

            if (in_array("ya", $a)) {
                return back()->with('gagal', 'Jadwal sudah ada');
            } else {
                if (in_array("tidak", $b)) {
                    return back()->with('gagal', 'Total bobot jabatan ada yang belum 100 ');
                } else {
                    if (in_array("tidak", $c)) {
                        return back()->with('gagal', 'Suatu jabatan ada yang belum mempunyai pegawai');
                    } else {
                        try {
                            Jadwal::create([
                                'nama_periode' => $request->nama_periode,
                                'tanggal_mulai' => $request->tanggal_mulai,
                                'tanggal_akhir' => $request->tanggal_akhir,
                            ]);
                        } catch (\Illuminate\Database\QueryException $ex) {
                            return back()->with('gagal', 'Gagal menambahkan jadwal');
                        }
                        return back()->with('success', 'Sukses menambahkan jadwal');
                    }
                }
            }
        } else {
            return back()->with('gagal', 'tanggal mulai harus lebih kecil dari tanggal akhir');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $hash = new Hashids();
        $id_jadwal = $hash->decode($id);
        $jadwals = Jadwal::find($id_jadwal);
        if ($jadwals->isEmpty()) {
            abort(404);
        } else {
            return view('admin.edit_jadwal', compact('jadwals', 'id'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $hash = new Hashids();
        $id_jadwal = $hash->decode($id);
        $jadwals = Jadwal::find($id_jadwal);
        if ($jadwals->isEmpty()) {
            abort(404);
        } else {
            if ($request->tanggal_akhir > $request->tanggal_mulai) {
                request()->validate(
                    [
                        'nama_periode' => 'required|max:100|min:4',
                        'tanggal_mulai' => 'required|date',
                        'tanggal_akhir' => 'required|date',
                    ]
                );
                $tanggal_mulai = $request->tanggal_mulai;
                $tanggal_akhir = $request->tanggal_akhir;

                $a = array();

                $tanggal2 = $this->getDatesFromRange($tanggal_mulai, $tanggal_akhir);
                foreach ($tanggal2 as $tanggal) {
                    $jadwal = Jadwal::whereRaw('? between tanggal_mulai and tanggal_akhir', $tanggal)->where('id_jadwal', '!=', $id_jadwal)->get();
                    if ($jadwal->isEmpty()) {
                        array_push($a, "tidak");
                    } else {
                        array_push($a, "ya");
                    }
                }

                if (in_array("ya", $a)) {
                    return back()->with('gagal', 'Jadwal sudah ada');
                } else {
                    try {
                        Jadwal::where('id_jadwal', $id_jadwal)->update([
                            'nama_periode' => $request->nama_periode,
                            'tanggal_mulai' => $request->tanggal_mulai,
                            'tanggal_akhir' => $request->tanggal_akhir,
                        ]);
                    } catch (\Illuminate\Database\QueryException $ex) {
                        return back()->with('gagal', 'Gagal mengubah jadwal');
                    }
                    return back()->with('success', 'Sukses mengubah jadwal');
                }
            } else {
                return back()->with('gagal', 'tanggal mulai harus lebih kecil dari tanggal akhir');
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $hash = new Hashids();
        $id_jadwal = $hash->decode($id);
        $jadwals = Jadwal::find($id_jadwal);
        if ($jadwals->isEmpty()) {
            abort(404);
        } else {
            try {
                Jadwal::where('id_jadwal', $id_jadwal)->first()->delete();
            } catch (\Illuminate\Database\QueryException $ex) {
                return back()->with('gagal', 'Gagal menghapus jadwal');
            }
            return back()->with('success', 'Sukses menghapus jadwal');
        }
    }
}
