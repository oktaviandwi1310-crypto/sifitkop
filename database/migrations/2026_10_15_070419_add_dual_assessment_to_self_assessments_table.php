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
        Schema::table('self_assessments', function (Blueprint $table) {
            // Penilaian Mandiri Calon Pengurus (Disimpan saat peserta submit)
            $table->integer('skor_mandiri_integritas')->default(0)->after('peserta_id');
            $table->integer('skor_mandiri_reputasi')->default(0)->after('skor_mandiri_integritas');
            $table->integer('skor_mandiri_kompetensi')->default(0)->after('skor_mandiri_reputasi');
            $table->integer('skor_mandiri_inovasi')->default(0)->after('skor_mandiri_kompetensi');
            $table->string('status_mandiri')->nullable()->after('skor_mandiri_inovasi');

            // Penilaian dari Tim Penguji / Asesor / Admin
            $table->foreignId('penguji_id')->nullable()->after('status_kelulusan')->constrained('users')->nullOnDelete();
            $table->integer('skor_penguji_integritas')->nullable()->after('penguji_id');
            $table->integer('skor_penguji_reputasi')->nullable()->after('skor_penguji_integritas');
            $table->integer('skor_penguji_kompetensi')->nullable()->after('skor_penguji_reputasi');
            $table->integer('skor_penguji_inovasi')->nullable()->after('skor_penguji_kompetensi');
            $table->string('status_penguji')->nullable()->after('skor_penguji_inovasi');
            $table->text('catatan_penguji')->nullable()->after('status_penguji');
            $table->json('penilaian_penguji_raw')->nullable()->after('catatan_penguji');
        });

        // Backfill data awal untuk record yang sudah ada
        \Illuminate\Support\Facades\DB::table('self_assessments')->update([
            'skor_mandiri_integritas' => \Illuminate\Support\Facades\DB::raw('integritas_score'),
            'skor_mandiri_reputasi' => \Illuminate\Support\Facades\DB::raw('reputasi_score'),
            'skor_mandiri_kompetensi' => \Illuminate\Support\Facades\DB::raw('kompetensi_score'),
            'skor_mandiri_inovasi' => \Illuminate\Support\Facades\DB::raw('inovasi_score'),
            'status_mandiri' => \Illuminate\Support\Facades\DB::raw('status_kelulusan'),
            'skor_penguji_integritas' => \Illuminate\Support\Facades\DB::raw('integritas_score'),
            'skor_penguji_reputasi' => \Illuminate\Support\Facades\DB::raw('reputasi_score'),
            'skor_penguji_kompetensi' => \Illuminate\Support\Facades\DB::raw('kompetensi_score'),
            'skor_penguji_inovasi' => \Illuminate\Support\Facades\DB::raw('inovasi_score'),
            'status_penguji' => \Illuminate\Support\Facades\DB::raw('status_kelulusan'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('self_assessments', function (Blueprint $table) {
            $table->dropForeign(['penguji_id']);
            $table->dropColumn([
                'skor_mandiri_integritas',
                'skor_mandiri_reputasi',
                'skor_mandiri_kompetensi',
                'skor_mandiri_inovasi',
                'status_mandiri',
                'penguji_id',
                'skor_penguji_integritas',
                'skor_penguji_reputasi',
                'skor_penguji_kompetensi',
                'skor_penguji_inovasi',
                'status_penguji',
                'catatan_penguji',
                'penilaian_penguji_raw',
            ]);
        });
    }
};
