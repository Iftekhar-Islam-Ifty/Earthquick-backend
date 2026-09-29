<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->timestamp('archived_at')->nullable()->index();
            $table->unsignedBigInteger('archived_by_user_id')->nullable();
            $table->softDeletes();
            $table->unsignedBigInteger('deleted_by_user_id')->nullable();
            $table->string('deletion_reason', 500)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropIndex(['archived_at']);
            $table->dropColumn(['archived_at', 'archived_by_user_id', 'deleted_at', 'deleted_by_user_id', 'deletion_reason']);
        });
    }
};
