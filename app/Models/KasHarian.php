<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KasHarian extends Model
{
    protected $table = 'kas_harian';

    protected $casts = [
        'tanggal' => 'date',
    ];

    protected $fillable = [
        'rekening_id',
        'tanggal',
        'jenis',
        'nominal',
        'keterangan'
    ];

    public function rekening()
    {
        return $this->belongsTo(Rekening::class);
    }
}