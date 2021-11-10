<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Jabatan;
use Hashids\Hashids;
use Illuminate\Http\Request;

class PegawaiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $hash = new Hashids();
        $pegawais = User::where('level', '!=', 'admin')
            ->join('jabatans', 'jabatans.id_jabatan', '=', 'users.id_jabatan')
            ->join('bidangs', 'bidangs.id_bidang', '=', 'jabatans.id_bidang')
            ->join('strukturals', 'strukturals.id_struktural', '=', 'bidangs.id_struktural')
            ->get();
        return view('admin.data_pegawai', compact('pegawais', 'hash'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $hash = new Hashids();
        $jabatans = Jabatan::join('bidangs', 'bidangs.id_bidang', '=', 'jabatans.id_bidang')
            ->join('strukturals', 'strukturals.id_struktural', '=', 'bidangs.id_struktural')
            ->get();
        return view('admin.tambah_pegawai', compact('hash', 'jabatans'));
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
                'npk' => 'required|max:20|min:6|unique:users,npk',
                'nama' => 'required|max:100|min:6',
                'email' => 'required|max:100|min:6|unique:users,email',
                'jabatan' => 'required',
            ]
        );
        $hash = new Hashids();
        try {
            User::create([
                'npk' => $request->npk,
                'nama' => $request->nama,
                'email' => $request->email,
                'password' => bcrypt('pegawaibarata'),
                'id_jabatan' => $hash->decode($request->jabatan)[0],
                'level' => 'pegawai',
                'status_user' => 'aktif'
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Gagal menambahkan pegawai');
        }
        return back()->with('success', 'Sukses menambahkan pegawai');
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
        $id_pegawai = $hash->decode($id);
        $pegawais = User::find($id_pegawai);
        $jabatans = Jabatan::join('bidangs', 'bidangs.id_bidang', '=', 'jabatans.id_bidang')
            ->join('strukturals', 'strukturals.id_struktural', '=', 'bidangs.id_struktural')
            ->get();
        return view('admin.edit_pegawai', compact('pegawais', 'id', 'jabatans', 'hash'));
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
        $id_pegawai = $hash->decode($id);
        request()->validate(
            [
                'npk' => 'required|max:20|min:6|unique:users,npk,' . $hash->decode($id)[0] . ',id_user',
                'nama' => 'required|max:100|min:6',
                'email' => 'required|max:100|min:6|unique:users,email,' . $hash->decode($id)[0] . ',id_user',
                'jabatan' => 'required',
            ]
        );

        try {
            User::where('id_user', $id_pegawai)->update([
                'npk' => $request->npk,
                'nama' => $request->nama,
                'email' => $request->email,
                'id_jabatan' => $hash->decode($request->jabatan)[0],
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Gagal mengubah pegawai');
        }
        return back()->with('success', 'Sukses mengubah pgawai');
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
        $id_pegawai = $hash->decode($id);
        try {
            User::where('id_user', $id_pegawai)->first()->delete();
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Gagal menghapus pegawai');
        }
        return back()->with('success', 'Sukses menghapus pegawai');
    }

    public function resetpassword($id)
    {
        $hash = new Hashids();
        $id_pegawai = $hash->decode($id);
        try {
            User::where('id_user', $id_pegawai)->update([
                'password' => bcrypt('pegawaibarata'),
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Gagal reset password pegawai');
        }
        return back()->with('success', 'Sukses reset password pegawai');
    }
}
