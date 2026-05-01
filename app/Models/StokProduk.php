<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; 

class StokProduk extends Model
{
    use SoftDeletes; 

    protected $table = 'stok_produk';
    
    public $timestamps = false; 

    protected $fillable = ['produk_id', 'jumlah'];

    public function produk()
    {
        return $this->belongsTo(Produk::class)->withTrashed();
    }
}