<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pricing_plan_id')->nullable();
            $table->foreignId('subscriber_id')->nullable();
            $table->timestamp('start_at')->nullable();
            $table->timestamp('end_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->boolean('created_using_auto_billing')->default(0);
            $table->foreignId('project_id');
            $table->timestamps();

            $table->index(['pricing_plan_id']);
            $table->index(['subscriber_id']);
            $table->index(['project_id']);

            /*  Foreign Key Constraints */
            $table->foreign('pricing_plan_id')->references('id')->on('pricing_plans')->nullOnDelete();
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->nullOnDelete();
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
        Schema::dropIfExists('subscriptions');
    }
}
