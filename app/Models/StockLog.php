<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLog extends Model
{
        public $timestamps = false; // Mematikan timestamps sesuai error sebelumnya
protected $casts = [
    'created_at' => 'datetime',
];
    protected $table = 'stock_logs';
    protected $fillable = [
        'item_id', 'item_type', 'jenis', 'jumlah', 
        'stok_sebelum', 'stok_sesudah', 'sumber', 
        'keterangan', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // MorphTo jika ingin lebih canggih, tapi kita pakai manual item_type dulu sesuai SQL Anda
}