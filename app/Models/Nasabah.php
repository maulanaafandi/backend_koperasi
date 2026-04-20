<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Hash;

class Nasabah extends Authenticatable
{
    use HasApiTokens, Notifiable;
    protected $table = 'nasabah';
    public $timestamps = true;
    const CREATED_AT = 'waktu_dibuat';
    const UPDATED_AT = null; 

    protected $fillable = [
        'id_jenis_simpanan',
        'nama_lengkap',
        'foto_profil',
        'nomor_induk_kependudukan',
        'nama_ibu_kandung',
        'tanggal_lahir',
        'tempat_lahir',
        'status_perkawinan',
        'jenis_kelamin',
        'alamat_ktp',
        'RT',
        'RW',
        'jenis_pekerjaan',
        'gaji_pekerjaan',
        'status',
        'nomor_nasabah',
        'nomor_handphone',
        'email',
        'password',
        'waktu_dibuat',
        'dibuat_oleh',
        'waktu_diaktifkan',
        'diaktifkan_oleh',
        'waktu_dinonaktifkan',
        'dinonaktifkan_oleh',
        'nomor_rekening',
        'tipe',
        'saldo',
        'pin'
    ];

    protected $hidden = [
        'password',
    ];


    protected $casts = [
        'tanggal_lahir' => 'date',
        'gaji_pekerjaan' => 'decimal:2',
        'waktu_dibuat' => 'datetime',
        'waktu_diaktifkan' => 'datetime',
        'waktu_dinonaktifkan' => 'datetime',
    ];

    protected function setPinAttribute($value)
    {
        $this->attributes['pin'] = Hash::make($value);
    }

    public function jenisSimpanan()
    {
        return $this->belongsTo(JenisSimpanan::class, 'id_jenis_simpanan');
    }
}
