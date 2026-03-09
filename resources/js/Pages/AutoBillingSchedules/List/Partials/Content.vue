<template>
    <div class="min-h-screen bg-slate-50/50 p-4 lg:p-8 font-sans antialiased text-slate-700">
        <div class="max-w-[1600px] mx-auto mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h1 class="text-2xl font-black tracking-tight text-indigo-950">Auto Billing Schedules</h1>

                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 block mb-0.5">Total</span>
                        <span class="text-xl font-bold text-indigo-900 tabular-nums leading-none">
                            {{ (payload.total ?? 0).toLocaleString() }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auto billing progress: batch jobs processed (uses total_in_batches so numerator/denominator match) -->
        <div v-if="progressDenominator > 0" class="max-w-[1600px] mx-auto mb-6">
            <div class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Auto billing progress</span>
                    <span class="text-sm font-semibold text-slate-700 tabular-nums">
                        {{ (effectiveProgress?.processed ?? 0).toLocaleString() }} of {{ progressDenominator.toLocaleString() }} subscribers processed
                        <span class="text-indigo-600 font-bold ml-1">{{ progressPercent }}%</span>
                    </span>
                </div>
                <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                    <div
                        class="h-full bg-indigo-600 rounded-full transition-all duration-500 ease-out min-w-0"
                        :style="{ width: progressPercent + '%' }"
                    />
                </div>
            </div>
        </div>

        <div class="max-w-[1600px] mx-auto space-y-4">
            <div class="flex flex-col xl:flex-row items-center justify-between gap-4">
                <div class="flex-grow w-full xl:w-auto flex items-center gap-2">
                    <button
                        @click="refresh"
                        class="h-11 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-[10px] font-black flex items-center justify-center gap-2 transition-all uppercase tracking-widest"
                        title="Sync Data"
                    >
                        <RefreshCw :size="16" class="text-indigo-500" :class="{ 'animate-spin-smooth': loading }" />
                        <span class="hidden lg:inline">Refresh</span>
                    </button>
                </div>

                <div class="flex items-center gap-1">
                    <button
                        v-for="(link, index) in filteredPagination"
                        :key="index"
                        :disabled="!link.page"
                        @click="link.page && changePage(link.page)"
                        class="h-9 min-w-[36px] flex items-center justify-center rounded-lg transition-all font-bold text-[10px]"
                        :class="[
                            link.active
                                ? 'bg-indigo-600 text-white shadow-sm px-3'
                                : link.label === '...' ? 'text-slate-300 cursor-default' : 'bg-transparent text-slate-500 hover:bg-slate-100',
                            !link.page && link.label !== '...' ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer'
                        ]"
                    >
                        <ChevronLeft v-if="link.label === 'prev'" :size="16" />
                        <ChevronRight v-else-if="link.label === 'next'" :size="16" />
                        <span v-else>{{ link.label }}</span>
                    </button>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
                <Transition name="content-switch" mode="out-in">
                    <div v-if="loading" key="loading" class="py-24 px-8 flex flex-col items-center justify-center text-center">
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-100 border border-slate-200 mb-6">
                            <RefreshCw :size="24" class="text-indigo-500 animate-spin-smooth" />
                        </span>
                        <p class="text-sm font-medium text-slate-500">Loading billing schedules...</p>
                    </div>
                    <div v-else-if="payload.data && payload.data.length > 0" key="table-wrapper" class="overflow-x-auto">
                        <table class="w-full min-w-[1280px] border-collapse [&_th]:whitespace-nowrap [&_td]:whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Subscriber</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Active</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Next attempt</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Attempts</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Overall</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Last tx</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Amount</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Reminders</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Plan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr
                                    v-for="row in payload.data"
                                    :key="row.id"
                                    class="group hover:bg-indigo-50/20 transition-colors"
                                >
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-indigo-600 group-hover:border-indigo-100 transition-all">
                                                <Phone :size="14" class="text-xs" />
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-indigo-950">{{ row.subscriber?.msisdn ?? '—' }}</div>
                                                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">#{{ row.id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <AutoBillingEnabledStatusBadge :auto-billing-schedule="row" class="scale-90 origin-left" />
                                    </td>
                                    <td class="px-6 py-4 text-xs text-slate-600 whitespace-normal align-top">
                                        <div class="flex flex-col gap-1">
                                            <span v-if="row.next_attempt_date_milli_seconds_left == null" class="text-slate-400 text-[10px]">—</span>
                                            <Countdown v-else :time="row.next_attempt_date_milli_seconds_left" />
                                            <span class="whitespace-nowrap text-[10px] text-slate-500">{{ row.next_attempt_date ? moment(row.next_attempt_date).format('DD MMM YY HH:mm') : '—' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-center text-xs text-slate-600">
                                        {{ row.attempt ?? '—' }} / {{ row.pricing_plan ? (row.pricing_plan.max_auto_billing_attempts === 0 ? '∞' : row.pricing_plan.max_auto_billing_attempts) : '—' }}
                                    </td>
                                    <td class="px-6 py-4 text-center whitespace-normal align-top">
                                        <div class="flex flex-col items-center gap-0.5">
                                            <span class="text-sm font-bold text-slate-800 tabular-nums">{{ row.overall_attempts ?? '—' }}</span>
                                            <span class="text-[10px] text-slate-500 tabular-nums">
                                                <span :class="(row.overall_successful_attempts ?? 0) >= 1 ? 'text-emerald-600' : 'text-slate-500'">{{ (row.overall_successful_attempts ?? 0).toLocaleString() }}</span>
                                                <span class="text-slate-400"> ok</span>
                                                <span class="text-slate-300"> · </span>
                                                <span :class="(row.overall_failed_attempts ?? 0) >= 1 ? 'text-rose-600' : 'text-slate-500'">{{ (row.overall_failed_attempts ?? 0).toLocaleString() }}</span>
                                                <span class="text-slate-400"> fail</span>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <template v-if="getLatestTx(row).id">
                                            <BillingTransactionStatusBadge :billing-transaction="getLatestTx(row)" class="scale-90 origin-left" />
                                            <p class="text-[10px] text-slate-500 mt-0.5">{{ getLatestTx(row).created_at ? moment(getLatestTx(row).created_at).format('DD MMM HH:mm') : '' }}</p>
                                        </template>
                                        <span v-else class="text-slate-400">—</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span v-if="getLatestTx(row).id" class="text-sm font-bold text-slate-800">{{ formatMoney(getLatestTx(row).amount) }}</span>
                                        <span v-else class="text-slate-400">—</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-nowrap gap-1 justify-center">
                                            <AutoBillingReminderStatusBadge v-for="h in [72, 48, 24, 12, 6, 1]" :key="h" :hours="h" :auto-billing-schedule="row" class="scale-75 origin-center shrink-0" />
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-slate-800">{{ row.pricing_plan?.name ?? '—' }}</div>
                                        <div class="text-[10px] text-slate-500">{{ row.pricing_plan?.duration_in_words ?? '' }} · {{ row.pricing_plan?.price != null ? formatMoney(row.pricing_plan.price) : '' }}</div>
                                        <PricingPlanActiveStatusBadge v-if="row.pricing_plan" :pricing-plan="row.pricing_plan" class="scale-90 origin-left mt-1" />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-else key="empty" class="py-24 px-8 flex flex-col items-center justify-center text-center">
                        <div class="h-20 w-20 rounded-3xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 mb-6">
                            <CalendarClock :size="40" class="text-slate-500" />
                        </div>
                        <h3 class="text-lg font-bold text-indigo-950 mb-1">No billing schedules found</h3>
                        <p class="text-sm text-slate-400 max-w-xs">There are no billing schedules in this project yet.</p>
                    </div>
                </Transition>
                <Pagination
                    :pagination-payload="payload"
                    :update-data="['autoBillingSchedulesPayload']"
                />
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent, computed, ref, onMounted, onBeforeUnmount } from 'vue';
import Pagination from '@/Partials/Pagination.vue';
import { router } from '@inertiajs/vue3';
import moment from 'moment';
import { RefreshCw, ChevronLeft, ChevronRight, Phone, CalendarClock } from 'lucide-vue-next';
import { formatMoney } from '@/utils/formatMoney';
import AutoBillingEnabledStatusBadge from './AutoBillingEnabledStatusBadge.vue';
import AutoBillingReminderStatusBadge from './AutoBillingReminderStatusBadge.vue';
import BillingTransactionStatusBadge from '@/Pages/BillingTransactions/List/Partials/BillingTransactionStatusBadge.vue';
import PricingPlanActiveStatusBadge from '@/Pages/PricingPlans/List/Partials/ActiveStatusBadge.vue';
import Countdown from '@/Partials/Countdown.vue';

export default defineComponent({
    components: {
        Pagination,
        RefreshCw,
        ChevronLeft,
        ChevronRight,
        Phone,
        CalendarClock,
        AutoBillingEnabledStatusBadge,
        AutoBillingReminderStatusBadge,
        BillingTransactionStatusBadge,
        PricingPlanActiveStatusBadge,
        Countdown,
    },
    props: {
        autoBillingSchedulesPayload: { type: Object, default: () => ({ data: [], current_page: 1, last_page: 1, total: 0, links: [] }) },
        autoBillingProgress: { type: Object, default: () => ({ total_due: 0, processed: 0, total_in_batches: 0 }) },
    },
    setup(props) {
        const payload = computed(() => props.autoBillingSchedulesPayload || { data: [], current_page: 1, last_page: 1, total: 0, links: [] });
        const liveProgress = ref(null);
        const effectiveProgress = computed(() => liveProgress.value ?? props.autoBillingProgress ?? { total_due: 0, processed: 0, total_in_batches: 0 });
        const progressDenominator = computed(() => {
            const totalInBatches = effectiveProgress.value?.total_in_batches ?? 0;
            const totalDue = effectiveProgress.value?.total_due ?? 0;
            return totalInBatches > 0 ? totalInBatches : totalDue;
        });
        const progressPercent = computed(() => {
            const denom = progressDenominator.value;
            if (denom <= 0) return 0;
            const processed = effectiveProgress.value?.processed ?? 0;
            return Math.min(100, Math.round((processed / denom) * 100));
        });

        const POLL_INTERVAL_MS = 5000;
        let progressPollTimer = null;

        function fetchProgress() {
            const project = route().params?.project;
            if (!project) return;
            const url = route('auto.billing.schedules.progress', { project });
            window.axios.get(url).then(({ data }) => {
                liveProgress.value = data;
            }).catch(() => {});
        }

        onMounted(() => {
            fetchProgress();
            progressPollTimer = setInterval(fetchProgress, POLL_INTERVAL_MS);
        });

        onBeforeUnmount(() => {
            if (progressPollTimer) {
                clearInterval(progressPollTimer);
                progressPollTimer = null;
            }
        });

        return { payload, effectiveProgress, progressDenominator, progressPercent };
    },
    data() {
        return { moment, loading: false, initialLoadComplete: false };
    },
    watch: {
        loading(val) {
            if (!val && (this.payload?.data?.length ?? 0) > 0) this.initialLoadComplete = true;
        },
        'autoBillingSchedulesPayload.data': {
            handler(data) {
                if (!this.loading && data && data.length > 0) this.initialLoadComplete = true;
            },
            deep: true,
        },
    },
    computed: {
        filteredPagination() {
            const current = this.payload.current_page || 1;
            const last = this.payload.last_page || 1;
            if (last <= 1) return [];
            const pages = [];
            pages.push({ label: 'prev', page: current > 1 ? current - 1 : null });
            pages.push({ label: '1', active: current === 1, page: 1 });
            if (current > 3) pages.push({ label: '...', active: false, page: null });
            const start = Math.max(2, current - 1);
            const end = Math.min(last - 1, current + 1);
            for (let i = start; i <= end; i++) if (i !== 1 && i !== last) pages.push({ label: i.toString(), active: current === i, page: i });
            if (current < last - 2) pages.push({ label: '...', active: false, page: null });
            if (last > 1) pages.push({ label: last.toString(), active: current === last, page: last });
            pages.push({ label: 'next', page: current < last ? current + 1 : null });
            return pages;
        },
    },
    methods: {
        formatMoney,
        getLatestTx(row) {
            return row?.subscriber?.latest_auto_billing_transaction ?? {};
        },
        refresh() {
            this.loading = true;
            this.$inertia.reload({ onFinish: () => { this.loading = false; } });
        },
        changePage(page) {
            const project = route().params.project;
            if (!project) return;
            this.loading = true;
            router.visit(route('show.auto.billing.schedules', { project }), { data: { page }, preserveState: false, onFinish: () => { this.loading = false; } });
        },
    },
});
</script>

<style scoped>
.content-switch-leave-active,
.content-switch-enter-active {
    transition: opacity 0.25s ease, transform 0.25s ease;
}
.content-switch-leave-to,
.content-switch-enter-from {
    opacity: 0;
    transform: translateY(6px);
}
.content-switch-leave-from,
.content-switch-enter-to {
    opacity: 1;
    transform: translateY(0);
}
@keyframes spin-smooth {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}
.animate-spin-smooth {
    animation: spin-smooth 0.8s linear infinite;
    transform-origin: center;
}
</style>
