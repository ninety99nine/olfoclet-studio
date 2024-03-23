<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\SmsCampaign;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

class AutoBillingSubscriptionPlanController extends Controller
{
    public function showAutoBillingSubscriptionPlans(Project $project)
    {
        //  Get the SMS campaigns
        $autoBillingSubscriptionPlansPayload = $project->subscriptionPlans()->has('autoBillingJobBatches')->with(['latestAutoBillingJobBatch' => function($query) {

            //  Seleted columns
            $selectedColumns = collect(Schema::getColumnListing('job_batches'))
                                ->reject(fn ($name) => in_array($name, ['options', 'failed_job_ids']))
                                ->map(fn ($name) => 'job_batches.'.$name)
                                ->all();

            //  Limit the loaded message to the message id and sent sms count to consume less memory
            return $query->select(...$selectedColumns);

        }])->withCount('autoBillingJobBatches')->latest()->paginate(10);

        //  Render view
        return Inertia::render('AutoBillingSubscriptionPlans/List/Main', [
            'projectPayload' => $project,
            'autoBillingSubscriptionPlansPayload' => $autoBillingSubscriptionPlansPayload,
        ]);
    }

    public function showAutoBillingSubscriptionPlanJobBatches(Project $project, SubscriptionPlan $subscriptionPlan)
    {
        //  Get the auto billing subscription plan job batches
        $autoBillingSubscriptionPlanJobBatchesPayload = $subscriptionPlan->autoBillingJobBatches()->latest()->paginate(10);

        //  Render view
        return Inertia::render('AutoBillingSubscriptionPlans/List/JobBatches/List/Main', [
            'subscriptionPlan' => $subscriptionPlan,
            'autoBillingSubscriptionPlanJobBatchesPayload' => $autoBillingSubscriptionPlanJobBatchesPayload
        ]);
    }
}
