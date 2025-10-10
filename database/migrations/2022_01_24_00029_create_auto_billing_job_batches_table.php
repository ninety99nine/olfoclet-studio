<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutoBillingJobBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_billing_job_batches', function (Blueprint $table) {
            $table->id();
            $table->string('job_batch_id');
            $table->foreignId('pricing_plan_id');
            $table->timestamps();

            $table->index(['job_batch_id', 'pricing_plan_id']);

            /*  Foreign Key Constraints */
            $table->foreign('job_batch_id')->references('id')->on('job_batches')->cascadeOnDelete();
            $table->foreign('pricing_plan_id')->references('id')->on('pricing_plans')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auto_billing_job_batches');
    }
}
