<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesertas', function (Blueprint $table) {
            $table->integer('skor_admin')
                ->default(4)
                ->comment('Maksimal 17 (13 dokumen + 4 poin awal)')
                ->change();
        });

        // Hitung ulang data lama: jumlah centang + 4 poin awal
        DB::table('pesertas')->orderBy('id')->each(function ($row) {
            $checklist = json_decode($row->penilaian_admin_checklist ?? '{}', true) ?? [];

            $skor = count($checklist['peserta'] ?? [])
                  + count($checklist['koperasi'] ?? [])
                  + 4;

            DB::table('pesertas')
                ->where('id', $row->id)
                ->update(['skor_admin' => $skor]);
        });
    }

    public function down(): void
    {
        Schema::table('pesertas', function (Blueprint $table) {
            $table->integer('skor_admin')
                ->default(0)
                ->comment('Maksimal 64')
                ->change();
        });
    }
};
