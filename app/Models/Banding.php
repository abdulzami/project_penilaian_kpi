<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banding extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_banding';
  
    protected $fillable = [
        'id_penilaian',
        'alasan',
        'bukti'
    ];
}
