<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // 1. Import trait

class Supplier extends Model
{
    use SoftDeletes; // 2. Gunakan trait

    protected $fillable = ['nama','telp','alamat','keterangan'];

    public function pembelian()
    {
        return $this->hasMany(Pembelian::class);
    }
}