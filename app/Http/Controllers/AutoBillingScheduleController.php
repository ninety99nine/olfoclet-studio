<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\BillingTransaction;
use App\Models\Pivots\AutoBillingSchedule;
use App\Repositories\AutoBillingScheduleRepository;
use App\Http\Requests\AutoBillingSchedules\ListAutoBillingSchedulesRequest;

class AutoBillingScheduleController extends Controller
{
    protected $project;
    protected $autoBillingSchedule;
    protected $autoBillingScheduleRepository;

    public function __construct()
    {
        $this->project = Project::findOrFail(request()->route('project'));
        $this->autoBillingSchedule = request()->route('auto_billing_schedule') ? AutoBillingSchedule::findOrFail(request()->route('auto_billing_schedule'))->load(['subscriber.latestAutoBillingTransaction', 'pricingPlan.autoBillingReminders']) : null;

        // Only create repository when we have a single-schedule route (showAutoBillingSchedule).
        // List page creates the repository on demand to keep full-page load as light as possible.
        if ($this->autoBillingSchedule !== null) {
            $this->autoBillingScheduleRepository = new AutoBillingScheduleRepository($this->project, $this->autoBillingSchedule);
        }
    }

    public function showAutoBillingSchedules(Request $request)
    {
        // JSON request (e.g. from Axios): validate and return paginated list + progress. Inertia page loads send X-Inertia and must get the Inertia response.
        if ($request->expectsJson() && ! $request->header('X-Inertia')) {
            $validated = $request->validate((new ListAutoBillingSchedulesRequest())->rules());
            $filters = [
                'msisdn'          => $validated['msisdn'] ?? null,
                'up_for_schedule' => isset($validated['up_for_schedule']) ? (bool) $validated['up_for_schedule'] : null,
                'billing_history' => $validated['billing_history'] ?? null,
                'sort'            => $validated['sort'] ?? null,
                'per_page'        => $validated['per_page'] ?? null,
                'page'            => $validated['page'] ?? null,
            ];
            $repo = $this->autoBillingScheduleRepository ?? new AutoBillingScheduleRepository($this->project, null);
            $schedules = $repo->getProjectAutoBillingSchedulesFiltered(
                array_filter($filters, fn ($v) => $v !== null && $v !== ''),
                ['subscriber.latestAutoBillingTransaction', 'pricingPlan.autoBillingReminders'],
                []
            );
            $progress = $repo->getAutoBillingProgress();

            return response()->json([
                'autoBillingSchedulesPayload' => $schedules,
                'autoBillingProgress'        => $progress,
            ]);
        }

        // Inertia: render shell only; frontend fetches list via Axios
        return Inertia::render('AutoBillingSchedules/List/Main', []);
    }

    public function showAutoBillingSchedule()
    {
        if ($this->autoBillingSchedule === null) {
            abort(404, 'Auto billing schedule not found.');
        }

        return Inertia::render('AutoBillingSchedule/Show/Main', [
            'autoBillingSchedule' => $this->autoBillingSchedule,
            'project' => $this->project,
        ]);
    }

    /**
     * Paginated auto billing transactions for this schedule (JSON).
     * Returns billing transactions for the schedule's subscriber + pricing plan that were created via auto billing.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function transactions(Request $request)
    {
        if ($this->autoBillingSchedule === null) {
            abort(404, 'Auto billing schedule not found.');
        }

        $perPage = min((int) $request->get('per_page', 10), 50);
        $payload = BillingTransaction::where('subscriber_id', $this->autoBillingSchedule->subscriber_id)
            ->where('pricing_plan_id', $this->autoBillingSchedule->pricing_plan_id)
            ->where('created_using_auto_billing', true)
            ->with(['subscription', 'pricingPlan'])
            ->latest()
            ->paginate($perPage);

        return response()->json(['data' => $payload]);
    }

    /**
     * Return auto billing progress as JSON for real-time polling.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function progress()
    {
        $repo = $this->autoBillingScheduleRepository ?? new AutoBillingScheduleRepository($this->project, null);

        return response()->json($repo->getAutoBillingProgress());
    }
}
