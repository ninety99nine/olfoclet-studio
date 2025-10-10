<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use App\Models\PricingPlan;
use Illuminate\Support\Facades\Schema;

class AutoBillingReminderPricingPlanController extends Controller
{
    public function showAutoBillingReminderPricingPlans(Project $project)
    {
        //  Get the SMS campaigns
        $autoBillingReminderPricingPlansPayload = $project->pricingPlans()->has('autoBillingReminderJobBatches')->with(['latestAutoBillingReminderJobBatch' => function($query) {

            //  Seleted columns
            $selectedColumns = collect(Schema::getColumnListing('job_batches'))
                                ->reject(fn ($name) => in_array($name, ['options', 'failed_job_ids']))
                                ->map(fn ($name) => 'job_batches.'.$name)
                                ->all();

            //  Limit the loaded message to the message id and sent sms count to consume less memory
            return $query->select(...$selectedColumns);

        }])->withCount('autoBillingReminderJobBatches')->latest()->paginate(10);

        //  Render view
        return Inertia::render('AutoBillingReminderPricingPlans/List/Main', [
            'projectPayload' => $project,
            'autoBillingReminderPricingPlansPayload' => $autoBillingReminderPricingPlansPayload,
        ]);
    }

    public function showAutoBillingReminderPricingPlanJobBatches(Project $project, PricingPlan $pricingPlan)
    {
        //  Get the pricing plan auto billing reminder job batches
        $pricingPlanAutoBillingReminderJobBatchesPayload = $pricingPlan->autoBillingReminderJobBatches()->latest()->paginate(10);

        //  Render view
        return Inertia::render('AutoBillingReminderPricingPlans/List/JobBatches/List/Main', [
            'pricingPlan' => $pricingPlan,
            'pricingPlanAutoBillingReminderJobBatchesPayload' => $pricingPlanAutoBillingReminderJobBatchesPayload
        ]);
    }
}
