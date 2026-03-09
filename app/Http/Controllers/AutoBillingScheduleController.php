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

        $this->autoBillingScheduleRepository = new AutoBillingScheduleRepository($this->project, $this->autoBillingSchedule);
    }

    public function showAutoBillingSchedules()
    {
        $isInertiaRequest = request()->header('X-Inertia');

        if ($isInertiaRequest) {
            $autoBillingSchedules = $this->autoBillingScheduleRepository->getProjectAutoBillingSchedules(null,
                ['subscriber.latestAutoBillingTransaction', 'pricingPlan.autoBillingReminders'], []
            );
            $autoBillingProgress = $this->autoBillingScheduleRepository->getAutoBillingProgress();

            return Inertia::render('AutoBillingSchedules/List/Main', [
                'deferredSchedules'           => false,
                'autoBillingSchedulesPayload' => $autoBillingSchedules,
                'autoBillingProgress'         => $autoBillingProgress,
            ]);
        }

        // Full page load (e.g. browser refresh): defer heavy data to avoid 502 (timeout/OOM).
        // Frontend will request these props via partial reload once the page has loaded.
        return Inertia::render('AutoBillingSchedules/List/Main', [
            'deferredSchedules'           => true,
            'autoBillingSchedulesPayload' => Inertia::lazy(fn () => $this->autoBillingScheduleRepository->getProjectAutoBillingSchedules(null,
                ['subscriber.latestAutoBillingTransaction', 'pricingPlan.autoBillingReminders'], []
            )),
            'autoBillingProgress' => Inertia::lazy(fn () => $this->autoBillingScheduleRepository->getAutoBillingProgress()),
        ]);
    }

    public function showAutoBillingSchedule()
    {
        return Inertia::render('AutoBillingSchedule/List/Main', [
            'autoBillingSchedule' => $this->autoBillingSchedule
        ]);
    }
}
