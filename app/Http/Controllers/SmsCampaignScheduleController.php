<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use App\Models\Pivots\SmsCampaignSchedule;
use App\Repositories\SmsCampaignScheduleRepository;

class SmsCampaignScheduleController extends Controller
{
    protected $project;
    protected $smsCampaignSchedule;
    protected $smsCampaignScheduleRepository;

    public function __construct()
    {
        $this->project = Project::findOrFail(request()->route('project'));
        $this->smsCampaignSchedule = request()->route('sms_campaign_schedules')
            ? SmsCampaignSchedule::findOrFail(request()->route('sms_campaign_schedules'))
                ->load(['subscriber', 'smsCampaign.latestSmsCampaignBatchJob'])
            : null;

        $this->smsCampaignScheduleRepository = new SmsCampaignScheduleRepository($this->project, $this->smsCampaignSchedule);
    }

    public function showSmsCampaignSchedules()
    {
        // Get the sms campaign schedules using the repository with the required relationships and pagination
        $smsCampaignSchedules = $this->smsCampaignScheduleRepository->getProjectSmsCampaignSchedules(null,
            ['subscriber', 'smsCampaign.latestSmsCampaignBatchJob'], []
        );

        // Render the sms campaign schedules view
        return Inertia::render('SmsCampaignSchedules/List/Main', [
            'smsCampaignSchedulesPayload' => $smsCampaignSchedules
        ]);
    }

    public function showSmsCampaignSchedule()
    {
        return Inertia::render('SmsCampaignSchedule/List/Main', [
            'smsCampaignSchedule' => $this->smsCampaignSchedule
        ]);
    }
}
