<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class Admin extends Authenticatable
{
    use HasApiTokens;

    public $timestamps = false;
    protected $table = 'admin'; 
    protected $fillable = [
    'nomor_admin',
    'password'
    ];
    protected $hidden = ['password'];
}