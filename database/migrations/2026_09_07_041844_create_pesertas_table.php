<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesertas', function (Blueprint $table) {
            $table->id();
            // Relasi ke koperasis (Foreign Key)
            $table->foreignId('koperasi_id')->constrained('koperasis')->onDelete('cascade');
            
            // Data Pribadi Peserta
            $table->char('nik_peserta', 16)->unique();
            $table->string('nama_lengkap');
            $table->enum('jabatan_usulan', ['Pengurus', 'Pengawas'])->index();
            
            // Status Khusus UKK
            $table->string('status_kelulusan')->nullable()->index(); // Lulus, Tidak Lulus
            $table->integer('skor_wawancara')->nullable(); // >= 18
            
            // Audit Trails
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesertas');
    }
};
