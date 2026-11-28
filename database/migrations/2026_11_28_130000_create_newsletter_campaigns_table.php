<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('newsletter_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('store_slug', 80);
            $table->string('subject', 180);
            $table->text('body');
            $table->string('status', 20)->default('draft');
            $table->foreignId('created_by_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('launched_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->index(['status', 'created_at'], 'newsletter_campaign_status');
        });

        Schema::create('newsletter_campaign_recipients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('newsletter_campaign_id');
            $table->foreignId('newsletter_subscription_id');
            $table->foreignId('outbound_message_id')->nullable();
            $table->timestamp('skipped_at')->nullable();
            $table->timestamps();
            $table->unique(['newsletter_campaign_id', 'newsletter_subscription_id'], 'newsletter_recipient_once');
            $table->index(['outbound_message_id']);
            $table->foreign('newsletter_campaign_id', 'ncr_campaign_fk')->references('id')->on('newsletter_campaigns')->cascadeOnDelete();
            $table->foreign('newsletter_subscription_id', 'ncr_subscription_fk')->references('id')->on('newsletter_subscriptions')->cascadeOnDelete();
            $table->foreign('outbound_message_id', 'ncr_outbox_fk')->references('id')->on('outbound_messages')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('newsletter_campaign_recipients');
        Schema::dropIfExists('newsletter_campaigns');
    }
};
