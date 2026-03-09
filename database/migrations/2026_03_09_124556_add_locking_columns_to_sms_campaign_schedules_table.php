<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('sms_campaign_schedules', function (Blueprint $table) {
            $table->boolean('is_processing')->default(false);
            $table->string('processing_token')->nullable();

            $table->index(['is_processing', 'next_message_date'], 'idx_sms_processing_lookup');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sms_campaign_schedules', function (Blueprint $table) {
            $table->dropIndex('idx_sms_processing_lookup');
            $table->dropColumn(['is_processing', 'processing_token']);
        });
    }
};
