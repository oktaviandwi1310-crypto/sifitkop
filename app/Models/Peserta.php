<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Peserta extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'koperasi_id',
        'nik_peserta',
        'nama_lengkap',
        'jabatan_usulan',
        'status_kelulusan',
        'status_verifikasi',
        'skor_wawancara',
        'dokumen_ktp',
        'dokumen_permohonan',
        'dokumen_cv',
        'dokumen_pendukung_peserta',
        'dokumen_pendukung_koperasi',
        'jadwal_wawancara',
        'dokumen_paparan',
        'penguji_id',
        'skor_admin',
        'wawancara_1',
        'wawancara_2',
        'wawancara_3',
        'wawancara_4',
        'wawancara_5',
        'wawancara_6',
        'skor_total',
        'token_assessment',
        'penilaian_admin_checklist',
        'dokumen_pernyataan',
        'dokumen_skck',
        'dokumen_slik',
        'dokumen_rencana_kerja',
        'dokumen_pailit',
        'keterangan'
    ];

    protected $casts = [
        'penilaian_admin_checklist' => 'array',
        'jadwal_wawancara' => 'datetime',
        'penguji_id' => 'array',
    ];

    protected static function booted()
    {
        static::creating(function ($peserta) {
            if (empty($peserta->token_assessment)) {
                $peserta->token_assessment = (string) \Illuminate\Support\Str::uuid();
            }
        });
    }

    public function koperasi()
    {
        return $this->belongsTo(Koperasi::class);
    }
    
    public function penguji()
    {
        return $this->belongsTo(Penguji::class, 'penguji_id');
    }
}
