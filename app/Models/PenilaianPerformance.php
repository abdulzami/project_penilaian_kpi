<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianPerformance extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_penilaian_performance';

    protected $fillable = [
        'id_penilaian',
        'id_performance',
        'realisasi'
    ];
}
