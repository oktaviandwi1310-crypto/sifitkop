<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $lama = DB::table('self_assessments')
            ->whereNotNull('penguji_id')
            ->pluck('penguji_id', 'id');

        Schema::table('self_assessments', function (Blueprint $table) {
            $table->dropForeign(['penguji_id']);
            $table->dropColumn('penguji_id');
        });

        Schema::table('self_assessments', function (Blueprint $table) {
            $table->json('penguji_id')->nullable()->after('status_kelulusan');
        });

        foreach ($lama as $id => $pengujiId) {
            DB::table('self_assessments')
                ->where('id', $id)
                ->update(['penguji_id' => json_encode([(int) $pengujiId])]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
