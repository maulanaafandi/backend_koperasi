<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Anggota extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'anggota';
    protected $fillable = [
        'nama_lengkap', 
        'foto_profil', 
        'jenis_kelamin', 
        'alamat', 
        'pin', 
        'nomor_anggota', 
        'nomor_handphone', 
        'email', 
        'password', 
        'status_akun'
    ];
    protected $hidden = ['password'];
}
