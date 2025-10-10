<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use App\Models\PricingPlan;
use Illuminate\Support\Facades\Schema;

class AutoBillingPricingPlanController extends Controller
{
    public function showAutoBillingPricingPlans(Project $project)
    {
        //  Get the SMS campaigns
        $autoBillingPricingPlansPayload = $project->pricingPlans()->has('autoBillingJobBatches')->with(['latestAutoBillingJobBatch' => function($query) {

            //  Seleted columns
            $selectedColumns = collect(Schema::getColumnListing('job_batches'))
                                ->reject(fn ($name) => in_array($name, ['options', 'failed_job_ids']))
                                ->map(fn ($name) => 'job_batches.'.$name)
                                ->all();

            //  Limit the loaded message to the message id and sent sms count to consume less memory
            return $query->select(...$selectedColumns);

        }])->withCount('autoBillingJobBatches')->latest()->paginate(10);

        //  Render view
        return Inertia::render('AutoBillingPricingPlans/List/Main', [
            'projectPayload' => $project,
            'autoBillingPricingPlansPayload' => $autoBillingPricingPlansPayload,
        ]);
    }

    public function showAutoBillingPricingPlanJobBatches(Project $project, PricingPlan $pricingPlan)
    {
        //  Get the auto billing pricing plan job batches
        $pricingPlanAutoBillingJobBatchesPayload = $pricingPlan->autoBillingJobBatches()->latest()->paginate(10);

        //  Render view
        return Inertia::render('AutoBillingPricingPlans/List/JobBatches/List/Main', [
            'pricingPlan' => $pricingPlan,
            'pricingPlanAutoBillingJobBatchesPayload' => $pricingPlanAutoBillingJobBatchesPayload
        ]);
    }
}
