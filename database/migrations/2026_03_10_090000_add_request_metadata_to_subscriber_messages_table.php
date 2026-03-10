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
        Schema::table('subscriber_messages', function (Blueprint $table) {
            $table->string('send_endpoint')->nullable()->after('delivery_status_endpoint');

            $table->timestamp('sent_at')->nullable()->after('send_endpoint');
            $table->timestamp('delivery_status_checked_at')->nullable()->after('sent_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriber_messages', function (Blueprint $table) {
            $table->dropColumn(['send_endpoint', 'sent_at', 'delivery_status_checked_at']);
        });
    }
};

