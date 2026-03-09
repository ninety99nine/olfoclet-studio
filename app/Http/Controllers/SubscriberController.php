<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Project;
use App\Models\Subscriber;
use App\Models\BillingTransaction;
use App\Models\Pivots\AutoBillingSchedule;
use App\Models\Pivots\SmsCampaignSchedule;
use Illuminate\Http\Request;
use App\Repositories\MessageRepository;
use App\Repositories\SubscriberRepository;
use App\Http\Requests\Subscribers\CreateSubscriberRequest;
use App\Http\Requests\Subscribers\ListSubscribersRequest;
use App\Http\Requests\Subscribers\UpdateSubscriberRequest;

class SubscriberController extends Controller
{
    protected $project;
    protected $messageRepository;
    protected $subscriberRepository;

    public function __construct()
    {
        $this->project = Project::findOrFail(request()->route('project'));
        $subscriber = null;
        if (request()->route('subscriber')) {
            $subscriber = $this->project->subscribers()->find(request()->route('subscriber'));
            if ($subscriber === null) {
                abort(404, 'Subscriber not found in this project.');
            }
        }

        $this->messageRepository = new MessageRepository($this->project);
        $this->subscriberRepository = new SubscriberRepository($this->project, $subscriber);
    }

    /**
     * Return filters array from validated request or query (for JSON and Inertia).
     *
     * @param array<string, mixed> $validated
     * @return array<string, mixed>
     */
    private function buildFilters(array $validated): array
    {
        return [
            'msisdn' => $validated['msisdn'] ?? null,
            'status' => $validated['status'] ?? null,
            'billingStatus' => $validated['billingStatus'] ?? null,
            'autoBillingStatus' => $validated['autoBillingStatus'] ?? null,
            'spendStatus' => $validated['spendStatus'] ?? null,
            'scheduledBilling' => $validated['scheduledBilling'] ?? null,
            'scheduledSms' => $validated['scheduledSms'] ?? null,
            'cancelledAutoBilling' => $validated['cancelledAutoBilling'] ?? null,
            'date_from' => $validated['date_from'] ?? null,
            'date_to' => $validated['date_to'] ?? null,
            'per_page' => $validated['per_page'] ?? null,
            'sort' => $validated['sort'] ?? null,
        ];
    }

    /**
     * Get paginated subscribers (shared logic for Inertia and JSON).
     *
     * @param array<string, mixed> $filters
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    private function getSubscribers(array $filters)
    {
        return $this->subscriberRepository->getProjectSubscribers(
            $filters,
            ['latestSubscription.pricingPlan', 'latestUserBillingTransaction', 'latestAutoBillingTransaction'],
            [
                'messages', 'subscriptions',
                'userBillingTransactions', 'successfulUserBillingTransactions', 'unsuccessfulUserBillingTransactions',
                'autoBillingTransactions', 'successfulAutoBillingTransactions', 'unsuccessfulAutoBillingTransactions',
                'messagesAsContent', 'messagesAsPaymentConfirmation', 'messagesAsAutoBillingReminder'
            ]
        );
    }

    /**
     * Attach next_auto_billing and next_sms to each subscriber in the list.
     *
     * @param array<int, Subscriber> $subscribers
     * @return void
     */
    private function attachNextSchedulesToSubscribers(array $subscribers): void
    {
        $subscriberIds = array_map(fn (Subscriber $s) => $s->id, $subscribers);
        if (empty($subscriberIds)) {
            return;
        }

        $now = now();
        $nextAutoBillings = AutoBillingSchedule::whereIn('subscriber_id', $subscriberIds)
            ->where('auto_billing_enabled', true)
            ->where('next_attempt_date', '>', $now)
            ->with('pricingPlan:id,name')
            ->orderBy('next_attempt_date')
            ->get()
            ->groupBy('subscriber_id')
            ->map(fn ($rows) => $rows->first());

        $nextSmsSchedules = SmsCampaignSchedule::whereIn('subscriber_id', $subscriberIds)
            ->where('next_message_date', '>', $now)
            ->with('smsCampaign:id,name')
            ->orderBy('next_message_date')
            ->get()
            ->groupBy('subscriber_id')
            ->map(fn ($rows) => $rows->first());

        $subscriberIdsWithCancelledAutoBilling = AutoBillingSchedule::whereIn('subscriber_id', $subscriberIds)
            ->where('auto_billing_enabled', false)
            ->distinct()
            ->pluck('subscriber_id')
            ->flip()
            ->all();

        foreach ($subscribers as $subscriber) {
            $subscriber->next_auto_billing = isset($nextAutoBillings[$subscriber->id])
                ? [
                    'at' => $nextAutoBillings[$subscriber->id]->next_attempt_date->toIso8601String(),
                    'pricing_plan_name' => $nextAutoBillings[$subscriber->id]->pricingPlan?->name,
                ]
                : null;
            $subscriber->auto_billing_cancelled = isset($subscriberIdsWithCancelledAutoBilling[$subscriber->id])
                && $subscriber->next_auto_billing === null;
            $subscriber->next_sms = isset($nextSmsSchedules[$subscriber->id])
                ? [
                    'at' => $nextSmsSchedules[$subscriber->id]->next_message_date->toIso8601String(),
                    'sms_campaign_name' => $nextSmsSchedules[$subscriber->id]->smsCampaign?->name,
                ]
                : null;
        }
    }

