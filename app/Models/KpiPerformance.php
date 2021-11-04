<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiPerformance extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_performance';

    protected $fillable = [
        'kategori',
        'id_jabatan',
        'indikator_kpi',
        'definisi',
        'satuan',
        'target',
        'bobot',
        'tipe_performance',
    ];

    public function jabatans()
    {
        return $this->belongsTo(Jabatan::class,'id_jabatan','id_jabatan');
    }
}
