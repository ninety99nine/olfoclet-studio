<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutoBillingReminderJobBatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auto_billing_reminder_job_batches', function (Blueprint $table) {
            $table->id();
            $table->string('job_batch_id');
            $table->foreignId('subscription_plan_id');
            $table->foreignId('auto_billing_reminder_id');
            $table->timestamps();

            $table->index(['job_batch_id', 'subscription_plan_id'], 'job_batch_id_subscription_plan_id_index');
            $table->index(['job_batch_id', 'auto_billing_reminder_id'], 'job_batch_id_auto_billing_reminder_id_index');

            /**
             *  Foreign Key Constraints
             *  -----------------------------
             *
             *  To avoid the following error:
             *
             *  SQLSTATE[42000]: Syntax error or access violation: 1059 Identifier name
             *  'auto_billing_reminder_job_batches_auto_billing_reminder_id_foreign' is too
             *  long (Connection: mysql, SQL: alter table `auto_billing_reminder_job_batches`
             *  add constraint `auto_billing_reminder_job_batches_auto_billing_reminder_id_foreign`
             *  foreign key (`auto_billing_reminder_id`) references `auto_billing_reminders` (`id`) on delete cascade)
             *
             *  We can simply abbreviate the "auto_billing_reminder_job_batches_auto_billing_reminder_id_foreign" into
             *  "abrjb_auto_billing_reminder_id_foreign"
             */
            $table->foreign('job_batch_id', 'abrjb_job_batch_id_foreign')->references('id')->on('job_batches')->cascadeOnDelete();
            $table->foreign('subscription_plan_id', 'abrjb_subscription_plan_id_foreign')->references('id')->on('subscription_plans')->cascadeOnDelete();
            $table->foreign('auto_billing_reminder_id', 'abrjb_auto_billing_reminder_id_foreign')->references('id')->on('auto_billing_reminders')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('auto_billing_reminder_job_batches');
    }
}
