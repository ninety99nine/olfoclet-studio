<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use App\Models\Project;
use App\Models\Subscriber;
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
                ['subscriber.latestSubscription.pricingPlan', 'smsCampaign.latestSmsCampaignBatchJob'],
                []
            );

            $project = $this->project;
            $schedules->getCollection()->transform(function (SmsCampaignSchedule $schedule) use ($project) {
                $schedule->setAttribute('subscription_label', $this->scheduleSubscriptionLabel($schedule));
                $schedule->setAttribute('next_message_block_reason', $this->scheduleNextMessageBlockReason($project, $schedule));

                return $schedule;
            });

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

    /**
     * Human-readable subscription status for a schedule's subscriber (for list column).
     */
    private function scheduleSubscriptionLabel(SmsCampaignSchedule $schedule): string
    {
        $subscriber = $schedule->subscriber;
        if (! $subscriber || ! $subscriber->relationLoaded('latestSubscription')) {
            return '—';
        }
        $sub = $subscriber->latestSubscription;
        if (! $sub) {
            return 'No subscription';
        }
        $active = $sub->start_at && $sub->end_at
            && $sub->start_at->lte(Carbon::now())
            && $sub->end_at->gt(Carbon::now())
            && $sub->cancelled_at === null;
        $planName = $sub->relationLoaded('pricingPlan') && $sub->pricingPlan
            ? $sub->pricingPlan->name
            : '—';

        return ($active ? 'Active' : 'Inactive') . ' · ' . $planName;
    }

    /**
     * Reason why the next SMS would not be sent for this schedule (matches StartSmsCampaign logic).
     * Returns null if no block (schedule can receive next message).
     */
    private function scheduleNextMessageBlockReason(Project $project, SmsCampaignSchedule $schedule): ?string
    {
        $campaign = $schedule->smsCampaign;
        $subscriber = $schedule->subscriber;
        if (! $campaign || ! $subscriber) {
            return null;
        }

        if (! $project->hasSmsCredentials()) {
            return 'Project SMS credentials missing';
        }
        if (! $project->can_send_messages) {
            return 'Project SMS disabled';
        }
        if (! $campaign->can_send_messages) {
            return 'Campaign inactive';
        }
        if (! $campaign->canStartSmsCampaign()) {
            return 'Campaign not in send window';
        }
        $pricingPlanIds = $campaign->pricing_plan_ids;
        if (is_array($pricingPlanIds) && ! empty($pricingPlanIds)) {
            $hasActive = Subscriber::where('id', $subscriber->id)
                ->hasActiveNonCancelledSubscription($pricingPlanIds)
                ->exists();
            if (! $hasActive) {
                return 'No active subscription';
            }
        }

        return null;
    }
}
