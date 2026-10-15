<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesertas', function (Blueprint $table) {
            $table->integer('skor_admin')->default(0)->after('status_verifikasi')->comment('Maksimal 64');
            $table->integer('wawancara_1')->default(0)->after('skor_wawancara');
            $table->integer('wawancara_2')->default(0)->after('wawancara_1');
            $table->integer('wawancara_3')->default(0)->after('wawancara_2');
            $table->integer('wawancara_4')->default(0)->after('wawancara_3');
            $table->integer('wawancara_5')->default(0)->after('wawancara_4');
            $table->integer('wawancara_6')->default(0)->after('wawancara_5');
            $table->integer('skor_total')->default(0)->after('wawancara_6')->comment('skor_admin + skor_wawancara');
        });
    }

    public function down(): void
    {
        Schema::table('pesertas', function (Blueprint $table) {
            $table->dropColumn([
                'skor_admin', 'wawancara_1', 'wawancara_2', 'wawancara_3', 
                'wawancara_4', 'wawancara_5', 'wawancara_6', 'skor_total'
            ]);
        });
    }
};
