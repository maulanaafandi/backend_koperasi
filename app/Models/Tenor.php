<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenor extends Model
{
    protected $table = 'tenor';
    public $timestamps = true;
    const CREATED_AT = 'waktu_dibuat';
    const UPDATED_AT = 'waktu_diubah';
    protected $fillable = [
        'tipe',
        'lama_angsuran',
        'bunga',
        'bunga_keterlambatan',
        'waktu_dibuat',
        'dibuat_oleh',
        'waktu_diubah',
        'diubah_oleh'
        ];

    protected $casts = [
        'bunga' => 'decimal:2',
        'bunga_keterlambatan' => 'decimal:2',
        'tenor' => 'integer',
        'lama_angsuran' => 'integer',
    ];

    public function getLamaAngsuranLabelAttribute()
    {
        return $this->lama_angsuran . ' Bulan';
    }
}