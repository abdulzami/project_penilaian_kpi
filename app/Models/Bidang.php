<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_bidang';

    protected $fillable = [
        'nama_bidang',
        'id_struktural'
    ];


    public function strukturals()
    {
        return $this->belongsTo(Struktural::class,'id_struktural','id_struktural');
    }

    public function jabatans()
    {
        return $this->hasMany(Bidang::class,'id_bidang','id_bidang');
    }
}
