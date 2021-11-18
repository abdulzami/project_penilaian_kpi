<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoriPenilaianPerformance extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_histori_penilaian_performance';

    protected $fillable = [
        'id_penilaian',
        'id_performance',
        'realisasi'
    ];
}
