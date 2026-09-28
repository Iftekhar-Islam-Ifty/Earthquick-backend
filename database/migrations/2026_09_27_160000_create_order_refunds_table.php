<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_return_request_id')->unique()->constrained('order_return_requests')->cascadeOnDelete();
            $table->decimal('item_amount', 10, 2);
            $table->decimal('discount_share', 10, 2)->default(0);
            $table->decimal('delivery_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->string('status', 32)->default('approved');
            $table->foreignId('approved_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->text('approval_note')->nullable();
            $table->string('method', 32)->nullable();
            $table->string('reference', 100)->nullable()->unique();
            $table->foreignId('completed_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_refunds');
    }
};
