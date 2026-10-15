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
        Schema::table('koperasis', function (Blueprint $table) {
            $table->string('tanggal_badan_hukum')->nullable()->after('badan_hukum');
            $table->json('data_usaha')->nullable()->after('alamat'); // Menyimpan Aset, SHU, dll
            $table->json('susunan_pengurus')->nullable()->after('data_usaha'); // Daftar pengurus lama
            $table->json('susunan_pengawas')->nullable()->after('susunan_pengurus'); // Daftar pengawas lama
            $table->string('klasifikasi_koperasi')->nullable()->after('susunan_pengawas'); // Primer/Sekunder, Jenis Usaha, dll
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('koperasis', function (Blueprint $table) {
            $table->dropColumn([
                'tanggal_badan_hukum',
                'data_usaha',
                'susunan_pengurus',
                'susunan_pengawas',
                'klasifikasi_koperasi'
            ]);
        });
    }
};
