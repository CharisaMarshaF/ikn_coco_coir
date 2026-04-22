<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Produksi extends Model
{
    protected $table = 'produksi';
    protected $fillable = ['tanggal','status','keterangan'];

    public function detail()
    {
        return $this->hasMany(ProduksiDetail::class);
    }
}