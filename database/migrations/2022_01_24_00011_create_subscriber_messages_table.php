<?php

use App\Enums\MessageType;
use App\Models\Message;
use App\Models\Pivots\SubscriberMessage;
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
            $table->foreignId('message_id')->nullable();
            $table->foreignId('subscriber_id');
            $table->string('content', 500);
            $table->enum('type', SubscriberMessage::TYPES)->default(MessageType::Content->value);
            $table->boolean('is_successful')->nullable();
            $table->string('delivery_status')->nullable();
            $table->string('delivery_status_endpoint')->nullable();
            $table->enum('failure_type', SubscriberMessage::FAILURE_TYPES)->nullable();
            $table->text('failure_reason')->nullable();
            $table->boolean('delivery_status_update_is_successful')->nullable();
            $table->enum('delivery_status_update_failure_type', SubscriberMessage::UPDATE_DELIVERY_STATUS_FAILURE_TYPES)->nullable();
            $table->text('delivery_status_update_failure_reason')->nullable();
            $table->foreignId('project_id');
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