    public function showSubscribers(Request $request)
    {
        $totalMessages = $this->messageRepository->countProjectMessages();

        // JSON request (e.g. from Axios): validate and return paginated list
        if ($request->expectsJson()) {
            $validated = $request->validate((new ListSubscribersRequest())->rules());
            $filters = $this->buildFilters($validated);
            $subscribers = $this->getSubscribers($filters);
            $this->attachNextSchedulesToSubscribers($subscribers->items());

            $payload = $subscribers->toArray();
            $payload['data'] = array_values($payload['data'] ?? []);

            return response()->json([
                'subscribersPayload' => $payload,
                'totalMessages' => $totalMessages,
            ]);
        }

        // Inertia: render shell only; frontend fetches list via Axios
        return Inertia::render('Subscribers/List/Main', [
            'totalMessages' => $totalMessages,
        ]);
    }

    /**
     * Show a single subscriber with overview and counts.
     */
    public function showSubscriber()
    {
        $subscriber = $this->subscriberRepository->getSubscriber();
        if ($subscriber === null) {
            abort(404, 'Subscriber not found.');
        }

        $subscriber->load([
            'latestSubscription.pricingPlan',
            'latestUserBillingTransaction',
            'latestAutoBillingTransaction',
        ]);
        $subscriber->loadCount([
            'subscriptions',
            'messages',
            'userBillingTransactions',
            'autoBillingTransactions',
            'successfulUserBillingTransactions',
            'unsuccessfulUserBillingTransactions',
            'successfulAutoBillingTransactions',
            'unsuccessfulAutoBillingTransactions',
            'topics',
            'autoBillingSchedules',
            'smsCampaigns',
        ]);

        $nextAutoBilling = AutoBillingSchedule::where('subscriber_id', $subscriber->id)
            ->where('auto_billing_enabled', true)
            ->where('next_attempt_date', '>', now())
            ->with('pricingPlan:id,name')
            ->orderBy('next_attempt_date')
            ->first();

        $nextSmsSchedule = SmsCampaignSchedule::where('subscriber_id', $subscriber->id)
            ->where('next_message_date', '>', now())
            ->with('smsCampaign:id,name')
            ->orderBy('next_message_date')
            ->first();

        $scheduleNextAutoBilling = $nextAutoBilling ? [
            'at' => $nextAutoBilling->next_attempt_date->toIso8601String(),
            'pricing_plan_name' => $nextAutoBilling->pricingPlan?->name,
        ] : null;

        $scheduleNextSms = $nextSmsSchedule ? [
            'at' => $nextSmsSchedule->next_message_date->toIso8601String(),
            'sms_campaign_name' => $nextSmsSchedule->smsCampaign?->name,
        ] : null;

        $subscriberArray = $subscriber->toArray();
        $subscriberArray['metadata'] = $subscriber->metadata;

        return Inertia::render('Subscribers/Show/Main', [
            'subscriber' => $subscriberArray,
            'project' => $this->project,
            'scheduleNextAutoBilling' => $scheduleNextAutoBilling,
            'scheduleNextSms' => $scheduleNextSms,
        ]);
    }

    /**
     * Paginated subscriptions for the subscriber (JSON).
     */
    public function subscriberSubscriptions(Request $request)
    {
        $subscriber = $this->subscriberRepository->getSubscriber();
        if ($subscriber === null) {
            abort(404, 'Subscriber not found.');
        }

        $perPage = min((int) $request->get('per_page', 10), 50);
        $payload = $subscriber->subscriptions()
            ->with(['pricingPlan', 'latestBillingTransaction'])
            ->latest()
            ->paginate($perPage);

        return response()->json(['data' => $payload]);
    }

