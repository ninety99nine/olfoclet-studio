<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description', 500)->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('website_url')->nullable();
            $table->boolean('can_auto_bill')->default(false);
            $table->boolean('can_send_messages')->default(false);
            $table->boolean('can_create_billing_reports')->default(false);
            $table->json('costs')->nullable();
            $table->unsignedTinyInteger('our_share_percentage')->nullable();
            $table->unsignedTinyInteger('their_share_percentage')->nullable();
            $table->json('billing_report_email_addresses')->nullable();
            $table->json('settings')->nullable();
            $table->timestamps();

            $table->index(['name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
