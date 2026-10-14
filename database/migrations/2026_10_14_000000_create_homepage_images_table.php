<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('homepage_images', function (Blueprint $table) {
            $table->string('slot', 32)->primary();
            $table->string('image_path')->nullable();
            $table->string('alt_text', 160)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('homepage_images');
    }
};
