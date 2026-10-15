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
        Schema::create('landing_pages', function (Blueprint $table) {
            $table->id();
            
            $table->string('hero_title')->nullable();
            $table->text('hero_subtitle')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('hero_stats_title')->nullable();
            
            $table->string('about_title')->nullable();
            $table->text('about_description')->nullable();
            $table->string('about_image_1')->nullable();
            $table->string('about_image_2')->nullable();
            
            $table->json('services')->nullable();
            $table->json('features')->nullable();
            $table->json('team')->nullable();
            $table->json('testimonials')->nullable();
            
            $table->string('contact_address')->nullable();
            $table->string('contact_email')->nullable();
            $table->string('contact_phone')->nullable();
            $table->text('footer_description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('landing_pages');
    }
};
