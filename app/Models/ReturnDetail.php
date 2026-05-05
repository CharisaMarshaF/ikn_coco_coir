<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnDetail extends Model
{
    use HasFactory;

    protected $table = 'return_details';

    protected $fillable = [
        'return_id',
        'produk_id',
        'qty',
        'harga',
        'subtotal',
    ];

    // Relasi balik ke Header Return
    public function returnHeader()
    {
        return $this->belongsTo(ReturnPenjualan::class, 'return_id');
    }

    // Relasi ke Produk (Untuk ambil nama produk, dll)
    public function produk()
    {
        return $this->belongsTo(Produk::class, 'produk_id')->withTrashed();
    }
    
}