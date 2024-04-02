<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriptionPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscription_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description');
            $table->boolean('active')->default(0);
            $table->boolean('is_folder')->default(0);
            $table->string('frequency')->nullable();
            $table->unsignedInteger('duration')->nullable();
            $table->float('price')->nullable();
            $table->boolean('can_auto_bill')->default(false);
            $table->unsignedTinyInteger('max_auto_billing_attempts')->default(1);
            $table->string('insufficient_funds_message')->nullable();
            $table->string('successful_payment_sms_message')->nullable();
            $table->string('next_auto_billing_reminder_sms_message')->nullable();
            $table->string('subscription_end_at_reference_name')->nullable();
            $table->foreignId('project_id');

            /**
             *  The nestedSet() method is required to handle nested relationships
             *  Refer to: https://github.com/lazychaser/laravel-nestedset
             */
            $table->nestedSet();
            $table->timestamps();

            $table->index(['name']);
            $table->index(['project_id']);

            /*  Foreign Key Constraints */
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
        Schema::dropIfExists('subscription_plans');
    }
}
