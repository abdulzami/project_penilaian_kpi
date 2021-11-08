<?php

namespace App\Http\Controllers;

use App\Models\KpiPerilaku;
use Illuminate\Http\Request;

class KpiperilakuController extends Controller
{
    public function index()
    {
        $perilakus = KpiPerilaku::select('nama_kpi','ekselen','baik','cukup','kurang','kurang_sekali')->get();
        return view('admin.data_kpiperilaku',compact('perilakus'));
    }
}
