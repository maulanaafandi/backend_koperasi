<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisSimpanan extends Model
{
    protected $table = 'jenis_simpanan';
    public $timestamps = false;
    const CREATED_AT = 'waktu_dibuat';
    const UPDATED_AT = 'waktu_diubah';
    protected $fillable = [
        'nama_simpanan',
        'saldo_minimal',
        'dibuat_oleh',
        'diubah_oleh',
        ];

    protected $casts = [
        'saldo_minimal' => 'decimal:2',
        'waktu_dibuat' => 'datetime',
        'waktu_diubah' => 'datetime',
    ];

    public function nasabah()
    {
        return $this->hasMany(Nasabah::class, 'id_jenis_simpanan');
    }
}
