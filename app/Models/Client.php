<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory; // 1. Tambahkan ini

class Client extends Model
{
        use HasFactory;

    use SoftDeletes; 

    protected $table = 'clients';
    protected $dates = ['deleted_at'];

    protected $fillable = [
        'nama',
        'telp',
        'alamat',
        'catatan'
    ];

    public function penjualan()
    {
        return $this->hasMany(Penjualan::class);
    }
}