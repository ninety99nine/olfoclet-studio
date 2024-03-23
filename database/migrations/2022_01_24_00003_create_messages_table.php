<?php

use App\Enums\MessageType;
use App\Models\Message;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('content', 500)->nullable();
            $table->foreignId('project_id');

            /**
             *  The nestedSet() method is required to handle nested relationships
             *  Refer to: https://github.com/lazychaser/laravel-nestedset
             */
            $table->nestedSet();
            $table->timestamps();

            /* Add Indexes */
            $table->index(['project_id']);

            /**
             *  Foreign Key Constraints
             *
             *  Note: The parent_id is set by the $table->nestedSet() method.
             */
            $table->foreign('parent_id')->references('id')->on('topics')->cascadeOnDelete();
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
        Schema::dropIfExists('messages');
    }
}
