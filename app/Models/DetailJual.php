<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailJual extends Model
{
    use HasFactory;

    protected $fillable = [
        'no_bon',
        'kode_barang',
        'harga',
        'jumlah',
    ];

    public function jual()
    {
        return $this->belongsTo(Jual::class, 'no_bon', 'no_bon');
    }

    public function barang() {
        return $this->belongsTo(Barang::class, 'kode_barang', 'kode_barang');
    }
}
