<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->text('admin_totp_secret')->nullable();
            $table->text('admin_recovery_codes')->nullable();
            $table->timestamp('admin_totp_confirmed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn([
                'admin_totp_secret',
                'admin_recovery_codes',
                'admin_totp_confirmed_at',
            ]);
        });
    }
};
