<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KasDetail extends Model
{
    protected $table = 'kas_detail';

    protected $fillable = [
        'kas_harian_id',
        'nama_item',
        'jumlah',
        'harga',
        'subtotal'
    ];

    // Relasi balik ke header
    public function kasHarian()
    {
        return $this->belongsTo(KasHarian::class, 'kas_harian_id');
    }
}