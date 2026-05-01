<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StokBahan extends Model
{
    use SoftDeletes; 

    protected $table = 'stok_bahan';
    public $timestamps = false; 

    protected $fillable = ['bahan_id', 'jumlah'];

    public function bahan()
    {
        return $this->belongsTo(BahanBaku::class, 'bahan_id')->withTrashed();
    }
}