<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'product_type')) {
                $table->string('product_type', 40)->default('general')->after('subcategory_id')->index();
            }
            if (! Schema::hasColumn('products', 'specifications')) {
                $table->json('specifications')->nullable()->after('description');
            }
            if (! Schema::hasColumn('products', 'warranty_info')) {
                $table->string('warranty_info')->nullable()->after('specifications');
            }
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $columns = [];
            foreach (['warranty_info', 'specifications', 'product_type'] as $column) {
                if (Schema::hasColumn('products', $column)) {
                    $columns[] = $column;
                }
            }
            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
