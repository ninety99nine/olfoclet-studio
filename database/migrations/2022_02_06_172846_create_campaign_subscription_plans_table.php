<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignSubscriptionPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaign_subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('campaign_id')->nullable();
            $table->unsignedInteger('subscription_plan_id')->nullable();
            $table->unsignedInteger('project_id');
            $table->timestamps();

            $table->index(['campaign_id']);
            $table->index(['subscription_plan_id']);
            $table->index(['project_id']);

            /*  Foreign Key Constraints */
            $table->foreign('campaign_id')->references('id')->on('campaigns')->cascadeOnDelete();
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
        Schema::dropIfExists('campaign_subscription_plans');
    }
}
