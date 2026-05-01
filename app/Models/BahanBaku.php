<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BahanBaku extends Model
{
    use SoftDeletes; 

    protected $table = 'bahan_baku';
    protected $fillable = ['nama', 'satuan'];
    protected $dates = ['deleted_at']; 

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