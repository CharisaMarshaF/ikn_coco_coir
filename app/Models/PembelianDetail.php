<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembelianDetail extends Model
{
    protected $table = 'pembelian_detail';
    public $timestamps = false;

    protected $fillable = ['pembelian_id','bahan_id','qty','harga','subtotal'];

    public function pembelian()
    {
        return $this->belongsTo(Pembelian::class);
    }

    public function bahan()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_id');
    }
}