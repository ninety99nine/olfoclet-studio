<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignSubscriberTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_subscriber', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id');
            $table->foreignId('campaign_id')->nullable();
            $table->foreignId('subscriber_id')->nullable();
            $table->datetime('next_message_date')->nullable();
            $table->unsignedSmallInteger('sent_sms_count')->default(1);
            $table->timestamps();

            $table->index(['project_id']);
            $table->index(['campaign_id']);
            $table->index(['subscriber_id']);

            /*  Foreign Key Constraints */
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            $table->foreign('campaign_id')->references('id')->on('campaigns')->cascadeOnDelete();
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
        Schema::dropIfExists('campaign_subscriber');
    }
}
