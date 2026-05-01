<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
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