<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SelfAssessment extends Model
{
    use HasFactory;

    protected $fillable = [
        'peserta_id',
        'skor_mandiri_integritas',
        'skor_mandiri_reputasi',
        'skor_mandiri_kompetensi',
        'skor_mandiri_inovasi',
        'status_mandiri',
        'integritas_score',
        'reputasi_score',
        'kompetensi_score',
        'inovasi_score',
        'status_kelulusan',
        'jawaban_raw',
        'penguji_id',
        'skor_penguji_integritas',
        'skor_penguji_reputasi',
        'skor_penguji_kompetensi',
        'skor_penguji_inovasi',
        'status_penguji',
        'catatan_penguji',
        'penilaian_penguji_raw',
    ];

    protected $casts = [
        'jawaban_raw' => 'array',
        'penilaian_penguji_raw' => 'array',
    ];

    public function peserta()
    {
        return $this->belongsTo(Peserta::class);
    }

    public function penguji()
    {
        return $this->belongsTo(User::class, 'penguji_id');
    }
}
