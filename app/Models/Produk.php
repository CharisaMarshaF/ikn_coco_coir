<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    protected $table = 'produk';
    protected $fillable = ['nama','satuan','harga_default','jenis',];

    public function stok()
    {
        return $this->hasOne(StokProduk::class);
    }

    public function penjualanDetail()
    {
        return $this->hasMany(PenjualanDetail::class);
    }
}