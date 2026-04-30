<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HasilProduksiDetail extends Model
{
    protected $table = 'hasil_produksi_detail';
    protected $fillable = ['hasil_produksi_id', 'produk_id', 'qty','kategori_pola'];

    public function produk() {
        return $this->belongsTo(Produk::class);
    }
}