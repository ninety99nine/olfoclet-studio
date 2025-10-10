<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricingPlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pricing_plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description')->nullable();
            $table->boolean('active')->default(0);
            $table->boolean('is_folder')->default(0);
            $table->enum('billing_type', ['one time', 'subscription'])->default('subscription');
            $table->string('frequency')->nullable();
            $table->unsignedInteger('duration')->nullable();
            $table->unsignedSmallInteger('trial_days')->default(0);
            $table->decimal('price', 10, 2)->nullable();
            $table->json('tags')->nullable();
            $table->string('billing_product_id', 500)->nullable();
            $table->string('billing_purchase_category_code', 500)->nullable();
            $table->boolean('can_auto_bill')->default(false);
            $table->unsignedTinyInteger('max_auto_billing_attempts')->default(0);
            $table->string('insufficient_funds_message', 500)->nullable();
            $table->string('trial_started_sms_message', 500)->nullable();
            $table->string('successful_payment_sms_message', 500)->nullable();
            $table->string('successful_auto_billing_payment_sms_message', 500)->nullable();
            $table->string('next_auto_billing_reminder_sms_message', 500)->nullable();
            $table->string('auto_billing_disabled_sms_message', 500)->nullable();
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
        Schema::dropIfExists('pricing_plans');
    }
}
