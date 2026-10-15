<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelfAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'peserta_id',
        'integritas_score',
        'reputasi_score',
        'kompetensi_score',
        'inovasi_score',
        'status_kelulusan',
        'jawaban_raw',
    ];

    protected $casts = [
        'jawaban_raw' => 'array',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }
}
