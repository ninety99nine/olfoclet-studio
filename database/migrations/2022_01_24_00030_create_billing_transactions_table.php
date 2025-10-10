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
            $table->decimal('amount', 10, 2);
            $table->string('description');
            $table->boolean('is_successful');
            $table->string('rating_type')->nullable();
            $table->decimal('funds_before_deduction', 10, 2)->nullable();
            $table->decimal('funds_after_deduction', 10, 2)->nullable();
            $table->enum('failure_type', BillingTransaction::FAILURE_TYPES)->nullable();
            $table->string('failure_reason')->nullable();
            $table->json('failed_attempts')->nullable();
            $table->boolean('created_using_auto_billing')->default(0);
            $table->string('client_correlator');
            $table->string('reference_code');
            $table->foreignId('subscription_id')->nullable();
            $table->foreignId('pricing_plan_id');
            $table->foreignId('subscriber_id');
            $table->foreignId('project_id');
            $table->timestamps();

            $table->index(['pricing_plan_id']);
            $table->index(['subscriber_id']);
            $table->index(['project_id']);

            /*  Foreign Key Constraints */
            $table->foreign('pricing_plan_id')->references('id')->on('pricing_plans')->cascadeOnDelete();
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
