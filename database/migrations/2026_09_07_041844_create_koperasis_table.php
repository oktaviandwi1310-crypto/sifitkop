<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('koperasis', function (Blueprint $table) {
            $table->id();
            // NIK char(16) for optimization and indexed
            $table->char('nik', 16)->unique();
            $table->string('nama_koperasi');
            $table->string('badan_hukum')->nullable();
            $table->string('kabupaten_kota')->index();
            $table->text('alamat')->nullable();
            $table->string('status_ods')->default('Aktif')->index(); // Aktif, Tidak Aktif, dll
            
            // Kolom spesifik SIFITKOP
            $table->string('status_pengajuan')->default('Draft')->index(); // Draft, Verifikasi, Wawancara, Lulus
            
            // Audit Trails
            $table->timestamps();
            $table->softDeletes(); // Sesuai arsitektur High-Load
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('koperasis');
    }
};
