<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Struktural;
use Hashids\Hashids;

class StrukturalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hash = new Hashids();
        $strukturals = Struktural::get();   
        return view('admin.data_struktural', compact('strukturals', 'hash'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.tambah_struktural');
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
                'nama_struktural' => 'required|max:20|min:4',
            ]
        );

        try {
            Struktural::create([
                'nama_struktural' => $request->nama_struktural,
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Gagal menambahkan struktural');
        }
        return back()->with('success', 'Sukses menambahkan struktural');
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
        $id_struktural = $hash->decode($id);
        $strukturals = Struktural::find($id_struktural);
        if ($strukturals->isEmpty()) {
            abort(404);
        } else {
            return view('admin.edit_struktural', compact('strukturals', 'id'));
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
        $id_struktural = $hash->decode($id);
        $strukturals = Struktural::find($id_struktural);
        if ($strukturals->isEmpty()) {
            abort(404);
        } else {
            request()->validate(
                [
                    'nama_struktural' => 'required|max:20|min:4',
                ]
            );

            try {
                Struktural::where('id_struktural',$id_struktural)->update([
                    'nama_struktural' => $request->nama_struktural
                ]);
            } catch (\Illuminate\Database\QueryException $ex) {
                return back()->with('gagal', 'Gagal mengubah struktural');
            }
            return back()->with('success', 'Sukses mengubah struktural');
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
        $id_struktural = $hash->decode($id);
        $strukturals = Struktural::find($id_struktural);
        if ($strukturals->isEmpty()) {
            abort(404);
        } else {
            try {
                Struktural::where('id_struktural',$id_struktural)->first()->delete();
            } catch (\Illuminate\Database\QueryException $ex) {
                return back()->with('gagal', 'Gagal menghapus struktural');
            }
            return back()->with('success', 'Sukses menghapus struktural');
        }
    }
}