    /**
     * Paginated subscriber messages (JSON).
     */
    public function subscriberMessages(Request $request)
    {
        $subscriber = $this->subscriberRepository->getSubscriber();
        if ($subscriber === null) {
            abort(404, 'Subscriber not found.');
        }

        $perPage = min((int) $request->get('per_page', 10), 50);
        $payload = $subscriber->messages()
            ->with('message')
            ->latest()
            ->paginate($perPage);

        return response()->json(['data' => $payload]);
    }

    /**
     * Paginated billing transactions for the subscriber (JSON).
     */
    public function subscriberBillingTransactions(Request $request)
    {
        $subscriber = $this->subscriberRepository->getSubscriber();
        if ($subscriber === null) {
            abort(404, 'Subscriber not found.');
        }

        $perPage = min((int) $request->get('per_page', 10), 50);
        $payload = BillingTransaction::where('subscriber_id', $subscriber->id)
            ->with(['subscription', 'pricingPlan'])
            ->latest()
            ->paginate($perPage);

        return response()->json(['data' => $payload]);
    }

    /**
     * Paginated topics for the subscriber (JSON).
     */
    public function subscriberTopics(Request $request)
    {
        $subscriber = $this->subscriberRepository->getSubscriber();
        if ($subscriber === null) {
            abort(404, 'Subscriber not found.');
        }

        $perPage = min((int) $request->get('per_page', 10), 50);
        $payload = $subscriber->topics()
            ->orderByPivot('created_at', 'desc')
            ->paginate($perPage);

        return response()->json(['data' => $payload]);
    }

    /**
     * Paginated auto billing schedules for the subscriber (JSON).
     */
    public function subscriberAutoBillingSchedules(Request $request)
    {
        $subscriber = $this->subscriberRepository->getSubscriber();
        if ($subscriber === null) {
            abort(404, 'Subscriber not found.');
        }

        $perPage = min((int) $request->get('per_page', 10), 50);
        $payload = $subscriber->autoBillingSchedules()
            ->with('pricingPlan')
            ->latest()
            ->paginate($perPage);

        return response()->json(['data' => $payload]);
    }

    /**
     * Paginated SMS campaign schedules for the subscriber (JSON).
     */
    public function subscriberSmsCampaigns(Request $request)
    {
        $subscriber = $this->subscriberRepository->getSubscriber();
        if ($subscriber === null) {
            abort(404, 'Subscriber not found.');
        }

        $perPage = min((int) $request->get('per_page', 10), 50);
        $payload = $subscriber->smsCampaigns()
            ->withPivot(SmsCampaignSchedule::VISIBLE_COLUMNS)
            ->latest('sms_campaign_schedules.created_at')
            ->paginate($perPage);

        return response()->json(['data' => $payload]);
    }

    public function createSubscriber(CreateSubscriberRequest $request)
    {
        //  Get the MSISDN
        $msisdn = $request->input('msisdn');

        //  Get the Metadata
        $metadata = $request->input('metadata');

        if(is_string($metadata)) {

            $metadata = json_decode($metadata, true);

        }

        // Create new subscriber using the repository
        $this->subscriberRepository->createProjectSubscriber($msisdn, $metadata);

        return redirect()->back()->with('message', 'Created Successfully');
    }

    public function updateSubscriber(UpdateSubscriberRequest $request)
    {
        //  Get the MSISDN
        $msisdn = $request->input('msisdn');

        //  Get the Metadata
        $metadata = $request->input('metadata');

        if(is_string($metadata)) {

            $metadata = json_decode($metadata, true);

        }

        // Update subscriber using the repository
        $this->subscriberRepository->updateProjectSubscriber($msisdn, $metadata);

        return redirect()->back()->with('message', 'Updated Successfully');
    }

    public function wipeSubscriberMetadata()
    {
        $this->subscriberRepository->wipeSubscriberMetadata();
        return redirect()->back()->with('message', 'Metadata cleared');
    }

    public function deleteSubscriber(Request $request)
    {
        $this->subscriberRepository->deleteProjectSubscriber();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Deleted successfully'], 200);
        }

        return redirect()->back()->with('message', 'Deleted Successfully');
    }
}
