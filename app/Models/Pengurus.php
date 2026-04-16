<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;

class Pengurus extends Authenticatable
{
    use HasApiTokens;

    protected $table = 'pengurus';
    public $timestamps = true;
    const CREATED_AT = 'waktu_dibuat';
    const UPDATED_AT = null;

    protected $fillable = [
        'nama_lengkap', 
        'foto_profil',
        'jenis_kelamin', 
        'nomor_pengurus', 
        'nomor_handphone',
        'password',
        'status_akun',
        'waktu_diaktifkan',
        'diaktifkan_oleh',
        'waktu_dinonaktifkan',
        'dinonaktifkan_oleh'
    ];
    protected $hidden = ['password'];
}