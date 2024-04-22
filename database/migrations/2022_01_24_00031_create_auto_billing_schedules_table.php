<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutoBillingSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_billing_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscriber_id');
            $table->foreignId('subscription_plan_id');
            $table->boolean('auto_billing_enabled')->default(1);
            $table->datetime('next_attempt_date')->nullable();
            $table->unsignedTinyInteger('attempts')->default(0);
            $table->unsignedSmallInteger('total_successful_attempts')->default(0);
            $table->unsignedSmallInteger('total_failed_attempts')->default(0);
            $table->datetime('reminded_one_hour_before_at');
            $table->datetime('reminded_six_hours_before_at');
            $table->datetime('reminded_twelve_hours_before_at');
            $table->datetime('reminded_twenty_four_hours_before_at');
            $table->datetime('reminded_forty_eight_hours_before_at');
            $table->datetime('reminded_seventy_two_hours_before_at');
            $table->foreignId('project_id');
            $table->timestamps();

            $table->index(['project_id']);
            $table->index(['subscriber_id']);
            $table->index(['next_attempt_date']);
            $table->index(['subscription_plan_id']);
            $table->index(['auto_billing_enabled']);

            /*  Foreign Key Constraints */
            $table->foreign('project_id')->references('id')->on('projects')->cascadeOnDelete();
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->cascadeOnDelete();
            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auto_billing_schedules');
    }
}
