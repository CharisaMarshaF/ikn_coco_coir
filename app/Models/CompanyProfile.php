<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    protected $table = 'company_profile';
    public $timestamps = false; // Mematikan timestamps sesuai error sebelumnya

    // Beritahu Laravel bahwa ID bukan auto-increment
    public $incrementing = false;
    protected $keyType = 'int';

    protected $fillable = [
        'id', // Izinkan ID diisi manual
        'nama_cv', 
        'logo', 
        'email', 
        'alamat', 
        'telepon'
    ];
}