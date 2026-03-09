<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use App\Models\Pivots\AutoBillingSchedule;
use App\Repositories\AutoBillingScheduleRepository;

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

    public function showAutoBillingSchedules()
    {
        $isInertiaRequest = request()->header('X-Inertia');

        if ($isInertiaRequest) {
            $repo = $this->autoBillingScheduleRepository ?? new AutoBillingScheduleRepository($this->project, null);
            $autoBillingSchedules = $repo->getProjectAutoBillingSchedules(null,
                ['subscriber.latestAutoBillingTransaction', 'pricingPlan.autoBillingReminders'], []
            );
            $autoBillingProgress = $repo->getAutoBillingProgress();

            return Inertia::render('AutoBillingSchedules/List/Main', [
                'deferredSchedules'           => false,
                'autoBillingSchedulesPayload' => $autoBillingSchedules,
                'autoBillingProgress'         => $autoBillingProgress,
            ]);
        }

        // Full page load (e.g. browser refresh): no repository, no heavy queries. Frontend will
        // partial-reload to fetch schedules and progress, avoiding 502 (timeout/OOM).
        return Inertia::render('AutoBillingSchedules/List/Main', [
            'deferredSchedules'           => true,
            'autoBillingSchedulesPayload' => Inertia::lazy(function () {
                return (new AutoBillingScheduleRepository($this->project, null))
                    ->getProjectAutoBillingSchedules(null, ['subscriber.latestAutoBillingTransaction', 'pricingPlan.autoBillingReminders'], []);
            }),
            'autoBillingProgress' => Inertia::lazy(fn () => (new AutoBillingScheduleRepository($this->project, null))->getAutoBillingProgress()),
        ]);
    }

    public function showAutoBillingSchedule()
    {
        return Inertia::render('AutoBillingSchedule/List/Main', [
            'autoBillingSchedule' => $this->autoBillingSchedule
        ]);
    }
}
