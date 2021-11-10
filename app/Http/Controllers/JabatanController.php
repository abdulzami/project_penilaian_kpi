<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\Jabatan;
use Hashids\Hashids;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JabatanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hash = new Hashids();
        // $jabatans = DB::table('jabatans as b')->leftJoin('jabatans as a', 'a.id_jabatan', '=', 'b.id_penilai')
        //     ->leftJoin('bidangs as ba', 'ba.id_bidang', '=', 'b.id_bidang')
        //     ->leftJoin('strukturals as sa', 'sa.id_struktural', '=', 'ba.id_struktural')
        //     ->leftJoin('bidangs as bb', 'bb.id_bidang', '=', 'a.id_bidang')
        //     ->leftJoin('strukturals as sb', 'sb.id_struktural', '=', 'bb.id_struktural')
        //     ->select(
        //         'b.id_jabatan',
        //         'b.nama_jabatan as nama_jabatan',
        //         'a.nama_jabatan as nama_penilai',
        //         'ba.nama_bidang',
        //         'sa.nama_struktural',
        //         'bb.nama_bidang as nama_bidang_penilai',
        //         'sb.nama_struktural as nama_struktural_penilai'
        //     )
        //     ->get();
        $jabatans = Jabatan::select(
            'jabatans.id_jabatan',
            'jabatans.nama_jabatan',
            'bidangs.nama_bidang',
            'strukturals.nama_struktural',
            'jabatans.id_penilai',
            DB::raw('SUM(kpi_performances.bobot) as total_bobot_jabatan')
        )
            ->join('bidangs', 'bidangs.id_bidang', '=', 'jabatans.id_bidang')
            ->join('strukturals', 'strukturals.id_struktural', '=', 'bidangs.id_struktural')
            ->leftJoin('kpi_performances', 'jabatans.id_jabatan', '=', 'kpi_performances.id_jabatan')
            ->groupBy('jabatans.id_jabatan', 'jabatans.nama_jabatan', 'bidangs.nama_bidang', 'strukturals.nama_struktural', 'jabatans.id_penilai')
            ->get();
        // $penilais = DB::table('jabatans as a')->leftJoin('jabatans as b','a.id_jabatan','=','b.id_penilai')
        // ->leftJoin('bidangs as c','c.id_bidang','=','b.id_bidang')
        // ->leftJoin('strukturals as d','d.id_struktural','=','c.id_struktural')
        // ->get();
        return view('admin.data_jabatan', compact('jabatans', 'hash'));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hash = new Hashids();
        $bidangs = Bidang::join('strukturals', 'strukturals.id_struktural', '=', 'bidangs.id_struktural')->get();
        $jabatans = Jabatan::join('bidangs', 'bidangs.id_bidang', '=', 'jabatans.id_bidang')
            ->join('strukturals', 'strukturals.id_struktural', '=', 'bidangs.id_struktural')
            ->get();

        return view('admin.tambah_jabatan', compact('bidangs', 'hash', 'jabatans'));
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
                'struktural' => 'required',
                'nama_jabatan' => 'required|max:20|min:4',
            ]
        );
        $hash = new Hashids();
        $id_bidang = $hash->decode($request->struktural);

        if ($request->penilai) {
            $id_penilai = $hash->decode($request->penilai)[0];
        } else {
            $id_penilai = null;
        }
        try {
            Jabatan::create([
                'id_bidang' => $id_bidang[0],
                'nama_jabatan' => $request->nama_jabatan,
                'id_penilai' => $id_penilai,
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Gagal menambahkan jabatan');
        }
        return back()->with('success', 'Sukses menambahkan jabatan');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
        $id_jabatan = $hash->decode($id);
        $jabatans = Jabatan::find($id_jabatan[0]);
        $bidangs = Bidang::join('strukturals', 'strukturals.id_struktural', '=', 'bidangs.id_struktural')->get();
        $jabatansall = Jabatan::join('bidangs', 'bidangs.id_bidang', '=', 'jabatans.id_bidang')
            ->join('strukturals', 'strukturals.id_struktural', '=', 'bidangs.id_struktural')
            ->get();
        return view('admin.edit_jabatan', compact('bidangs', 'jabatans', 'jabatansall', 'hash', 'id'));
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
        $id_jabatan = $hash->decode($id);
        request()->validate(
            [
                'nama_jabatan' => 'required|max:50|min:4',
                'struktural' => 'required'
            ]
        );
        if ($request->penilai) {
            $id_penilai = $hash->decode($request->penilai)[0];
            if ($id_penilai == $id_jabatan[0]) {
                return back()->with('gagal', 'Gagal jabatan dan penilai tidak boleh sama');
            }
        } else {
            $id_penilai = null;
        }
        try {
            Jabatan::where('id_jabatan', $id_jabatan)->update([
                'nama_jabatan' => $request->nama_jabatan,
                'id_bidang' => $hash->decode($request->struktural)[0],
                'id_penilai' => $id_penilai,
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Gagal mengubah jabatan');
        }
        return back()->with('success', 'Sukses mengubah jabatan');
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
        $id_jabatan = $hash->decode($id);
        try {
            Jabatan::where('id_jabatan', $id_jabatan)->first()->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Gagal menghapus jabatan');
        }
        return back()->with('success', 'Sukses menghapus jabatan');
    }

    public function hirarki($id)
    {
        $hash = new Hashids();
        $id_jabatan = $hash->decode($id);
        $jabatan = Jabatan::where('id_jabatan', $id_jabatan)
            ->join('bidangs', 'bidangs.id_bidang', '=', 'jabatans.id_bidang')
            ->join('strukturals', 'strukturals.id_struktural', '=', 'bidangs.id_struktural')
            ->first();
        $penilai = Jabatan::where('id_jabatan', $jabatan->id_penilai)
            ->join('bidangs', 'bidangs.id_bidang', '=', 'jabatans.id_bidang')
            ->join('strukturals', 'strukturals.id_struktural', '=', 'bidangs.id_struktural')
            ->first();
        if ($penilai != null) {
            $atasan_penilai = Jabatan::where('id_jabatan', $penilai->id_penilai)
                ->join('bidangs', 'bidangs.id_bidang', '=', 'jabatans.id_bidang')
                ->join('strukturals', 'strukturals.id_struktural', '=', 'bidangs.id_struktural')
                ->first();
            if ($atasan_penilai == null) {
                $atasan_penilai = $penilai;
            }
        } else {
            $atasan_penilai = null;
        }


        return view('admin.hirarki_jabatan', compact('jabatan', 'penilai', 'atasan_penilai'));
    }
}
