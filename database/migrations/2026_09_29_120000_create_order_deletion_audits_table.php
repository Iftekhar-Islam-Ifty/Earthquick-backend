<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_deletion_audits', function (Blueprint $table) {
            $table->id();
            // No foreign key: the original order is intentionally removed.
            $table->unsignedBigInteger('original_order_id');
            $table->string('order_number');
            $table->foreignId('deleted_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('reason', 500);
            $table->timestamp('deleted_at');

            $table->index('order_number');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_deletion_audits');
    }
};
