<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTopicsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100)->nullable();
            $table->string('content', 5000)->nullable();
            $table->foreignId('project_id');
            /**
             *  The nestedSet() method is required to handle nested relationships
             *  Refer to: https://github.com/lazychaser/laravel-nestedset
             */
            $table->nestedSet();
            $table->timestamps();

            $table->index(['title']);
            $table->index(['project_id']);

            /**
             *  Foreign Key Constraints
             */
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
        Schema::dropIfExists('topics');
    }
}
