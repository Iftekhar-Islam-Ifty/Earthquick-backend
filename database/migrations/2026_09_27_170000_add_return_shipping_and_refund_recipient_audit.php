<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('order_return_requests', function (Blueprint $table) {
            $table->string('reported_issue_type', 32)->nullable();
            $table->string('verified_issue_type', 32)->nullable();
            $table->string('return_shipping_payer', 16)->nullable();
        });

        Schema::table('order_refunds', function (Blueprint $table) {
            $table->decimal('return_shipping_amount', 10, 2)->default(0);
            $table->string('return_shipping_receipt_reference', 100)->nullable()->unique();
            $table->string('recipient_name', 150)->nullable();
            $table->string('recipient_account_last4', 4)->nullable();
            $table->string('recipient_verified_via', 32)->nullable();
            $table->text('recipient_verification_note')->nullable();
            $table->foreignId('recipient_verified_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('recipient_verified_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('order_refunds', function (Blueprint $table) {
            $table->dropConstrainedForeignId('recipient_verified_by_user_id');
            $table->dropColumn([
                'return_shipping_amount', 'return_shipping_receipt_reference',
                'recipient_name', 'recipient_account_last4', 'recipient_verified_via',
                'recipient_verification_note', 'recipient_verified_at',
            ]);
        });

        Schema::table('order_return_requests', function (Blueprint $table) {
            $table->dropColumn(['reported_issue_type', 'verified_issue_type', 'return_shipping_payer']);
        });
    }
};
