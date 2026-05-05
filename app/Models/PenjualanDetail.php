<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenjualanDetail extends Model
{
    protected $table = 'penjualan_detail';
    public $timestamps = false;

    protected $fillable = ['penjualan_id','produk_id','qty','harga','subtotal'];

    public function penjualan()
    {
        return $this->belongsTo(Penjualan::class);
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function getTotalReturn()
    {
        return \App\Models\ReturnDetail::whereHas('returnHeader', function($q) {
            $q->where('penjualan_id', $this->penjualan_id);
        })->where('produk_id', $this->produk_id)->sum('qty');
    }
}
