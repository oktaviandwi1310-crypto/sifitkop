<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('self_assessments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('peserta_id')->constrained('pesertas')->cascadeOnDelete();
            
            // Skor per variabel
            $table->integer('integritas_score')->default(0)->comment('>= 9 Terpenuhi');
            $table->integer('reputasi_score')->default(0)->comment('>= 2 Terpenuhi');
            $table->integer('kompetensi_score')->default(0)->comment('>= 27 Terpenuhi');
            $table->integer('inovasi_score')->default(0)->comment('>= 9 Terpenuhi');
            
            // Status Akhir Self Assessment
            $table->enum('status_kelulusan', ['Terpenuhi', 'Tidak Terpenuhi'])->nullable();
            
            // Kolom JSON untuk menyimpan rincian jawaban jika diperlukan untuk audit trail
            $table->json('jawaban_raw')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('self_assessments');
    }
};
