<?php

namespace App\Http\Controllers;

use App\Models\Banding;
use Hashids\Hashids;
use Illuminate\Http\Request;

class DinilaiController extends Controller
{
    public function create_pengajuan_banding($id)
    {
        return view('pegawai.dinilai.ajukan_pengajuan_banding',compact('id'));
    }

    public function store_pengajuan_banding(Request $request,$id)
    {
        $hash = new Hashids();
        request()->validate(
            [
                'tulisan_komplain' => 'required|max:5000|min:4',
                'bukti_pendukung' => 'required|mimetypes:application/pdf|file|max:1000',
                'agreement' => 'required'
            ]
        );

        if($request->agreement)
        {
            $agree = 'ya';
        }

        try {
            Banding::create([
                'id_penilaian' => $hash->decode($id)[0],
                'alasan' => $request->tulisan_komplain,
                'bukti' => $request->file('bukti_pendukung')->store('buktipendukung-pdf'),
                'agreement' => $agree,
                'status_banding' => 'proses'
            ]);
        } catch (\Illuminate\Database\QueryException $ex) {
            return back()->with('gagal', 'Gagal mengajukan banding');
        }
        return redirect('dashboard')->with('success', 'Sukses mengajukan banding');
    }
}
