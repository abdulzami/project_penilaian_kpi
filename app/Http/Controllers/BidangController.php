<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Struktural;
use App\Models\Bidang;
use Hashids\Hashids;

class BidangController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index($id)
    {
        $hash = new Hashids();
        $id_struktural = $hash->decode($id);
        $strukturals = Struktural::find($id_struktural);
        $bidangs = Struktural::find($id_struktural[0])->bidangs;
        $nama_struktural = $strukturals[0]->nama_struktural;
        return view('admin.data_bidang', compact('bidangs', 'hash', 'id', 'nama_struktural'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($id)
    {
        $hash = new Hashids();
        $id_struktural = $hash->decode($id);
        $strukturals = Struktural::find($id_struktural);
        $nama_struktural = $strukturals[0]->nama_struktural;
        return view('admin.tambah_bidang', compact('nama_struktural', 'id'));
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
        $id_struktural = $hash->decode($id);
        $strukturals = Struktural::find($id_struktural);
        request()->validate(
            [
                'nama_bidang' => 'required|max:50|min:4',
            ]
        );
        try {
            $bidang = new Bidang();
            $bidang->nama_bidang = $request->nama_bidang;
            $strukturals[0]->bidangs()->save($bidang);
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Gagal menambahkan bidang');
        }
        return redirect()->route('bidang',$id)->with('success', 'Sukses menambahkan bidang');
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
        $id_struktural = $hash->decode($id);
        $id_bidang = $hash->decode($id2);
        $bidangs = Struktural::find($id_struktural[0])->bidangs()->find($id_bidang);
        $strukturals = Struktural::find($id_struktural);
        $nama_struktural = $strukturals[0]->nama_struktural;
        return view('admin.edit_bidang', compact('bidangs', 'nama_struktural', 'id', 'id2'));
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
        $id_struktural = $hash->decode($id);
        $id_bidang = $hash->decode($id2);
        $bidangs = Struktural::find($id_struktural[0])->bidangs()->find($id_bidang);
        request()->validate(
            [
                'nama_bidang' => 'required|max:20|min:4',
            ]
        );
        try {
            $bidangs->first()->update([
                'nama_bidang' => $request->nama_bidang
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Gagal mengubah bidang');
        }
        return redirect()->route('bidang',$id)->with('success', 'Sukses mengubah bidang');
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
        $id_struktural = $hash->decode($id);
        $id_bidang = $hash->decode($id2);
        $bidangs = Struktural::find($id_struktural[0])->bidangs()->find($id_bidang);
        try {
            $bidangs->first()->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Bidang ini tidak bisa dihapus karena sudah di pakai di tabel lainnya');
        }
        return back()->with('success', 'Sukses menghapus bidang');
    }
}
