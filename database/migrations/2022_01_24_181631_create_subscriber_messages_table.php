<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriberMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriber_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('message_id')->nullable();
            $table->unsignedInteger('subscriber_id')->nullable();
            $table->unsignedSmallInteger('sent_sms_count')->default(1);
            $table->unsignedInteger('project_id');
            $table->timestamps();

            $table->index(['message_id']);
            $table->index(['subscriber_id']);
            $table->index(['project_id']);

            /*  Foreign Key Constraints */
            $table->foreign('message_id')->references('id')->on('messages')->cascadeOnDelete();
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
        Schema::dropIfExists('subscriber_messages');
    }
}
