<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use App\Models\SubscriptionPlan;
use Illuminate\Support\Facades\Schema;

class AutoBillingReminderSubscriptionPlanController extends Controller
{
    public function showAutoBillingReminderSubscriptionPlans(Project $project)
    {
        //  Get the SMS campaigns
        $autoBillingReminderSubscriptionPlansPayload = $project->subscriptionPlans()->has('autoBillingReminderJobBatches')->with(['latestAutoBillingReminderJobBatch' => function($query) {

            //  Seleted columns
            $selectedColumns = collect(Schema::getColumnListing('job_batches'))
                                ->reject(fn ($name) => in_array($name, ['options', 'failed_job_ids']))
                                ->map(fn ($name) => 'job_batches.'.$name)
                                ->all();

            //  Limit the loaded message to the message id and sent sms count to consume less memory
            return $query->select(...$selectedColumns);

        }])->withCount('autoBillingReminderJobBatches')->latest()->paginate(10);

        //  Render view
        return Inertia::render('AutoBillingReminderSubscriptionPlans/List/Main', [
            'projectPayload' => $project,
            'autoBillingReminderSubscriptionPlansPayload' => $autoBillingReminderSubscriptionPlansPayload,
        ]);
    }

    public function showAutoBillingReminderSubscriptionPlanJobBatches(Project $project, SubscriptionPlan $subscriptionPlan)
    {
        //  Get the subscription plan auto billing reminder job batches
        $subscriptionPlanAutoBillingReminderJobBatchesPayload = $subscriptionPlan->autoBillingReminderJobBatches()->latest()->paginate(10);

        //  Render view
        return Inertia::render('AutoBillingReminderSubscriptionPlans/List/JobBatches/List/Main', [
            'subscriptionPlan' => $subscriptionPlan,
            'subscriptionPlanAutoBillingReminderJobBatchesPayload' => $subscriptionPlanAutoBillingReminderJobBatchesPayload
        ]);
    }
}
