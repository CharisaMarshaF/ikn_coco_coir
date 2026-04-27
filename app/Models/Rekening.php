<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rekening extends Model
{
    protected $table = 'rekening';

    protected $fillable = ['nama','jenis','saldo'];

    public function kasHarian()
    {
        return $this->hasMany(KasHarian::class);
    }
}