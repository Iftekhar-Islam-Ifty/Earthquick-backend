<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('cod_collection_channel', 32)->nullable();
            $table->string('cod_collection_note', 255)->nullable();
            $table->foreignId('paid_recorded_by')->nullable()->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('paid_recorded_by');
            $table->dropColumn(['cod_collection_channel', 'cod_collection_note']);
        });
    }
};
