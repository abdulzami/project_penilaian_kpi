<?php

namespace App\Http\Controllers;

use App\Models\Jadwal;
use App\Models\Penilai;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index()
    {
        
        if (!Auth::check()) {
            return redirect('/');
        }

        $sekarang = Carbon::now();
        $sekarang = $sekarang->toDateString();
        $jadwal = Jadwal::whereRaw('? between tanggal_mulai and tanggal_akhir', $sekarang)->get();
        if($jadwal->isEmpty())
        {
            $nama_periode = "";
        }else{
            $nama_periode = $jadwal[0]->nama_periode;
        }
        // $id = Auth::user()->id_user;
        
        if (Auth::user()->level == 'admin') {
            return view('admin.home',compact('nama_periode'));
        } 
        elseif(Auth::user()->level == 'pegawai')
        {
            return view('pegawai.home',compact('nama_periode'));
        }
        else {
            return redirect('/')->with('error', 'Anda tidak punya akses !');
        }
    }
}
