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

        <!-- Progress bar when runs are in progress; big countdown to next run when idle; empty state when no upcoming runs -->
        <div class="max-w-[1600px] mx-auto mb-6">
            <!-- Running: show progress bar in real time -->
            <div v-if="showProgressBar" class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Auto billing progress</span>
                    <span class="text-sm font-semibold text-slate-700 tabular-nums">
                        {{ (effectiveProgress?.processed ?? 0).toLocaleString() }} of {{ progressDenominator.toLocaleString() }} schedule runs processed
                        <span class="text-indigo-600 font-bold ml-1">{{ progressPercent }}%</span>
                    </span>
                </div>
                <div class="h-3 bg-slate-100 rounded-full overflow-hidden">
                    <div
                        class="h-full bg-indigo-600 rounded-full transition-all duration-500 ease-out min-w-0"
                        :style="{ width: progressPercent + '%' }"
                    />
                </div>
                <div v-if="progressData?.started_at" class="mt-3 pt-3 border-t border-slate-100 flex flex-wrap items-center gap-x-6 gap-y-1 text-xs text-slate-500">
                    <span><span class="font-semibold text-slate-600">Started:</span> {{ formatProgressDate(progressData.started_at) }}</span>
                    <span v-if="progressData?.ended_at"><span class="font-semibold text-slate-600">Ended:</span> {{ formatProgressDate(progressData.ended_at) }}</span>
                    <span v-if="progressData?.duration_seconds != null"><span class="font-semibold text-slate-600">Duration:</span> {{ formatProgressDuration(progressData.duration_seconds) }}</span>
                </div>
            </div>

            <!-- Idle: show big countdown to closest next run -->
            <div v-else-if="showCountdown" class="bg-white border border-slate-200 rounded-2xl shadow-sm p-6">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 mb-4">Next run in</p>
                <VueCountdown
                    :key="progressData.next_run_at || 'next'"
                    :time="countdownMilliseconds"
                    @end="onCountdownEnd"
                    v-slot="{ days, hours, minutes, seconds }"
                >
                    <div class="flex flex-wrap items-end justify-center gap-3 sm:gap-6">
                        <div class="flex flex-col items-center">
                            <span class="text-3xl sm:text-4xl md:text-5xl font-black tabular-nums text-indigo-950">{{ pad(days) }}</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-1">days</span>
                        </div>
                        <span class="text-2xl sm:text-3xl font-bold text-slate-300 pb-2 self-center">:</span>
                        <div class="flex flex-col items-center">
                            <span class="text-3xl sm:text-4xl md:text-5xl font-black tabular-nums text-indigo-950">{{ pad(hours) }}</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-1">hours</span>
                        </div>
                        <span class="text-2xl sm:text-3xl font-bold text-slate-300 pb-2 self-center">:</span>
                        <div class="flex flex-col items-center">
                            <span class="text-3xl sm:text-4xl md:text-5xl font-black tabular-nums text-indigo-950">{{ pad(minutes) }}</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-1">minutes</span>
                        </div>
                        <span class="text-2xl sm:text-3xl font-bold text-slate-300 pb-2 self-center">:</span>
                        <div class="flex flex-col items-center">
                            <span class="text-3xl sm:text-4xl md:text-5xl font-black tabular-nums text-indigo-950">{{ pad(seconds) }}</span>
                            <span class="text-[10px] font-bold uppercase tracking-widest text-slate-500 mt-1">seconds</span>
                        </div>
                    </div>
                </VueCountdown>
            </div>

            <!-- No batches and no upcoming run -->
            <div v-else-if="showProgressOrCountdownArea" class="bg-white border border-slate-200 rounded-2xl shadow-sm p-5">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400">Auto billing</p>
                <p class="text-sm font-medium text-slate-500 mt-1">No upcoming runs scheduled.</p>
            </div>
        </div>

        <div class="max-w-[1600px] mx-auto space-y-4">
            <div class="flex flex-col xl:flex-row items-center justify-between gap-4">
                <div class="flex-grow w-full xl:w-auto flex items-center gap-2">
                    <div class="relative flex-grow max-w-md group">
                        <Search :size="18" class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-300 group-focus-within:text-indigo-500 transition-colors" />
                        <input
                            v-model="searchQuery"
                            type="search"
                            placeholder="MSISDN or ID..."
                            class="w-full bg-white border border-slate-200 rounded-xl pl-11 pr-4 py-3 text-sm font-medium focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-300 transition-all placeholder:text-slate-300 text-slate-700"
                            @input="debouncedSearch"
                        />
                    </div>

                    <button @click="showFilterModal" class="h-11 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-[10px] font-black flex items-center justify-center gap-2 transition-all uppercase tracking-widest">
                        <Filter :size="16" class="text-indigo-500" />
                        <span class="hidden lg:inline">Filters</span>
                        <div v-if="selectedFilterTags.length" class="bg-indigo-600 text-white text-[9px] min-w-[1rem] h-4 px-1 rounded-full flex items-center justify-center">
                            {{ selectedFilterTags.length }}
                        </div>
                    </button>

                    <button @click="showSortModal" class="h-11 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-[10px] font-black flex items-center justify-center gap-2 transition-all uppercase tracking-widest">
                        <ArrowDownWideNarrow :size="16" class="text-indigo-500" />
                        <span class="hidden lg:inline">Sort</span>
                        <div v-if="selectedSortOptions.length" class="bg-indigo-600 text-white text-[9px] h-4 w-4 rounded-full flex items-center justify-center">
                            {{ selectedSortOptions.length }}
                        </div>
                    </button>

                    <button
                        @click="refresh"
                        class="h-11 w-11 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-indigo-500 hover:text-indigo-700 hover:border-indigo-200 transition-all shadow-sm"
                        title="Sync Data"
                    >
                        <span class="inline-flex items-center justify-center w-5 h-5">
                            <RefreshCw :size="18" class="text-base block" :class="{ 'animate-spin-smooth': loading }" />
                        </span>
                    </button>

                    <button
                        v-if="hasActiveFilters"
                        @click="clearAll"
                        class="text-[10px] font-black text-rose-500 hover:text-rose-600 px-2 uppercase tracking-widest transition-colors flex items-center gap-1"
                    >
                        <X :size="10" />
                        Reset
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

            <!-- Active filter & sort tags -->
            <div v-if="selectedFilterTags.length > 0 || selectedSortOptions.length > 0" class="flex flex-wrap items-center gap-2">
                <span
                    v-for="tag in selectedFilterTags"
                    :key="tag.key"
                    class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-full"
                >
                    {{ tag.label }}
                    <button type="button" aria-label="Remove filter" @click="removeFilterTag(tag)" class="ml-2 text-indigo-500 hover:text-indigo-700 font-bold leading-none">×</button>
                </span>
                <span
                    v-for="sort in selectedSortOptions"
                    :key="sort.value"
                    class="inline-flex items-center px-3 py-1.5 bg-amber-50 text-amber-800 text-xs font-semibold rounded-full"
                >
                    {{ sort.label }}
                    <button type="button" aria-label="Remove sort" @click="removeSort(sort)" class="ml-2 text-amber-600 hover:text-amber-800 font-bold leading-none">×</button>
                </span>
                <button type="button" @click="clearAll" class="text-xs font-bold text-slate-500 hover:text-slate-700 underline underline-offset-2">
                    Clear all
                </button>
            </div>

            <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
                <Transition name="content-switch" mode="out-in">
                    <div v-if="loading && scheduleList.length === 0" key="loading" class="py-24 px-8 flex flex-col items-center justify-center text-center">
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-100 border border-slate-200 mb-6">
                            <RefreshCw :size="24" class="text-indigo-500 animate-spin-smooth" />
                        </span>
                        <p class="text-sm font-medium text-slate-500">Loading billing schedules...</p>
                    </div>
                    <div v-else-if="scheduleList.length > 0" key="table-wrapper" class="overflow-x-auto">
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
                                <template v-if="loading && scheduleList.length > 0 && initialLoadComplete">
                                    <tr v-for="i in Math.min(scheduleList.length, 10)" :key="'skeleton-' + i" class="animate-pulse">
                                        <td class="px-6 py-4"><div class="h-10 w-10 rounded-xl bg-slate-100" /></td>
                                        <td class="px-6 py-4"><div class="h-5 w-12 bg-slate-100 rounded" /></td>
                                        <td class="px-6 py-4"><div class="h-4 w-24 bg-slate-100 rounded" /></td>
                                        <td class="px-6 py-4"><div class="h-4 w-16 bg-slate-100 rounded mx-auto" /></td>
                                        <td class="px-6 py-4"><div class="h-4 w-12 bg-slate-100 rounded mx-auto" /></td>
                                        <td class="px-6 py-4"><div class="h-4 w-20 bg-slate-100 rounded" /></td>
                                        <td class="px-6 py-4"><div class="h-4 w-14 bg-slate-100 rounded mx-auto" /></td>
                                        <td class="px-6 py-4"><div class="h-4 w-28 bg-slate-100 rounded mx-auto" /></td>
                                        <td class="px-6 py-4"><div class="h-4 w-32 bg-slate-100 rounded" /></td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr
                                        v-for="row in scheduleList"
                                        :key="row.id"
                                        class="group hover:bg-indigo-50/20 transition-colors cursor-pointer"
                                        @click="goToAutoBillingSchedule(row.id)"
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
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <div v-else key="empty" class="py-24 px-8 flex flex-col items-center justify-center text-center">
                        <div class="h-20 w-20 rounded-3xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 mb-6">
                            <CalendarClock :size="40" class="text-slate-500" />
                        </div>
                        <h3 class="text-lg font-bold text-indigo-950 mb-1">No billing schedules found</h3>
                        <p class="text-sm text-slate-400 max-w-xs">We couldn't find any records matching your current criteria.</p>
                        <button v-if="hasActiveFilters" @click="clearAll" class="mt-6 text-xs font-black text-indigo-600 uppercase tracking-widest hover:text-indigo-700 underline underline-offset-4">
                            Clear all filters
                        </button>
                    </div>
                </Transition>
                <Pagination
                    v-if="showPaginationFooter"
                    :pagination-payload="payload"
                    :api-mode="true"
                    :min-pages="1"
                    @page-change="changePage"
                />
            </div>
        </div>

        <!-- Filter modal: Up for schedule only -->
        <Dialog v-model:visible="filterModalVisible" header="Filters" :modal="true" :dismissableMask="true" :draggable="false" :style="{ width: '360px' }"
            :pt="{
                root: { class: 'rounded-3xl border-none shadow-2xl overflow-hidden' },
                header: { class: 'bg-white px-6 pt-5 pb-0 border-none text-indigo-900 font-black uppercase text-xs tracking-widest' },
                pcCloseButton: { root: { class: 'h-8 w-8 bg-slate-50 text-slate-400 hover:text-rose-500 transition-all !border-0 !border-none shadow-none' } }
            }">
            <div class="pt-4 pb-6 space-y-4">
                <div class="space-y-1">
                    <label class="text-[11px] font-black text-slate-500 uppercase tracking-widest ml-1 block">Schedule status</label>
                    <Select v-model="tempFilters.up_for_schedule" :options="upForScheduleOptions" option-label="label" option-value="value" class="w-full" showClear placeholder="All schedules" />
                </div>
                <div class="space-y-1">
                    <label class="text-[11px] font-black text-slate-500 uppercase tracking-widest ml-1 block">Billing history</label>
                    <Select v-model="tempFilters.billing_history" :options="billingHistoryOptions" option-label="label" option-value="value" class="w-full" showClear placeholder="All" />
                </div>
                <button @click="hideFilterModal" class="w-full py-3.5 mt-1 bg-indigo-600 text-white rounded-2xl font-black text-[10px] uppercase tracking-widest shadow-xl shadow-indigo-100">Apply Filters</button>
            </div>
        </Dialog>

        <!-- Sort modal -->
        <Dialog v-model:visible="sortModalVisible" header="Sort by" :modal="true" :dismissableMask="true" :draggable="false" :style="{ width: '380px' }"
            :pt="{
                root: { class: 'rounded-3xl border-none shadow-2xl overflow-hidden' },
                header: { class: 'bg-white px-6 pt-5 pb-0 border-none text-indigo-900 font-black uppercase text-xs tracking-widest' },
                pcCloseButton: { root: { class: 'h-8 w-8 bg-slate-50 text-slate-400 hover:text-rose-500 transition-all !border-0 !border-none shadow-none' } }
            }">
            <div class="pt-4 pb-6 space-y-1">
                <button
                    v-for="option in sortOptions"
                    :key="option.value"
                    type="button"
                    @click="toggleSort(option)"
                    :class="[
                        'w-full px-4 py-3 rounded-xl text-left text-sm font-medium transition-all flex items-center justify-between',
                        isSortActive(option) ? 'bg-indigo-50 text-indigo-700' : 'hover:bg-slate-50 text-slate-700'
                    ]"
                >
                    <span>{{ option.label }}</span>
                    <Check v-if="isSortActive(option)" :size="18" class="text-indigo-600 shrink-0" />
                </button>
            </div>
            <template v-if="selectedSortOptions.length" #footer>
                <div class="flex gap-2 pt-2">
                    <button type="button" @click="clearSorts" class="flex-1 py-3.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 font-black text-xs uppercase tracking-widest transition-all">Clear sort</button>
                    <button type="button" @click="hideSortModal" class="flex-1 py-3.5 bg-indigo-600 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-indigo-100 hover:bg-indigo-700 transition-all">Done</button>
                </div>
            </template>
        </Dialog>
    </div>
