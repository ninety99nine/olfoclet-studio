<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsCampaignSubscriptionPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_campaign_subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sms_campaign_id');
            $table->foreignId('subscription_plan_id');
            $table->foreignId('project_id');
            $table->timestamps();

            $table->index(['sms_campaign_id']);
            $table->index(['subscription_plan_id']);
            $table->index(['project_id']);

            /*  Foreign Key Constraints */
            $table->foreign('sms_campaign_id')->references('id')->on('sms_campaigns')->cascadeOnDelete();
            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->cascadeOnDelete();
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_campaign_subscription_plans');
    }
}
