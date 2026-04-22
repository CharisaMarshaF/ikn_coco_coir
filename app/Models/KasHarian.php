<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KasHarian extends Model
{
    protected $casts = [
    'tanggal' => 'date',
];
    protected $table = 'kas_harian';
    protected $fillable = ['tanggal','jenis','nominal','keterangan'];
}
