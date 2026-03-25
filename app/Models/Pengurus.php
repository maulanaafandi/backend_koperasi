<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Pengurus extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'pengurus';

    public $timestamps = false;
    
    protected $fillable = [
        'nama_lengkap', 
        'foto_profil',
        'jenis_kelamin', 
        'nomor_pengurus', 
        'nomor_handphone',
        'password',
        'status_akun'
    ];
    protected $hidden = ['password'];
}