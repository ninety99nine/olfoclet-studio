<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('billing_reports', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->float('gross_revenue')->nullable();
            $table->float('costs')->nullable();
            $table->json('cost_breakdown')->nullable();
            $table->float('sharable_revenue')->nullable();
            $table->float('our_share')->nullable();
            $table->float('their_share')->nullable();
            $table->unsignedInteger('total_transactions');
            $table->string('overview_pdf_path')->nullable();
            $table->string('successful_transactions_csv_path')->nullable();
            $table->string('invoice_pdf_path')->nullable();
            $table->foreignId('project_id');
            $table->timestamps();

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
        Schema::dropIfExists('billing_reports');
    }
}
