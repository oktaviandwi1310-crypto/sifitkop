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
            $table->string('dokumen_pernyataan')->nullable();
            $table->string('dokumen_skck')->nullable();
            $table->string('dokumen_slik')->nullable();
            $table->string('dokumen_rencana_kerja')->nullable();
            $table->string('dokumen_pailit')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pesertas', function (Blueprint $table) {
            $table->dropColumn([
                'dokumen_pernyataan',
                'dokumen_skck',
                'dokumen_slik',
                'dokumen_rencana_kerja',
                'dokumen_pailit'
            ]);
        });
    }
};
