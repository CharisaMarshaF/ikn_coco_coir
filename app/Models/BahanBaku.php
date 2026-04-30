<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BahanBaku extends Model
{
    protected $table = 'bahan_baku';
    protected $fillable = ['nama','satuan'];

    public function stok()
    {
        return $this->hasOne(StokBahan::class, 'bahan_id');
    }

    public function pembelianDetail()
    {
        return $this->hasMany(PembelianDetail::class, 'bahan_id');
    }

    public function pengambilanDetail()
    {
        return $this->hasMany(PengambilanBahanDetail::class, 'bahan_id');
    }
}
