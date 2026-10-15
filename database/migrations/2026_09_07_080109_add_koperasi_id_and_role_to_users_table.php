<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'koperasi'])->default('admin')->after('email');
            $table->foreignId('koperasi_id')->nullable()->constrained('koperasis')->nullOnDelete()->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['koperasi_id']);
            $table->dropColumn(['role', 'koperasi_id']);
        });
    }
};
