<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSmsCampaignJobBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_campaign_job_batches', function (Blueprint $table) {
            $table->id();
            $table->string('job_batch_id');
            $table->foreignId('sms_campaign_id');
            $table->timestamps();

            $table->index(['job_batch_id', 'sms_campaign_id']);

            /*  Foreign Key Constraints */
            $table->foreign('job_batch_id')->references('id')->on('job_batches')->cascadeOnDelete();
            $table->foreign('sms_campaign_id')->references('id')->on('sms_campaigns')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sms_campaign_job_batches');
    }
}
