<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenilaianPerilaku extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_penilaian_perilaku';

    protected $fillable = [
        'id_penilaian',
        'id_perilaku',
        'nilai_perilaku'
    ];
}
