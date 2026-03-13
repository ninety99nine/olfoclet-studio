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
            $table->unsignedTinyInteger('delivery_status_update_attempts')
                ->default(0)
                ->after('delivery_status_update_is_successful');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subscriber_messages', function (Blueprint $table) {
            $table->dropColumn('delivery_status_update_attempts');
        });
    }
};

