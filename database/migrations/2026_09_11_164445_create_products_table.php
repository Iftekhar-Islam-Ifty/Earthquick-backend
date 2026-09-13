<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->foreignId('subcategory_id')->nullable()->constrained()->nullOnDelete();
            $table->string('sku')->unique()->nullable();
            $table->string('name');
            $table->string('slug')->unique();
            $table->decimal('price', 10, 2);
            $table->decimal('old_price', 10, 2)->nullable();
            $table->string('fabric')->nullable();
            $table->text('short_desc')->nullable();
            $table->longText('description')->nullable();
            $table->string('image');
            $table->string('alt_image')->nullable();
            $table->string('badge')->nullable();
            $table->string('badge_type')->nullable();
            $table->decimal('rating', 2, 1)->default(4.9);
            $table->unsignedInteger('reviews_count')->default(0);
            $table->boolean('in_stock')->default(true);
            $table->unsignedInteger('stock_quantity')->default(10);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_new_arrival')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};