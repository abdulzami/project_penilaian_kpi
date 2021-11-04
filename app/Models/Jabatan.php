<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jabatan extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_jabatan';

    protected $fillable = [
        'nama_jabatan',
        'id_bidang',
        'id_penilai'
    ];

    public function bidangs()
    {
        return $this->belongsTo(Bidang::class,'id_bidang','id_bidang');
    }

    public function kpiperformances()
    {
        return $this->hasMany(KpiPerformance::class,'id_jabatan','id_jabatan');
    }
}
