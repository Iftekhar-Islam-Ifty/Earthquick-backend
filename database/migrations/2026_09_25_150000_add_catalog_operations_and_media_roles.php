<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->boolean('is_returnable')->default(true)->after('warranty_info');
            $table->unsignedSmallInteger('return_window_days')->nullable()->default(7)->after('is_returnable');
            $table->string('return_policy_note')->nullable()->after('return_window_days');
            $table->string('delivery_class', 30)->default('standard')->after('return_policy_note')->index();
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->string('role', 30)->default('gallery')->after('image_path')->index();
            $table->string('alt_text')->nullable()->after('role');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->string('delivery_class', 30)->nullable()->after('variant_attributes');
            $table->boolean('is_returnable')->nullable()->after('delivery_class');
            $table->unsignedSmallInteger('return_window_days')->nullable()->after('is_returnable');
            $table->string('return_policy_note')->nullable()->after('return_window_days');
        });
    }

    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_class',
                'is_returnable',
                'return_window_days',
                'return_policy_note',
            ]);
        });

        Schema::table('product_images', function (Blueprint $table) {
            $table->dropIndex(['role']);
            $table->dropColumn(['role', 'alt_text']);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['delivery_class']);
            $table->dropColumn([
                'is_returnable',
                'return_window_days',
                'return_policy_note',
                'delivery_class',
            ]);
        });
    }
};
