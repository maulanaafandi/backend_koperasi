<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisSimpanan extends Model
{
    protected $table = 'jenis_simpanan';
    protected $fillable = ['jenis_simpanan'];
    public $timestamps = false; 

    public function simpanan()
    {
        return $this->hasMany(Simpanan::class, 'id_jenis_simpanan');
    }
}
