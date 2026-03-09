<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Project;
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
        // JSON request (e.g. from Axios): validate and return paginated list + progress
        if ($request->expectsJson()) {
            $validated = $request->validate((new ListAutoBillingSchedulesRequest())->rules());
            $filters = [
                'msisdn'          => $validated['msisdn'] ?? null,
                'up_for_schedule' => isset($validated['up_for_schedule']) ? (bool) $validated['up_for_schedule'] : null,
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
        return Inertia::render('AutoBillingSchedule/List/Main', [
            'autoBillingSchedule' => $this->autoBillingSchedule
        ]);
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
