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
        $this->autoBillingSchedule = request()->route('auto_billing_schedule') ? AutoBillingSchedule::findOrFail(request()->route('auto_billing_schedule'))->load(['subscriber.latestAutoBillingTransaction', 'subscriptionPlan.autoBillingReminders']) : null;

        $this->autoBillingScheduleRepository = new AutoBillingScheduleRepository($this->project, $this->autoBillingSchedule);
    }

    public function showAutoBillingSchedules()
    {
        // Get the auto billing schedules using the repository with the required relationships and pagination
        $autoBillingSchedules = $this->autoBillingScheduleRepository->getProjectAutoBillingSchedules(null,
            ['subscriber.latestAutoBillingTransaction', 'subscriptionPlan.autoBillingReminders'], []
        );

        // Render the auto billing schedules view
        return Inertia::render('AutoBillingSchedules/List/Main', [
            'autoBillingSchedulesPayload' => $autoBillingSchedules
        ]);
    }

    public function showAutoBillingSchedule()
    {
        return Inertia::render('AutoBillingSchedule/List/Main', [
            'autoBillingSchedule' => $this->autoBillingSchedule
        ]);
    }
}
