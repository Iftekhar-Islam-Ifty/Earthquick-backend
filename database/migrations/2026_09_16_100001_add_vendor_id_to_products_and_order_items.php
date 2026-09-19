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
        // 1. Add vendor_id to products table
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'vendor_id')) {
                $table->foreignId('vendor_id')->nullable()->after('id')->constrained('vendors')->nullOnDelete();
            }
        });

        // 2. Add vendor_id to order_items table
        Schema::table('order_items', function (Blueprint $table) {
            if (! Schema::hasColumn('order_items', 'vendor_id')) {
                $table->foreignId('vendor_id')->nullable()->after('product_id')->constrained('vendors')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            if (Schema::hasColumn('order_items', 'vendor_id')) {
                $table->dropForeign(['vendor_id']);
                $table->dropColumn('vendor_id');
            }
        });

        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'vendor_id')) {
                $table->dropForeign(['vendor_id']);
                $table->dropColumn('vendor_id');
            }
        });
    }
};
