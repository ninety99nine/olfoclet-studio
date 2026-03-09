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
        Schema::table('auto_billing_schedules', function (Blueprint $table) {

            $table->boolean('is_processing')->default(false)->after('auto_billing_enabled');
            $table->string('processing_token')->nullable()->after('is_processing');

            $table->index(['is_processing', 'next_attempt_date'], 'idx_billing_lookup');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('auto_billing_schedules', function (Blueprint $table) {
            $table->dropIndex('idx_billing_lookup');
            $table->dropColumn(['is_processing', 'processing_token']);
        });
    }
};
