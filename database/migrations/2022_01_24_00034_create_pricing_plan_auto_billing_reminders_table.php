<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricingPlanAutoBillingRemindersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pricing_plan_auto_billing_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id');
            $table->foreignId('pricing_plan_id');
            $table->foreignId('auto_billing_reminder_id');
            $table->timestamps();

            $table->index(['auto_billing_reminder_id', 'pricing_plan_id'], 'auto_billing_reminder_id_pricing_plan_id_index');

            /**
             *  Foreign Key Constraints
             *  -----------------------------
             *
             *  To avoid the following error:
             *
             *  SQLSTATE[42000]: Syntax error or access violation: 1059 Identifier name
             *  'pricing_plan_auto_billing_reminders_auto_billing_reminder_id_foreign' is too
             *  long (Connection: mysql, SQL: alter table `pricing_plan_auto_billing_reminders`
             *  add constraint `pricing_plan_auto_billing_reminders_auto_billing_reminder_id_foreign`
             *  foreign key (`auto_billing_reminder_id`) references `auto_billing_reminders` (`id`) on delete cascade)
             *
             *  We can simply abbreviate the "pricing_plan_auto_billing_reminders_auto_billing_reminder_id_foreign" into
             *  "spabr_auto_billing_reminder_id_foreign"
             */
            $table->foreign('project_id', 'spabr_project_id_foreign')->references('id')->on('projects')->cascadeOnDelete();
            $table->foreign('pricing_plan_id', 'spabr_pricing_plan_id_foreign')->references('id')->on('pricing_plans')->cascadeOnDelete();
            $table->foreign('auto_billing_reminder_id', 'spabr_auto_billing_reminder_id_foreign')->references('id')->on('auto_billing_reminders')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pricing_plan_auto_billing_reminders');
    }
}
