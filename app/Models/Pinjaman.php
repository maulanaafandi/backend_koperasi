<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Tenor;

class Pinjaman extends Model
{
    protected $table = 'pinjaman';

    public $timestamps = true;
    const CREATED_AT = 'waktu_dibuat';
    const UPDATED_AT = null;

    protected $fillable = [
        'id_nasabah',
        'id_transaksi',
        'id_tenor',
        'jumlah_pinjaman',
        'jaminan',
        'foto_jaminan',
        'nilai_jaminan',
        'status',
        'dibuat_oleh',
        'waktu_disetujui',
        'disetujui_oleh',
        'waktu_tidak_setujui',
        'tidak_setujui_oleh'
    ];

    protected $casts = [
        'jumlah_pinjaman' => 'decimal:2',
        'waktu_dibuat' => 'datetime',
        'waktu_disetujui' => 'datetime',
        'waktu_tidak_setujui' => 'datetime',
    ];

    public function nasabah() {
        return $this->belongsTo(Nasabah::class, 'id_nasabah');
    }

    public function tenor() {
        return $this->belongsTo(Tenor::class, 'id_tenor');
    }

    public function transaksi() {
        return $this->belongsTo(Transaksi::class, 'id_transaksi');
    }

    public function cicilan()
    {
        return $this->hasMany(CicilanPinjaman::class, 'id_pinjaman');
    }
}
