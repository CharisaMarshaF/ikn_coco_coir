<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StokProduk extends Model
{
    protected $table = 'stok_produk';
    public $timestamps = false;

    protected $fillable = ['produk_id','jumlah'];

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }
}
