<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jual extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_bon',
        'total',
        'diskon',
        'bayar',
        'kembali',  
        'kode_kasir',
        'tanggal',
        'waktu',
    ];

    public function kasir()
    {
        return $this->belongsTo(Kasir::class, 'kode_kasir', 'kode_kasir');
    }

    public function detailJual()
    {
        return $this->hasMany(DetailJual::class, 'no_bon', 'no_bon');
    }
}
