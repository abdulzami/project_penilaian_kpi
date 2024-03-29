<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\KpiPerformance;
use Illuminate\Http\Request;
use Hashids\Hashids;

class KpiperformanceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $hash = new Hashids();
        $id_jabatan = $hash->decode($id);
        $jabatans = Jabatan::join('bidangs', 'jabatans.id_bidang', '=', 'bidangs.id_bidang')
            ->join('strukturals', 'strukturals.id_struktural', '=', 'bidangs.id_struktural')
            ->find($id_jabatan);
        $nama_jabatan = $jabatans[0]->nama_jabatan . " " . $jabatans[0]->nama_struktural . " " . $jabatans[0]->nama_bidang;
        $kpiperformances = Jabatan::find($id_jabatan[0])->kpiperformances;
        $total_bobot = $kpiperformances->sum('bobot');
        return view('admin.data_kpiperformance', compact('kpiperformances', 'hash', 'id', 'nama_jabatan', 'total_bobot'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $hash = new Hashids();
        $id_jabatan = $hash->decode($id);
        $jabatans = Jabatan::join('bidangs', 'jabatans.id_bidang', '=', 'bidangs.id_bidang')
            ->join('strukturals', 'strukturals.id_struktural', '=', 'bidangs.id_struktural')
            ->find($id_jabatan);
        $nama_jabatan = $jabatans[0]->nama_jabatan . " " . $jabatans[0]->nama_struktural . " " . $jabatans[0]->nama_bidang;
        return view('admin.tambah_kpiperformance', compact('nama_jabatan', 'id'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, $id)
    {
        $hash = new Hashids();
        $id_jabatan = $hash->decode($id);
        $jabatans = Jabatan::join('bidangs', 'jabatans.id_bidang', '=', 'bidangs.id_bidang')
            ->join('strukturals', 'strukturals.id_struktural', '=', 'bidangs.id_struktural')
            ->find($id_jabatan);
        request()->validate(
            [
                'kategori' => 'required|max:100|min:4',
                'indikator_kpi' => 'required|max:100|min:4',
                'definisi' => 'required|max:500|min:4',
                'satuan' => 'required|max:20|min:1',
                'target' => 'required|numeric|max:200|min:1',
                'bobot' => 'required|numeric|max:100|min:1',
                'tipe_perform' => 'required|max:100|min:3',
            ]
        );
        try {
            $kpiperformances = new KpiPerformance();
            $kpiperformances->kategori = $request->kategori;
            $kpiperformances->indikator_kpi = $request->indikator_kpi;
            $kpiperformances->definisi = $request->definisi;
            $kpiperformances->satuan = $request->satuan;
            $kpiperformances->target = $request->target;
            $kpiperformances->bobot = $request->bobot;
            $kpiperformances->tipe_performance = $request->tipe_perform;
            $jabatans[0]->kpiperformances()->save($kpiperformances);
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Gagal menambahkan kpi performance');
        }
        return redirect()->route('kpiperformance',$id)->with('success', 'Sukses menambahkan kpi performance');
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
    public function edit($id, $id2)
    {
        $hash = new Hashids();
        $id_jabatan = $hash->decode($id);
        $id_kpip = $hash->decode($id2);
        $jabatans = Jabatan::join('bidangs', 'jabatans.id_bidang', '=', 'bidangs.id_bidang')
            ->join('strukturals', 'strukturals.id_struktural', '=', 'bidangs.id_struktural')
            ->find($id_jabatan);
        $kpips = $jabatans[0]->kpiperformances()->find($id_kpip);
        $kpips = $kpips[0];
        $nama_jabatan = $jabatans[0]->nama_jabatan . " " . $jabatans[0]->nama_struktural . " " . $jabatans[0]->nama_bidang;
        return view('admin.edit_kpiperformance', compact('kpips', 'nama_jabatan', 'id', 'id2'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id, $id2)
    {
        $hash = new Hashids();
        $id_jabatan = $hash->decode($id);
        $id_kpip = $hash->decode($id2);
        $kpips = Jabatan::find($id_jabatan[0])->kpiperformances()->find($id_kpip);
        request()->validate(
            [
                'kategori' => 'required|max:100|min:4',
                'indikator_kpi' => 'required|max:100|min:4',
                'definisi' => 'required|max:500|min:4',
                'satuan' => 'required|max:20|min:1',
                'target' => 'required|numeric|max:200|min:1',
                'bobot' => 'required|numeric|max:100|min:1',
                'tipe_perform' => 'required|max:100|min:3',
            ]
        );
        if (str_contains($request->target, ',')) {
            $request->target = str_replace(',', '.', $request->target);
        }
        try {
            $kpips->first()->update([
                'kategori' => $request->kategori,
                'indikator_kpi' => $request->indikator_kpi,
                'definisi' => $request->definisi,
                'satuan' => $request->satuan,
                'target' => $request->target,
                'bobot' => $request->bobot,
                'tipe_performance' => $request->tipe_perform
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Gagal mengubah kpi performance');
        }
        return redirect()->route('kpiperformance',$id)->with('success', 'Sukses mengubah kpi performance');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, $id2)
    {
        $hash = new Hashids();
        $id_jabatan = $hash->decode($id);
        $id_kpip = $hash->decode($id2);
        $kpips = Jabatan::find($id_jabatan[0])->kpiperformances()->find($id_kpip);
        try {
            $kpips->first()->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Kpi Performance ini tidak bisa dihapus karena sudah di pakai di tabel lainnya');
        }
        return back()->with('success', 'Sukses menghapus kpi performance');
    }
}
