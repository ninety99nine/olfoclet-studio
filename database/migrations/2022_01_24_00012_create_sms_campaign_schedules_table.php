<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsCampaignSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_campaign_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id');
            $table->foreignId('subscriber_id');
            $table->foreignId('sms_campaign_id');
            $table->datetime('next_message_date');
            $table->unsignedSmallInteger('attempts')->default(0);
            $table->unsignedSmallInteger('total_successful_attempts')->default(0);
            $table->unsignedSmallInteger('total_failed_attempts')->default(0);

            $table->timestamps();

            $table->index(['project_id']);
            $table->index(['sms_campaign_id']);
            $table->index(['subscriber_id']);

            /*  Foreign Key Constraints */
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            $table->foreign('sms_campaign_id')->references('id')->on('sms_campaigns')->cascadeOnDelete();
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_campaign_schedules');
    }
}
