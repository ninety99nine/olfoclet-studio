<?php

use App\Models\SmsCampaign;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSmsCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sms_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description', 500)->nullable();
            $table->boolean('can_send_messages')->default(false);
            $table->boolean('can_repeat_messages')->default(true);

            $table->enum('schedule_type', SmsCampaign::SCHEDULE_TYPE)->default(Arr::first(SmsCampaign::SCHEDULE_TYPE));
            $table->unsignedInteger('recurring_duration')->nullable();
            $table->string('recurring_frequency')->nullable();

            $table->enum('message_to_send', SmsCampaign::MESSAGE_TO_SEND)->default(Arr::last(SmsCampaign::MESSAGE_TO_SEND));
            $table->json('message_ids');

            $table->boolean('has_start_date')->default(false);
            $table->date('start_date')->nullable();
            $table->char('start_time', 5)->nullable();

            $table->boolean('has_end_date')->default(false);
            $table->date('end_date')->nullable();
            $table->char('end_time', 5)->nullable();

            $table->json('days_of_the_week')->nullable();
            $table->json('pricing_plan_ids');

            $table->foreignId('project_id');
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
        Schema::dropIfExists('sms_campaigns');
    }
}
