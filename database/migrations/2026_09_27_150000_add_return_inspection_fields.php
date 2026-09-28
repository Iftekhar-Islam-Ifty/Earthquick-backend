<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_return_requests', function (Blueprint $table) {
            $table->string('inspection_outcome', 32)->nullable();
            $table->text('inspection_note')->nullable();
            $table->foreignId('inspected_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('inspected_at')->nullable();
            $table->foreignId('restocked_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('restocked_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('order_return_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('restocked_by_user_id');
            $table->dropConstrainedForeignId('inspected_by_user_id');
            $table->dropColumn(['inspection_outcome', 'inspection_note', 'inspected_at', 'restocked_at']);
        });
    }
};
