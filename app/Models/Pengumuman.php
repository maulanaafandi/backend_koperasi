<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Pengumuman extends Model
{
    protected $table = 'pengumuman';
    public $timestamps = true;
    const CREATED_AT = 'waktu_dibuat';
    const UPDATED_AT = 'waktu_diubah';
    protected $fillable = [
        'judul',
        'foto',
        'deskripsi',
        'waktu_dibuat',
        'dibuat_oleh',
        'waktu_diubah',
        'diubah_oleh',
    ];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->waktu_diubah = null;
        });
    }
    
public function setFotoAttribute($value)
{
    if ($value instanceof UploadedFile) {

        $extension = $value->getClientOriginalExtension();

        $namaFoto = Str::random(40) . '.' . $extension;

        $value->storeAs(
            'pengumuman',
            $namaFoto,
            'public'
        );

        $this->attributes['foto'] = $namaFoto;
    }
}
}
