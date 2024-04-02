<?php

use App\Models\BillingTransaction;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_transactions', function (Blueprint $table) {
            $table->id();
            $table->float('amount');
            $table->string('description');
            $table->boolean('is_successful');
            $table->boolean('rating_type')->nullable();
            $table->float('funds_before_deduction')->nullable();
            $table->float('funds_after_deduction')->nullable();
            $table->enum('failure_type', BillingTransaction::FAILURE_TYPES)->nullable();
            $table->text('failure_reason')->nullable();
            $table->boolean('created_using_auto_billing')->default(0);
            $table->foreignId('subscription_id')->nullable();
            $table->foreignId('subscription_plan_id');
            $table->foreignId('subscriber_id');
            $table->foreignId('project_id');
            $table->timestamps();

            $table->index(['subscription_plan_id']);
            $table->index(['subscriber_id']);
            $table->index(['project_id']);

            /*  Foreign Key Constraints */
            $table->foreign('subscription_plan_id')->references('id')->on('subscription_plans')->cascadeOnDelete();
            $table->foreign('subscription_id')->references('id')->on('subscriptions')->cascadeOnDelete();
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->cascadeOnDelete();
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
        Schema::dropIfExists('billing_transactions');
    }
}
