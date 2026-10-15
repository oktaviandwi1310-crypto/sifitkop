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
        Schema::table('pesertas', function (Blueprint $table) {
            $table->enum('status_verifikasi', ['Pending', 'Revisi', 'Terverifikasi', 'Ditolak'])
                  ->default('Pending')
                  ->after('jabatan_usulan')
                  ->comment('Status verifikasi administratif Modul 2');
                  
            $table->string('dokumen_ktp')->nullable()->after('status_verifikasi');
            $table->string('dokumen_permohonan')->nullable()->after('dokumen_ktp');
            $table->string('dokumen_cv')->nullable()->after('dokumen_permohonan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesertas', function (Blueprint $table) {
            $table->dropColumn([
                'status_verifikasi', 
                'dokumen_ktp', 
                'dokumen_permohonan', 
                'dokumen_cv'
            ]);
        });
    }
};
