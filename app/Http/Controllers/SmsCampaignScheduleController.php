<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\Pivots\SmsCampaignSchedule;
use App\Repositories\SmsCampaignScheduleRepository;
use App\Http\Requests\SmsCampaignSchedules\ListSmsCampaignSchedulesRequest;

class SmsCampaignScheduleController extends Controller
{
    protected $project;
    protected $smsCampaignSchedule;
    protected $smsCampaignScheduleRepository;

    public function __construct()
    {
        $this->project = Project::findOrFail(request()->route('project'));
        $this->smsCampaignSchedule = request()->route('sms_campaign_schedule')
            ? SmsCampaignSchedule::findOrFail(request()->route('sms_campaign_schedule'))
                ->load(['subscriber', 'smsCampaign.latestSmsCampaignBatchJob'])
            : null;

        $this->smsCampaignScheduleRepository = new SmsCampaignScheduleRepository($this->project, $this->smsCampaignSchedule);
    }

    public function showSmsCampaignSchedules(Request $request)
    {
        // Return list JSON only for Axios list fetch; Inertia page loads send X-Inertia and must get the Inertia response.
        if ($request->expectsJson() && ! $request->header('X-Inertia')) {
            $validated = $request->validate((new ListSmsCampaignSchedulesRequest())->rules());
            $filters = [
                'msisdn'          => $validated['msisdn'] ?? null,
                'up_for_message' => isset($validated['up_for_message']) ? (bool) $validated['up_for_message'] : null,
                'sort'            => $validated['sort'] ?? null,
                'per_page'        => $validated['per_page'] ?? null,
                'page'            => $validated['page'] ?? null,
            ];
            $schedules = $this->smsCampaignScheduleRepository->getProjectSmsCampaignSchedulesFiltered(
                array_filter($filters, fn ($v) => $v !== null && $v !== ''),
                ['subscriber', 'smsCampaign.latestSmsCampaignBatchJob'],
                []
            );

            return response()->json([
                'smsCampaignSchedulesPayload' => $schedules,
            ]);
        }

        return Inertia::render('SmsCampaignSchedules/List/Main', []);
    }

    public function showSmsCampaignSchedule()
    {
        if ($this->smsCampaignSchedule === null) {
            abort(404, 'SMS campaign schedule not found.');
        }

        return Inertia::render('SmsCampaignSchedule/Show/Main', [
            'smsCampaignSchedule' => $this->smsCampaignSchedule,
            'project' => $this->project,
        ]);
    }
}
