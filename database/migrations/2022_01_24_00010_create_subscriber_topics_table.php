<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriberTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriber_topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('topic_id')->nullable();
            $table->foreignId('subscriber_id')->nullable();
            $table->boolean('started_reading')->default(false);
            $table->boolean('finished_reading')->default(false);
            $table->foreignId('project_id');
            $table->timestamps();

            $table->index(['topic_id']);
            $table->index(['subscriber_id']);
            $table->index(['project_id']);

            /*  Foreign Key Constraints */
            $table->foreign('topic_id')->references('id')->on('topics')->cascadeOnDelete();
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
        Schema::dropIfExists('subscriber_topics');
    }
}
