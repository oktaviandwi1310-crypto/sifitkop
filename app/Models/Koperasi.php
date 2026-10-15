<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Koperasi extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $casts = [
        'data_usaha' => 'array',
        'susunan_pengurus' => 'array',
        'susunan_pengawas' => 'array',
    ];

    public function pesertas()
    {
        return $this->hasMany(Peserta::class);
    }
}
