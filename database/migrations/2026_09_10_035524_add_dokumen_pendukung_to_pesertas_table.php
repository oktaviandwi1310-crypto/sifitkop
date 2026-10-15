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
            $table->string('dokumen_pendukung_peserta')->nullable()->after('dokumen_cv');
            $table->string('dokumen_pendukung_koperasi')->nullable()->after('dokumen_pendukung_peserta');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesertas', function (Blueprint $table) {
            $table->dropColumn(['dokumen_pendukung_peserta', 'dokumen_pendukung_koperasi']);
        });
    }
};
