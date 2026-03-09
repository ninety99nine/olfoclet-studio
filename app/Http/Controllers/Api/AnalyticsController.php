<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    /**
     * Return overview KPIs for the project analytics dashboard.
     */
    public function overview(Request $request, Project $project): JsonResponse
    {
        $range = $request->input('range', '30d');
        $start = $this->rangeStart($range, $request->input('start'), $request->input('end'));
        $end = $this->rangeEnd($range, $request->input('start'), $request->input('end'));
        if ($range === 'today' && ! $end) {
            $end = now()->endOfDay()->toDateTimeString();
        }
        if (in_array($range, ['7d', '30d', '90d', 'ytd'], true) && ! $end) {
            $end = now()->toDateTimeString();
        }

        $totalSubscribers = $project->subscribers()->count();
        $newSubscribersInPeriod = 0;
        if ($start || $end) {
            $q = $project->subscribers();
            if ($start) {
                $q = $q->where('created_at', '>=', $start);
            }
            if ($end) {
                $q = $q->where('created_at', '<=', $end);
            }
            $newSubscribersInPeriod = $q->count();
        }

        $totalSubscriptions = $project->subscriptions()->count();
        $activeSubscriptions = $project->subscriptions()->active()->count();
        $totalMessages = $project->messages()->count();
        $totalPricingPlans = $project->pricingPlans()->count();
        $totalSmsCampaigns = $project->smsCampaigns()->count();

        $totalTransactions = $project->billingTransactions()->count();
        $successfulTransactions = $project->billingTransactions()->successful()->count();
        $unsuccessfulTransactions = $totalTransactions - $successfulTransactions;
        $totalSubscriberMessages = $project->subscriberMessages()->count();
        $successfulSubscriberMessages = $project->subscriberMessages()->where('is_successful', true)->count();

        // Freemium vs paid: active subscriptions by plan price (free = 0 or null)
        $activeFreeSubscriptions = $project->subscriptions()->active()
            ->whereHas('pricingPlan', fn ($q) => $q->whereNull('price')->orWhere('price', 0))
            ->count();
        $activePaidSubscriptions = $project->subscriptions()->active()
            ->whereHas('pricingPlan', fn ($q) => $q->where('price', '>', 0))
            ->count();

        // Subscribers with at least one active subscription
        $subscribersWithActiveSub = $project->subscribers()
            ->whereHas('subscriptions', fn ($q) => $q->where('start_at', '<=', now())->where('end_at', '>', now())->whereNull('cancelled_at'))
            ->count();

        // Subscribers who have ever had a successful payment (paid users)
        $paidSubscribers = (int) $project->billingTransactions()
            ->successful()
            ->selectRaw('COUNT(DISTINCT subscriber_id) as c')
            ->value('c');

        // Total revenue from successful transactions (raw amount sum)
        $totalRevenue = (float) $project->billingTransactions()->successful()->sum('amount');

        $messageDeliveryRate = $totalSubscriberMessages > 0
            ? round(100 * $successfulSubscriberMessages / $totalSubscriberMessages, 1)
            : null;
        $transactionSuccessRate = $totalTransactions > 0
            ? round(100 * $successfulTransactions / $totalTransactions, 1)
            : null;

        return response()->json([
            'total_subscribers' => $totalSubscribers,
            'new_subscribers' => $newSubscribersInPeriod,
            'subscribers_with_active_subscription' => $subscribersWithActiveSub,
            'paid_subscribers' => $paidSubscribers,
            'total_subscriptions' => $totalSubscriptions,
            'active_subscriptions' => $activeSubscriptions,
            'active_free_subscriptions' => $activeFreeSubscriptions,
            'active_paid_subscriptions' => $activePaidSubscriptions,
            'total_messages' => $totalMessages,
            'total_pricing_plans' => $totalPricingPlans,
            'total_sms_campaigns' => $totalSmsCampaigns,
            'total_transactions' => $totalTransactions,
            'successful_transactions' => $successfulTransactions,
            'unsuccessful_transactions' => $unsuccessfulTransactions,
            'total_revenue' => $totalRevenue,
            'message_delivery_rate' => $messageDeliveryRate,
            'transaction_success_rate' => $transactionSuccessRate,
            'total_subscriber_messages' => $totalSubscriberMessages,
            'successful_subscriber_messages' => $successfulSubscriberMessages,
        ]);
    }

    /**
     * Return subscribers over time (daily or hourly when range=today) for charts.
     * Fills missing days/hours with 0.
     */
    public function subscribersOverTime(Request $request, Project $project): JsonResponse
    {
        $range = $request->input('range', '30d');
        $start = $this->rangeStart($range, $request->input('start'), $request->input('end'));
        $end = $this->rangeEnd($range, $request->input('start'), $request->input('end'));

        if ($range === 'today') {
            $dayStart = \Carbon\Carbon::parse($start)->startOfDay();
            $dayEnd = \Carbon\Carbon::parse($start)->endOfDay();
            $query = $project->subscribers()
                ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->groupBy('hour')
                ->orderBy('hour');
            $rows = $query->get()->keyBy('hour');
            $labels = [];
            $values = [];
            for ($h = 0; $h < 24; $h++) {
                $labels[] = sprintf('%02d:00', $h);
                $values[] = (int) ($rows->get($h)?->count ?? 0);
            }
            return response()->json(['labels' => $labels, 'values' => $values]);
        }

        $endForSeries = $this->rangeEndForSeries($request);
        $allDates = $this->allDatesBetween($start, $endForSeries);
        $query = $project->subscribers()->selectRaw('DATE(created_at) as date, COUNT(*) as count');
        $query->where('created_at', '>=', $start)->where('created_at', '<=', $endForSeries->endOfDay()->toDateTimeString());
        $rows = $query->groupBy('date')->orderBy('date')->get()->keyBy('date');
        $labels = [];
        $values = [];
        foreach ($allDates as $date) {
            $labels[] = \Carbon\Carbon::parse($date)->format('M j');
            $values[] = (int) ($rows->get($date)?->count ?? 0);
        }
        return response()->json(['labels' => $labels, 'values' => $values]);
    }

    /**
     * Return billing transactions over time (daily or hourly when range=today). Fills missing slots with 0.
     */
    public function transactionsOverTime(Request $request, Project $project): JsonResponse
    {
        $range = $request->input('range', '30d');
        $start = $this->rangeStart($range, $request->input('start'), $request->input('end'));
        $end = $this->rangeEnd($range, $request->input('start'), $request->input('end'));

        if ($range === 'today') {
            $dayStart = \Carbon\Carbon::parse($start)->startOfDay();
            $dayEnd = \Carbon\Carbon::parse($start)->endOfDay();
            $allByHour = $project->billingTransactions()
                ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->groupBy('hour')->orderBy('hour')->get()->keyBy('hour');
            $successByHour = $project->billingTransactions()->successful()
                ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->groupBy('hour')->orderBy('hour')->get()->keyBy('hour');
            $labels = [];
            $valuesAll = [];
            $valuesSuccess = [];
            for ($h = 0; $h < 24; $h++) {
                $labels[] = sprintf('%02d:00', $h);
                $valuesAll[] = (int) ($allByHour->get($h)?->count ?? 0);
                $valuesSuccess[] = (int) ($successByHour->get($h)?->count ?? 0);
            }
            return response()->json(['labels' => $labels, 'values' => $valuesAll, 'values_successful' => $valuesSuccess]);
        }

        $endForSeries = $this->rangeEndForSeries($request);
        $allDates = $this->allDatesBetween($start, $endForSeries);
        $base = $project->billingTransactions()
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $endForSeries->endOfDay()->toDateTimeString());
        $allByDay = (clone $base)->selectRaw('DATE(created_at) as date, COUNT(*) as count')->groupBy('date')->get()->keyBy(fn ($row) => \Carbon\Carbon::parse($row->date)->format('Y-m-d'));
        $successByDay = (clone $base)->successful()->selectRaw('DATE(created_at) as date, COUNT(*) as count')->groupBy('date')->get()->keyBy(fn ($row) => \Carbon\Carbon::parse($row->date)->format('Y-m-d'));
        $labels = [];
        $valuesAll = [];
        $valuesSuccess = [];
        foreach ($allDates as $date) {
            $labels[] = \Carbon\Carbon::parse($date)->format('M j');
            $valuesAll[] = (int) ($allByDay->get($date)?->count ?? 0);
            $valuesSuccess[] = (int) ($successByDay->get($date)?->count ?? 0);
        }
        return response()->json(['labels' => $labels, 'values' => $valuesAll, 'values_successful' => $valuesSuccess]);
    }

    /**
     * Return subscriber messages over time (daily or hourly when range=today). Fills missing slots with 0.
     */
    public function messagesOverTime(Request $request, Project $project): JsonResponse
    {
        $range = $request->input('range', '30d');
        $start = $this->rangeStart($range, $request->input('start'), $request->input('end'));
        $end = $this->rangeEnd($range, $request->input('start'), $request->input('end'));

        if ($range === 'today') {
            $dayStart = \Carbon\Carbon::parse($start)->startOfDay();
            $dayEnd = \Carbon\Carbon::parse($start)->endOfDay();
            $rows = $project->subscriberMessages()
                ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->groupBy('hour')->orderBy('hour')->get()->keyBy('hour');
            $labels = [];
            $values = [];
            for ($h = 0; $h < 24; $h++) {
                $labels[] = sprintf('%02d:00', $h);
                $values[] = (int) ($rows->get($h)?->count ?? 0);
            }
            return response()->json(['labels' => $labels, 'values' => $values]);
        }

        $endForSeries = $this->rangeEndForSeries($request);
        $allDates = $this->allDatesBetween($start, $endForSeries);
        $rows = $project->subscriberMessages()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $endForSeries->endOfDay()->toDateTimeString())
            ->groupBy('date')->get()->keyBy('date');
        $labels = [];
        $values = [];
        foreach ($allDates as $date) {
            $labels[] = \Carbon\Carbon::parse($date)->format('M j');
            $values[] = (int) ($rows->get($date)?->count ?? 0);
        }
        return response()->json(['labels' => $labels, 'values' => $values]);
    }

    /**
     * Freemium vs paid: counts of active subscriptions by plan type.
     */
    public function subscriptionMix(Request $request, Project $project): JsonResponse
    {
        $free = $project->subscriptions()->active()
            ->whereHas('pricingPlan', fn ($q) => $q->whereNull('price')->orWhere('price', 0))
            ->count();
        $paid = $project->subscriptions()->active()
            ->whereHas('pricingPlan', fn ($q) => $q->where('price', '>', 0))
            ->count();

        return response()->json([
            'labels' => ['Free', 'Paid'],
            'values' => [$free, $paid],
        ]);
    }

    /**
     * Active subscriptions grouped by pricing plan (top 10).
     */
    public function subscriptionsByPlan(Request $request, Project $project): JsonResponse
    {
        $rows = $project->subscriptions()->active()
            ->join('pricing_plans', 'subscriptions.pricing_plan_id', '=', 'pricing_plans.id')
            ->selectRaw('pricing_plans.name as plan_name, COUNT(*) as count')
            ->groupBy('pricing_plans.id', 'pricing_plans.name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        return response()->json([
            'labels' => $rows->pluck('plan_name')->toArray(),
            'values' => $rows->pluck('count')->map(fn ($c) => (int) $c)->toArray(),
        ]);
    }

    /**
     * Revenue over time (daily or hourly when range=today). Fills missing slots with 0.
     */
    public function revenueOverTime(Request $request, Project $project): JsonResponse
    {
        $range = $request->input('range', '30d');
        $start = $this->rangeStart($range, $request->input('start'), $request->input('end'));
        $end = $this->rangeEnd($range, $request->input('start'), $request->input('end'));

        if ($range === 'today') {
            $dayStart = \Carbon\Carbon::parse($start)->startOfDay();
            $dayEnd = \Carbon\Carbon::parse($start)->endOfDay();
            $rows = $project->billingTransactions()->successful()
                ->selectRaw('HOUR(created_at) as hour, SUM(amount) as total')
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->groupBy('hour')->orderBy('hour')->get()->keyBy('hour');
            $labels = [];
            $values = [];
            for ($h = 0; $h < 24; $h++) {
                $labels[] = sprintf('%02d:00', $h);
                $values[] = round((float) ($rows->get($h)?->total ?? 0), 2);
            }
            return response()->json(['labels' => $labels, 'values' => $values]);
        }

        $endForSeries = $this->rangeEndForSeries($request);
        $allDates = $this->allDatesBetween($start, $endForSeries);
        $rows = $project->billingTransactions()->successful()
            ->selectRaw('DATE(created_at) as date, SUM(amount) as total')
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $endForSeries->endOfDay()->toDateTimeString())
            ->groupBy('date')->get()->keyBy(fn ($row) => \Carbon\Carbon::parse($row->date)->format('Y-m-d'));
        $labels = [];
        $values = [];
        foreach ($allDates as $date) {
            $labels[] = \Carbon\Carbon::parse($date)->format('M j');
            $values[] = round((float) ($rows->get($date)?->total ?? 0), 2);
        }
        return response()->json(['labels' => $labels, 'values' => $values]);
    }

    /**
     * New subscriptions over time (daily or hourly when range=today). Fills missing slots with 0.
     */
    public function subscriptionsOverTime(Request $request, Project $project): JsonResponse
    {
        $range = $request->input('range', '30d');
        $start = $this->rangeStart($range, $request->input('start'), $request->input('end'));
        $end = $this->rangeEnd($range, $request->input('start'), $request->input('end'));

        if ($range === 'today') {
            $dayStart = \Carbon\Carbon::parse($start)->startOfDay();
            $dayEnd = \Carbon\Carbon::parse($start)->endOfDay();
            $rows = $project->subscriptions()
                ->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                ->whereBetween('created_at', [$dayStart, $dayEnd])
                ->groupBy('hour')->orderBy('hour')->get()->keyBy('hour');
            $labels = [];
            $values = [];
            for ($h = 0; $h < 24; $h++) {
                $labels[] = sprintf('%02d:00', $h);
                $values[] = (int) ($rows->get($h)?->count ?? 0);
            }
            return response()->json(['labels' => $labels, 'values' => $values]);
        }

        $endForSeries = $this->rangeEndForSeries($request);
        $allDates = $this->allDatesBetween($start, $endForSeries);
        $rows = $project->subscriptions()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', $start)
            ->where('created_at', '<=', $endForSeries->endOfDay()->toDateTimeString())
            ->groupBy('date')->get()->keyBy('date');
        $labels = [];
        $values = [];
        foreach ($allDates as $date) {
            $labels[] = \Carbon\Carbon::parse($date)->format('M j');
            $values[] = (int) ($rows->get($date)?->count ?? 0);
        }
        return response()->json(['labels' => $labels, 'values' => $values]);
    }

    private function rangeStart(string $range, ?string $start = null, ?string $end = null): ?string
    {
        if ($range === 'custom' && $start && $end) {
            return $start;
        }
        $now = now();
        return match ($range) {
            'today' => $now->copy()->startOfDay()->toDateTimeString(),
            '7d' => $now->copy()->subDays(7)->toDateTimeString(),
            '30d' => $now->copy()->subDays(30)->toDateTimeString(),
            '90d' => $now->copy()->subDays(90)->toDateTimeString(),
            'ytd' => $now->copy()->startOfYear()->toDateTimeString(),
            default => $now->copy()->subDays(30)->toDateTimeString(),
        };
    }

    private function rangeEnd(string $range, ?string $start = null, ?string $end = null): ?string
    {
        if ($range === 'custom' && $start && $end) {
            return \Carbon\Carbon::parse($end)->endOfDay()->toDateTimeString();
        }
        return null;
    }

    private function rangeEndForSeries(Request $request): \Carbon\Carbon
    {
        $range = $request->input('range', '30d');
        $start = $request->input('start');
        $end = $request->input('end');
        if ($range === 'custom' && $start && $end) {
            return \Carbon\Carbon::parse($end)->endOfDay();
        }
        if ($range === 'today') {
            return now()->endOfDay();
        }
        return now();
    }

    /**
     * @return list<string> Date strings Y-m-d from start through end (inclusive).
     */
    private function allDatesBetween(string $start, \Carbon\Carbon $end): array
    {
        $startDate = \Carbon\Carbon::parse($start)->startOfDay();
        $endDate = $end->copy()->startOfDay();
        if ($startDate->gt($endDate)) {
            return [];
        }
        $dates = [];
        $cursor = $startDate->copy();
        while ($cursor->lte($endDate)) {
            $dates[] = $cursor->format('Y-m-d');
            $cursor->addDay();
        }
        return $dates;
    }
}
