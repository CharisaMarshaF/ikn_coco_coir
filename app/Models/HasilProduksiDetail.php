<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilProduksiDetail extends Model
{
    protected $table = 'hasil_produksi_detail';
    protected $fillable = ['hasil_produksi_id', 'produk_id', 'qty'];

    public function produk() {
        return $this->belongsTo(Produk::class);
    }
}