<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class Produk extends Model
{
    use SoftDeletes;

    protected $table = 'produk';
    
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'nama',
        'satuan',
        'harga_default',
        'jenis',
    ];

    public function stok()
    {
        return $this->hasOne(StokProduk::class)->withTrashed();
    }

    public function penjualanDetail()
    {
        return $this->hasMany(PenjualanDetail::class);
    }
}