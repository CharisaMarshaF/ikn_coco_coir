<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengambilanBahanDetail extends Model
{
    use HasFactory;

    protected $table = 'pengambilan_bahan_detail';

    protected $fillable = [
        'pengambilan_id',
        'bahan_id',
        'qty'
    ];

    /**
     * Relasi balik ke Master Pengambilan.
     */
    public function pengambilan()
    {
        return $this->belongsTo(PengambilanBahan::class, 'pengambilan_id');
    }

    /**
     * Relasi ke referensi Bahan Baku.
     */
    public function bahan()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_id');
    }
}