<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfilController extends Controller
{
    public function index()
    {


        if (Auth::user()->level = 'admin') {
            return view('profil');
        } else {
            $all = Jabatan::select('nama_jabatan', 'nama_struktural', 'nama_bidang')->join('bidangs', 'bidangs.id_bidang', '=', 'jabatans.id_bidang')
                ->join('strukturals', 'strukturals.id_struktural', '=', 'bidangs.id_struktural')->where('jabatans.id_jabatan', Auth::user()->id_jabatan)->get();
            $all = $all[0];
            $jabatan = $all->nama_jabatan . " " . $all->nama_struktural . " " . $all->nama_bidang;
            return view('profil', compact('jabatan'));
        }
       
    }

    public function ganti_password(Request $request)
    {
        $id_user = Auth::user()->id_user;
        $password = Auth::user()->password;
        request()->validate(
            [
                'ulangi_password_baru' => 'required|max:50|min:8',
                'password_baru' => 'required|max:50|min:8',
                'password_lama' => 'required|max:50|min:8',
            ]
        );
        if (Hash::check($request->password_lama, $password)) {
            if ($request->ulangi_password_baru == $request->password_baru) {
                try {
                    User::where('id_user', $id_user)->update([
                        'password' => bcrypt($request->password_baru),
                    ]);
                } catch (\Illuminate\Database\QueryException $ex) {
                    return back()->with('gagal', 'Gagal mengubah password');
                }
                return back()->with('success', 'Sukses mengubah password');
            } else {
                return back()->with('gagal', 'Password baru dan ulangi password baru tidak sama');
            }
        } else {
            return back()->with('gagal', 'Gagal mengubah password');
        }
    }
}
