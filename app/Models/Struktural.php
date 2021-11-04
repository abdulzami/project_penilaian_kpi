<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Struktural extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_struktural';

    protected $fillable = [
        'nama_struktural',
    ];
    
    public function bidangs()
    {
        return $this->hasMany(Bidang::class,'id_struktural','id_struktural');
    }
}
