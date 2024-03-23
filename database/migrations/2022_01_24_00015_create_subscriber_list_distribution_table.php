<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriberListDistributionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriber_list_distribution', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subscriber_id');
            $table->foreignId('subscriber_list_id');
            $table->foreignId('project_id');
            $table->timestamps();

            $table->index(['subscriber_id']);
            $table->index(['subscriber_list_id']);
            $table->index(['project_id']);

            /*  Foreign Key Constraints */
            $table->foreign('subscriber_id')->references('id')->on('subscribers')->cascadeOnDelete();
            $table->foreign('subscriber_list_id')->references('id')->on('subscriber_lists')->cascadeOnDelete();
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
        Schema::dropIfExists('subscriber_list_distribution');
    }
}
