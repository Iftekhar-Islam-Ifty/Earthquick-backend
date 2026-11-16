<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_brands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained('vendors')->cascadeOnDelete();
            $table->foreignId('warranty_provider_vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->string('name', 120);
            $table->string('slug', 140);
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['vendor_id', 'slug']);
            $table->index(['vendor_id', 'is_active']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->foreignId('brand_id')->nullable()->after('vendor_id')
                ->constrained('product_brands')->nullOnDelete();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('vendor_name', 160)->nullable()->after('product_name');
            $table->string('product_brand_name', 120)->nullable()->after('product_name');
            $table->string('warranty_provider_name', 120)->nullable()->after('product_brand_name');
        });

        $brightId = DB::table('vendors')->where('slug', 'bright')->value('id');
        if ($brightId) {
            DB::table('product_brands')->updateOrInsert(
                ['vendor_id' => $brightId, 'slug' => 'heaven'],
                [
                    'warranty_provider_vendor_id' => $brightId,
                    'name' => 'Heaven',
                    'description' => 'Bright-owned product brand.',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn(['vendor_name', 'product_brand_name', 'warranty_provider_name']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropConstrainedForeignId('brand_id');
        });

        Schema::dropIfExists('product_brands');
    }
};
