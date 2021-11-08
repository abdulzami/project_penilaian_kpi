<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KpiPerilaku extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_perilaku';

    protected $fillable = [
        'nama_kpi',
        'ekselen',
        'baik',
        'cukup',
        'kurang',
        'kurang_sekali'
    ];
}
