<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Historical payment state cannot be inferred from the method alone.
            $table->string('payment_status', 32)->default('unknown');
            $table->string('payment_reference', 100)->nullable()->unique();
            $table->timestamp('paid_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropUnique(['payment_reference']);
            $table->dropColumn(['payment_status', 'payment_reference', 'paid_at']);
        });
    }
};
