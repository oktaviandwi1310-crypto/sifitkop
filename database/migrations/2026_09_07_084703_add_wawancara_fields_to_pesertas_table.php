<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesertas', function (Blueprint $table) {
            $table->dateTime('jadwal_wawancara')->nullable()->after('dokumen_cv');
            $table->string('dokumen_paparan')->nullable()->after('jadwal_wawancara');
            $table->foreignId('penguji_id')->nullable()->constrained('users')->nullOnDelete()->after('dokumen_paparan');
        });
    }

    public function down(): void
    {
        Schema::table('pesertas', function (Blueprint $table) {
            $table->dropForeign(['penguji_id']);
            $table->dropColumn(['jadwal_wawancara', 'dokumen_paparan', 'penguji_id']);
        });
    }
};
