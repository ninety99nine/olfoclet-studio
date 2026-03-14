<template>
    <app-layout title="Auto Billing Schedule">
        <div class="min-h-screen bg-slate-50/50 p-4 lg:p-8 font-sans antialiased text-slate-700">
            <div class="max-w-[1400px] mx-auto space-y-6">
                <!-- Back + breadcrumb -->
                <div class="flex flex-wrap items-center gap-2">
                    <Link
                        :href="route('show.auto.billing.schedules', { project: project?.id })"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 text-xs font-semibold hover:bg-slate-50 hover:border-slate-300 transition-all"
                    >
                        <ArrowLeft :size="14" />
                        Back
                    </Link>
                    <span class="flex items-center gap-2">
                        <ChevronRight :size="14" class="text-slate-300 shrink-0" />
                        <Link
                            :href="route('show.auto.billing.schedules', { project: project?.id })"
                            class="text-indigo-600 hover:text-indigo-700 text-xs font-semibold hover:underline"
                        >
                            Auto Billing Schedules
                        </Link>
                    </span>
                    <span class="flex items-center gap-2">
                        <ChevronRight :size="14" class="text-slate-300 shrink-0" />
                        <span class="text-slate-600 text-xs font-medium truncate max-w-[200px]">
                            Schedule #{{ schedule?.id ?? '—' }}
                        </span>
                    </span>
                </div>

                <!-- Header card -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 lg:p-8">
                        <div class="flex items-start gap-4">
                            <div class="h-14 w-14 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                                <CalendarClock :size="24" class="text-indigo-600" />
                            </div>
                            <div>
                                <h1 class="text-2xl font-black tracking-tight text-indigo-950">
                                    Auto Billing Schedule #{{ schedule?.id ?? '—' }}
                                </h1>
                                <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">
                                    ID {{ schedule?.id ?? '—' }}
                                </p>
                                <p class="mt-2 inline-block">
                                    <AutoBillingEnabledStatusBadge :auto-billing-schedule="schedule" class="scale-90 origin-left" />
                                </p>
                            </div>
                        </div>

                        <!-- Key metrics -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-6 gap-4 mt-8 pt-6 border-t border-slate-100">
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Active</p>
                                <p class="mt-0.5">
                                    <AutoBillingEnabledStatusBadge :auto-billing-schedule="schedule" class="scale-90 origin-left" />
                                </p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Next attempt</p>
                                <p class="text-sm font-bold text-indigo-900 mt-0.5">
                                    {{ schedule?.next_attempt_date ? moment(schedule.next_attempt_date).format('DD MMM YYYY HH:mm') : '—' }}
                                </p>
                                <p v-if="schedule?.next_attempt_date_milli_seconds_left != null && schedule.next_attempt_date_milli_seconds_left > 0" class="text-[10px] text-slate-500 mt-0.5">
                                    <Countdown :time="schedule.next_attempt_date_milli_seconds_left" />
                                </p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Attempts</p>
                                <p class="text-sm font-bold text-indigo-900 mt-0.5">
                                    {{ schedule?.attempt ?? '—' }} / {{ maxAttemptsLabel }}
                                </p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Overall</p>
                                <p class="text-sm font-bold text-slate-800 mt-0.5 tabular-nums">
                                    <span :class="(schedule?.overall_successful_attempts ?? 0) >= 1 ? 'text-emerald-600' : 'text-slate-600'">{{ (schedule?.overall_successful_attempts ?? 0).toLocaleString() }}</span>
                                    <span class="text-slate-400"> ok</span>
                                    <span class="text-slate-300"> · </span>
                                    <span :class="(schedule?.overall_failed_attempts ?? 0) >= 1 ? 'text-rose-600' : 'text-slate-600'">{{ (schedule?.overall_failed_attempts ?? 0).toLocaleString() }}</span>
                                    <span class="text-slate-400"> fail</span>
                                </p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Last tx</p>
                                <p v-if="lastTx?.id" class="mt-0.5">
                                    <BillingTransactionStatusBadge :billing-transaction="lastTx" class="scale-90 origin-left" />
                                </p>
                                <p v-else class="text-sm text-slate-400 mt-0.5">—</p>
                                <p v-if="lastTx?.created_at" class="text-[10px] text-slate-500 mt-0.5">{{ moment(lastTx.created_at).format('DD MMM HH:mm') }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Amount</p>
                                <p v-if="lastTx?.id" class="text-sm font-bold text-indigo-900 mt-0.5">{{ formatMoney(lastTx.amount) }}</p>
                                <p v-else class="text-sm text-slate-400 mt-0.5">—</p>
                            </div>
                        </div>

                        <!-- Pricing plan -->
                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-3">Pricing plan</p>
                            <Link
                                v-if="schedule?.pricing_plan && project?.id"
                                :href="route('show.pricing.plan', { project: project.id, pricing_plan: schedule.pricing_plan.id })"
                                class="block rounded-xl border border-slate-200 bg-slate-50/80 p-4 hover:bg-indigo-50/20 hover:border-indigo-100 transition-all group"
                            >
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div>
                                        <p class="text-sm font-bold text-indigo-950 group-hover:text-indigo-700">{{ schedule.pricing_plan?.name ?? '—' }}</p>
                                        <p class="text-[10px] text-slate-500 mt-0.5">
                                            {{ schedule.pricing_plan?.duration_in_words ?? '—' }} · {{ schedule.pricing_plan?.price != null ? formatMoney(schedule.pricing_plan.price) : '—' }}
                                        </p>
                                        <PricingPlanActiveStatusBadge v-if="schedule.pricing_plan" :pricing-plan="schedule.pricing_plan" class="scale-90 origin-left mt-1" />
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest group-hover:text-indigo-600">View plan →</span>
                                </div>
                            </Link>
                            <p v-else class="text-sm text-slate-400">No pricing plan linked</p>
                        </div>

                        <!-- Reminder timestamps -->
                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-3">Reminder status</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                                <div v-for="h in [72, 48, 24, 12, 6, 1]" :key="h" class="bg-slate-50 rounded-xl p-3 border border-slate-100 flex flex-col items-center">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase">{{ h }}h before</span>
                                    <AutoBillingReminderStatusBadge :hours="h" :auto-billing-schedule="schedule" class="scale-75 origin-center mt-1" />
                                </div>
                            </div>
                        </div>

                        <!-- Reminder sent dates (raw) -->
                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-3">Reminder sent at</p>
                            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-2 text-xs">
                                <div v-if="schedule?.reminded_seventy_two_hours_before_at" class="bg-slate-50 rounded-lg px-3 py-2 border border-slate-100">
                                    <span class="text-slate-500">72h:</span> {{ moment(schedule.reminded_seventy_two_hours_before_at).format('DD MMM HH:mm') }}
                                </div>
                                <div v-if="schedule?.reminded_forty_eight_hours_before_at" class="bg-slate-50 rounded-lg px-3 py-2 border border-slate-100">
                                    <span class="text-slate-500">48h:</span> {{ moment(schedule.reminded_forty_eight_hours_before_at).format('DD MMM HH:mm') }}
                                </div>
                                <div v-if="schedule?.reminded_twenty_four_hours_before_at" class="bg-slate-50 rounded-lg px-3 py-2 border border-slate-100">
                                    <span class="text-slate-500">24h:</span> {{ moment(schedule.reminded_twenty_four_hours_before_at).format('DD MMM HH:mm') }}
                                </div>
                                <div v-if="schedule?.reminded_twelve_hours_before_at" class="bg-slate-50 rounded-lg px-3 py-2 border border-slate-100">
                                    <span class="text-slate-500">12h:</span> {{ moment(schedule.reminded_twelve_hours_before_at).format('DD MMM HH:mm') }}
                                </div>
                                <div v-if="schedule?.reminded_six_hours_before_at" class="bg-slate-50 rounded-lg px-3 py-2 border border-slate-100">
                                    <span class="text-slate-500">6h:</span> {{ moment(schedule.reminded_six_hours_before_at).format('DD MMM HH:mm') }}
                                </div>
                                <div v-if="schedule?.reminded_one_hour_before_at" class="bg-slate-50 rounded-lg px-3 py-2 border border-slate-100">
                                    <span class="text-slate-500">1h:</span> {{ moment(schedule.reminded_one_hour_before_at).format('DD MMM HH:mm') }}
                                </div>
                            </div>
                            <p v-if="!anyReminderSent" class="text-sm text-slate-400 mt-2">No reminders sent yet.</p>
                        </div>

                        <!-- Subscriber -->
                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-3">Subscriber</p>
                            <Link
                                v-if="schedule?.subscriber && project?.id"
                                :href="route('show.subscriber', { project: project.id, subscriber: schedule.subscriber.id })"
                                class="block w-full"
                            >
                                <span class="inline-flex items-start gap-3 p-4 rounded-xl bg-slate-50 border border-slate-100 hover:bg-indigo-50 hover:border-indigo-100 transition-all group w-full">
                                    <div class="h-10 w-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-indigo-600 shrink-0">
                                        <User :size="18" class="text-xs" />
                                    </div>
                                    <div class="text-left min-w-0 flex-1">
                                        <div class="text-sm font-bold text-indigo-950 group-hover:text-indigo-700">{{ schedule.subscriber?.msisdn ?? '—' }}</div>
                                        <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">Subscriber #{{ schedule.subscriber?.id ?? '—' }} · View subscriber →</div>
                                    </div>
                                </span>
                            </Link>
                            <p v-else class="text-sm text-slate-400">No subscriber linked</p>
                        </div>

                        <!-- Last transaction (link to transaction show) -->
                        <div v-if="lastTx?.id && project?.id" class="mt-6 pt-6 border-t border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-3">Last billing transaction</p>
                            <Link
                                :href="route('show.transaction', { project: project.id, billing_transaction: lastTx.id })"
                                class="block rounded-xl border border-slate-200 bg-slate-50/80 p-4 hover:bg-indigo-50/20 hover:border-indigo-100 transition-all group"
                            >
                                <div class="flex flex-wrap items-center justify-between gap-2">
                                    <div class="flex items-center gap-3">
                                        <BillingTransactionStatusBadge :billing-transaction="lastTx" class="scale-90 origin-left" />
                                        <span class="text-sm font-bold text-indigo-950">Transaction #{{ lastTx.id }}</span>
                                        <span class="text-xs text-slate-500">{{ moment(lastTx.created_at).format('DD MMM YYYY HH:mm') }}</span>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest group-hover:text-indigo-600">View transaction →</span>
                                </div>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Auto billing transactions table -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 lg:p-8">
                        <h2 class="text-lg font-black tracking-tight text-indigo-950 mb-1">Auto billing transactions</h2>
                        <p class="text-xs text-slate-500 mb-4">Billing transactions created by this schedule. Click a row to view the transaction.</p>
                        <div v-if="loadingTransactions" class="flex justify-center py-12">
                            <RefreshCw :size="24" class="text-indigo-500 animate-spin" />
                        </div>
                        <template v-else>
                            <table v-if="transactionsData.data?.length" class="w-full border-collapse text-sm">
                                <thead>
                                    <tr class="border-b border-slate-200">
                                        <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">Amount</th>
                                        <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">Plan</th>
                                        <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">Status</th>
                                        <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">Failure reason</th>
                                        <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">Date</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    <tr
                                        v-for="tx in transactionsData.data"
                                        :key="tx.id"
                                        class="hover:bg-indigo-50/20 cursor-pointer transition-colors"
                                        @click="goToTransaction(tx.id)"
                                    >
                                        <td class="py-3 px-2 font-medium text-slate-800">{{ formatAmount(tx) }}</td>
                                        <td class="py-3 px-2 text-slate-600">{{ tx.pricing_plan?.name ?? '—' }}</td>
                                        <td class="py-3 px-2">
                                            <BillingTransactionStatusBadge :billing-transaction="tx" class="scale-90 origin-left" />
                                        </td>
                                        <td class="py-3 px-2 text-slate-600 text-xs max-w-[200px] truncate" :title="tx.failure_reason || (tx.failure_type ? tx.failure_type : null)">
                                            <template v-if="!tx.is_successful && (tx.failure_reason || tx.failure_type)">
                                                {{ tx.failure_reason || tx.failure_type }}
                                            </template>
                                            <span v-else class="text-slate-300">—</span>
                                        </td>
                                        <td class="py-3 px-2 text-slate-500 text-xs">{{ formatDate(tx.created_at) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <p v-else class="text-slate-500 text-center py-8">No auto billing transactions yet.</p>
                            <Pagination
                                v-if="transactionsData.total > 0 && !loadingTransactions"
                                :pagination-payload="transactionsData"
                                :api-mode="true"
                                :min-pages="1"
                                @page-change="fetchTransactions"
                            />
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Partials/Pagination.vue';
import moment from 'moment';
import { ArrowLeft, ChevronRight, User, CalendarClock, RefreshCw } from 'lucide-vue-next';
import { formatMoney } from '@/utils/formatMoney';
import AutoBillingEnabledStatusBadge from '@/Pages/AutoBillingSchedules/List/Partials/AutoBillingEnabledStatusBadge.vue';
import AutoBillingReminderStatusBadge from '@/Pages/AutoBillingSchedules/List/Partials/AutoBillingReminderStatusBadge.vue';
import BillingTransactionStatusBadge from '@/Pages/BillingTransactions/List/Partials/BillingTransactionStatusBadge.vue';
import PricingPlanActiveStatusBadge from '@/Pages/PricingPlans/List/Partials/ActiveStatusBadge.vue';
import Countdown from '@/Partials/Countdown.vue';

export default defineComponent({
    name: 'AutoBillingScheduleShow',
    components: {
        AppLayout,
        Link,
        Pagination,
        AutoBillingEnabledStatusBadge,
        AutoBillingReminderStatusBadge,
        BillingTransactionStatusBadge,
        PricingPlanActiveStatusBadge,
        Countdown,
        ArrowLeft,
        ChevronRight,
        User,
        CalendarClock,
        RefreshCw,
    },
    props: {
        autoBillingSchedule: { type: Object, required: true },
        project: { type: Object, required: true },
    },
    setup(props) {
        const schedule = computed(() => props.autoBillingSchedule ?? {});
        const lastTx = computed(() => schedule.value?.subscriber?.latest_auto_billing_transaction ?? null);
        const maxAttemptsLabel = computed(() => {
            const plan = schedule.value?.pricing_plan;
            if (!plan) return '—';
            return plan.max_auto_billing_attempts === 0 ? '∞' : String(plan.max_auto_billing_attempts);
        });
        const anyReminderSent = computed(() => {
            const s = schedule.value;
            return !!(s?.reminded_one_hour_before_at || s?.reminded_six_hours_before_at || s?.reminded_twelve_hours_before_at
                || s?.reminded_twenty_four_hours_before_at || s?.reminded_forty_eight_hours_before_at || s?.reminded_seventy_two_hours_before_at);
        });
        return { schedule, lastTx, maxAttemptsLabel, anyReminderSent };
    },
    data() {
        return {
            moment,
            loadingTransactions: false,
            transactionsData: { data: [], current_page: 1, last_page: 1, total: 0, from: 0, to: 0, links: [] },
        };
    },
    mounted() {
        this.fetchTransactions(1);
    },
    methods: {
        formatMoney,
        formatAmount(tx) {
            if (!tx) return '—';
            return formatMoney(tx.amount ?? tx);
        },
        formatDate(val) {
            return val ? moment(val).format('DD MMM YYYY HH:mm') : '—';
        },
        async fetchTransactions(page = 1) {
            const projectId = this.project?.id;
            const scheduleId = this.schedule?.id;
            if (!projectId || !scheduleId) return;
            this.loadingTransactions = true;
            try {
                const url = route('auto.billing.schedule.transactions', {
                    project: projectId,
                    auto_billing_schedule: scheduleId,
                });
                const { data } = await window.axios.get(url, { params: { page, per_page: 10 } });
                this.transactionsData = data.data ?? this.transactionsData;
            } catch (_) {
                this.transactionsData = { data: [], current_page: 1, last_page: 1, total: 0, from: 0, to: 0, links: [] };
            } finally {
                this.loadingTransactions = false;
            }
        },
        goToTransaction(billingTransactionId) {
            if (!this.project?.id || !billingTransactionId) return;
            router.visit(route('show.transaction', { project: this.project.id, billing_transaction: billingTransactionId }));
        },
    },
});
</script>
