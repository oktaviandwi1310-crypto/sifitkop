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
            $table->string('dokumen_pad')->nullable();
            $table->string('dokumen_rat')->nullable();
            $table->string('dokumen_lap_keuangan')->nullable();
            $table->string('dokumen_goaml')->nullable();
            $table->string('dokumen_npwp')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('koperasis', function (Blueprint $table) {
            $table->dropColumn([
                'dokumen_pad',
                'dokumen_rat',
                'dokumen_lap_keuangan',
                'dokumen_goaml',
                'dokumen_npwp'
            ]);
        });
    }
};
