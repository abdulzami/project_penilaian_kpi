<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Penilaian extends Model
{
    use HasFactory;
    
    protected $primaryKey = 'id_penilaian';

    protected $fillable = [
        'id_pegawai',
        'id_jadwal',
        'status_penilaian',
        'catatan_penting',
        'pengurangan'
    ];

    public function penilaianperformances()
    {
        return $this->hasMany(PenilaianPerformance::class,'id_penilaian','id_penilaian');
    }

    public function penilaianperilakus()
    {
        return $this->hasMany(PenilaianPerilaku::class,'id_penilaian','id_penilaian');
    }
}
