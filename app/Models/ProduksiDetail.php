<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProduksiDetail extends Model
{
    protected $table = 'produksi_detail';
    public $timestamps = false;

    protected $fillable = ['produksi_id','jenis','item_id','qty'];

    public function produksi()
    {
        return $this->belongsTo(Produksi::class);
    }

    public function bahan()
    {
        return $this->belongsTo(BahanBaku::class, 'item_id');
    }

    public function produk()
    {
        return $this->belongsTo(Produk::class, 'item_id');
    }
}