</template>

<script>
import { defineComponent } from 'vue';
import { router } from '@inertiajs/vue3';
import Dialog from 'primevue/dialog';
import Select from 'primevue/select';
import debounce from 'lodash/debounce';
import Pagination from '@/Partials/Pagination.vue';
import moment from 'moment';
import { Search, Filter, RefreshCw, X, ChevronLeft, ChevronRight, Phone, CalendarClock, ArrowDownWideNarrow, Check } from 'lucide-vue-next';
import { formatMoney } from '@/utils/formatMoney';
import AutoBillingEnabledStatusBadge from './AutoBillingEnabledStatusBadge.vue';
import AutoBillingReminderStatusBadge from './AutoBillingReminderStatusBadge.vue';
import BillingTransactionStatusBadge from '@/Pages/BillingTransactions/List/Partials/BillingTransactionStatusBadge.vue';
import PricingPlanActiveStatusBadge from '@/Pages/PricingPlans/List/Partials/ActiveStatusBadge.vue';
import VueCountdown from '@chenfengyuan/vue-countdown';
import Countdown from '@/Partials/Countdown.vue';

export default defineComponent({
    components: {
        Dialog,
        Select,
        Pagination,
        RefreshCw,
        ChevronLeft,
        ChevronRight,
        Phone,
        CalendarClock,
        ArrowDownWideNarrow,
        Check,
        VueCountdown,
        AutoBillingEnabledStatusBadge,
        AutoBillingReminderStatusBadge,
        BillingTransactionStatusBadge,
        PricingPlanActiveStatusBadge,
        Countdown,
    },
    data() {
        return {
            moment,
            loading: false,
            initialLoadComplete: false,
            payload: { data: [], current_page: 1, last_page: 1, total: 0, links: [] },
            progressData: { total_due: 0, processed: 0, total_in_batches: 0 },
            searchQuery: '',
            filters: { up_for_schedule: null, billing_history: null },
            tempFilters: { up_for_schedule: null, billing_history: null },
            filterModalVisible: false,
            sortModalVisible: false,
            selectedSortOptions: [],
            sortOptions: [
                { label: 'Next attempt soonest', value: 'next_attempt_date:asc', group: 'next' },
                { label: 'Next attempt furthest', value: 'next_attempt_date:desc', group: 'next' },
                { label: 'Most attempts', value: 'overall_attempts:desc', group: 'attempts' },
                { label: 'Least attempts', value: 'overall_attempts:asc', group: 'attempts' },
                { label: 'Most successful attempts', value: 'overall_successful_attempts:desc', group: 'successful' },
                { label: 'Least successful attempts', value: 'overall_successful_attempts:asc', group: 'successful' },
                { label: 'Most failed attempts', value: 'overall_failed_attempts:desc', group: 'failed' },
                { label: 'Least failed attempts', value: 'overall_failed_attempts:asc', group: 'failed' },
                { label: 'ID descending', value: 'id:desc', group: 'id' },
                { label: 'ID ascending', value: 'id:asc', group: 'id' },
            ],
            upForScheduleOptions: [
                { label: 'All schedules', value: null },
                { label: 'Up for schedule only', value: true },
            ],
            billingHistoryOptions: [
                { label: 'All', value: null },
                { label: 'Billed before', value: 'billed_before' },
                { label: 'Not yet billed', value: 'not_billed_yet' },
            ],
        };
    },
    computed: {
        /** Show footer only after first load; keep visible during refetch; hide when no data. */
        showPaginationFooter() {
            const hasData = (this.payload?.data?.length ?? 0) > 0 || (this.payload?.total ?? 0) > 0;
            return hasData || (this.initialLoadComplete && this.loading);
        },
        scheduleList() {
            return this.payload?.data ?? [];
        },
        effectiveProgress() {
            return this.progressData;
        },
        progressDenominator() {
            const totalInBatches = this.progressData?.total_in_batches ?? 0;
            const totalDue = this.progressData?.total_due ?? 0;
            return totalInBatches > 0 ? totalInBatches : totalDue;
        },
        progressPercent() {
            const denom = this.progressDenominator;
            if (denom <= 0) return 0;
            const processed = this.progressData?.processed ?? 0;
            return Math.min(100, Math.round((processed / denom) * 100));
        },
        /** True when batches are running → show progress bar */
        showProgressBar() {
            return (this.progressData?.total_in_batches ?? 0) > 0;
        },
        /** Milliseconds from now until next_run_at (for countdown); 0 if in past or missing */
        countdownMilliseconds() {
            const next = this.progressData?.next_run_at;
            if (!next) return 0;
            const ms = new Date(next).getTime() - Date.now();
            return Math.max(0, ms);
        },
        /** True when idle and we have a future next run → show big countdown */
        showCountdown() {
            return !this.showProgressBar && !!this.progressData?.next_run_at && this.countdownMilliseconds > 0;
        },
        /** True when we should show the "no upcoming runs" card */
        showProgressOrCountdownArea() {
            return !this.showProgressBar && !this.showCountdown;
        },
        hasActiveFilters() {
            return this.searchQuery.trim().length > 0 ||
                this.filters.up_for_schedule === true ||
                (this.filters.billing_history != null && this.filters.billing_history !== '') ||
                this.selectedSortOptions.length > 0;
        },
        selectedFilterTags() {
            const tags = [];
            if (this.filters.up_for_schedule === true) {
                tags.push({ key: 'up_for_schedule', label: 'Up for schedule only' });
            }
            if (this.filters.billing_history === 'billed_before') {
                tags.push({ key: 'billing_history', label: 'Billed before' });
            }
            if (this.filters.billing_history === 'not_billed_yet') {
                tags.push({ key: 'billing_history', label: 'Not yet billed' });
            }
            return tags;
        },
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
    watch: {
        loading(val) {
            if (!val && (this.scheduleList?.length ?? 0) > 0) this.initialLoadComplete = true;
        },
        'payload.data': {
            handler(data) {
                if (!this.loading && data && data.length > 0) this.initialLoadComplete = true;
            },
            deep: true,
        },
    },
    created() {
        this.debouncedSearch = debounce(() => this.fetchSchedules(1), 400);
        this.fetchSchedules(1);
    },
    methods: {
        formatMoney,
        getLatestTx(row) {
            return row?.subscriber?.latest_auto_billing_transaction ?? {};
        },
        fetchSchedules(page = 1) {
            const project = route().params?.project;
            if (!project) return;
            const minSpinMs = 800;
            const startedAt = Date.now();
            this.loading = true;
            const sortParam = this.selectedSortOptions.length > 0 ? this.selectedSortOptions[0].value : undefined;
            const params = {
                page,
                per_page: 15,
                msisdn: this.searchQuery.trim() || undefined,
                up_for_schedule: this.filters.up_for_schedule === true ? true : undefined,
                billing_history: this.filters.billing_history || undefined,
                ...(sortParam ? { sort: sortParam } : {}),
            };
            const stopSpinner = () => {
                const elapsed = Date.now() - startedAt;
                const delay = Math.max(0, minSpinMs - elapsed);
                if (delay > 0) setTimeout(() => { this.loading = false; }, delay);
                else this.loading = false;
            };
            window.axios.get(route('show.auto.billing.schedules', { project }), { params })
                .then(({ data }) => {
                    this.payload = data.autoBillingSchedulesPayload ?? this.payload;
                    this.progressData = data.autoBillingProgress ?? this.progressData;
                })
                .finally(stopSpinner);
        },
        refresh() {
            this.fetchSchedules(this.payload.current_page || 1);
        },
        pad(n) {
            return String(n).padStart(2, '0');
        },
        formatProgressDate(isoString) {
            return isoString ? moment(isoString).format('DD MMM YYYY, HH:mm') : '';
        },
        formatProgressDuration(seconds) {
            if (seconds == null || seconds < 0) return '';
            const h = Math.floor(seconds / 3600);
            const m = Math.floor((seconds % 3600) / 60);
            const s = seconds % 60;
            const parts = [];
            if (h > 0) parts.push(`${h}h`);
            if (m > 0) parts.push(`${m}m`);
            if (s > 0 || parts.length === 0) parts.push(`${s}s`);
            return parts.join(' ');
        },
        onCountdownEnd() {
            this.fetchSchedules(this.payload.current_page || 1);
        },
        changePage(page) {
            this.fetchSchedules(page);
        },
        clearAll() {
            this.searchQuery = '';
            this.filters.up_for_schedule = null;
            this.filters.billing_history = null;
            this.tempFilters.up_for_schedule = null;
            this.tempFilters.billing_history = null;
            this.selectedSortOptions = [];
            this.fetchSchedules(1);
        },
        showFilterModal() {
            this.tempFilters = { ...this.filters };
            this.filterModalVisible = true;
        },
        hideFilterModal() {
            this.filters = { ...this.filters, ...this.tempFilters };
            this.filterModalVisible = false;
            this.fetchSchedules(1);
        },
        removeFilterTag(tag) {
            if (tag.key === 'up_for_schedule') {
                this.filters.up_for_schedule = null;
                this.tempFilters.up_for_schedule = null;
            }
            if (tag.key === 'billing_history') {
                this.filters.billing_history = null;
                this.tempFilters.billing_history = null;
            }
            this.fetchSchedules(1);
        },
        showSortModal() {
            this.sortModalVisible = true;
        },
        hideSortModal() {
            this.sortModalVisible = false;
        },
        isSortActive(option) {
            return this.selectedSortOptions.some(s => s.value === option.value);
        },
        toggleSort(option) {
            const existingInGroupIndex = this.selectedSortOptions.findIndex(s => s.group === option.group);
            if (existingInGroupIndex !== -1) {
                const existing = this.selectedSortOptions[existingInGroupIndex];
                if (existing.value === option.value) {
                    this.selectedSortOptions.splice(existingInGroupIndex, 1);
                } else {
                    this.selectedSortOptions.splice(existingInGroupIndex, 1, { ...option });
                }
            } else {
                this.selectedSortOptions.push({ ...option });
            }
            this.fetchSchedules(1);
        },
        removeSort(sort) {
            this.selectedSortOptions = this.selectedSortOptions.filter(s => s.value !== sort.value);
            this.fetchSchedules(1);
        },
        clearSorts() {
            this.selectedSortOptions = [];
            this.hideSortModal();
            this.fetchSchedules(1);
        },
        goToAutoBillingSchedule(scheduleId) {
            const project = route().params?.project;
            if (!project || !scheduleId) return;
            router.visit(route('show.auto.billing.schedule', { project, auto_billing_schedule: scheduleId }));
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
:deep(.p-select) {
    @apply h-11 bg-slate-50 border-slate-100 rounded-xl transition-all shadow-none ring-0;
}
:deep(.p-select:not(.p-disabled).p-focus) {
    @apply border-indigo-400 bg-white;
}
:deep(.p-select-label) {
    @apply text-[10px] font-bold uppercase tracking-widest text-indigo-950 py-3.5 px-4;
}
</style>
