<template>
    <div class="bg-slate-50/50 p-4 lg:p-8 font-sans antialiased text-slate-700">
        <div class="max-w-[1600px] mx-auto mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h1 class="text-2xl font-black tracking-tight text-indigo-950">Billing Reports</h1>

                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 block mb-0.5">Total</span>
                        <span class="text-xl font-bold text-indigo-900 tabular-nums leading-none">
                            {{ (billingReportsPayload.total ?? 0).toLocaleString() }}
                        </span>
                    </div>
                </div>
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
                            placeholder="Report name..."
                            class="w-full bg-white border border-slate-200 rounded-xl pl-11 pr-4 py-3 text-sm font-medium focus:ring-2 focus:ring-indigo-500/10 focus:border-indigo-300 transition-all placeholder:text-slate-300 text-slate-700"
                            @input="debouncedSearch"
                        />
                    </div>

                    <button @click="showDateModal" class="h-11 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-[10px] font-black flex items-center justify-center gap-2 transition-all uppercase tracking-widest">
                        <Calendar :size="16" class="text-indigo-500" />
                        <span class="hidden lg:inline">{{ selectedDateLabel }}</span>
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
                        title="Refresh"
                    >
                        <span class="inline-flex items-center justify-center w-5 h-5">
                            <RefreshCw :size="18" class="text-base block" :class="{'animate-spin-smooth': loading}" />
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

            <div v-if="selectedDateOption?.value !== 'all' || selectedSortOptions.length > 0" class="flex flex-wrap items-center gap-2">
                <span
                    v-if="selectedDateOption?.value !== 'all'"
                    class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-full"
                >
                    <template v-if="selectedDateOption?.value === 'custom' && (filters.date_from || filters.date_to)">
                        {{ moment(filters.date_from).format('MMM D') }} – {{ moment(filters.date_to).format('MMM D') }}
                    </template>
                    <template v-else>{{ selectedDateOption?.label }}</template>
                    <button type="button" aria-label="Remove date" @click="selectedDateOption = { value: 'all', label: 'All Time' }; filters.date_from = ''; filters.date_to = ''; fetchBillingReports(1)" class="ml-2 text-indigo-500 hover:text-indigo-700 font-bold leading-none">×</button>
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
                    <div v-if="loading" key="loading" class="py-24 px-8 flex flex-col items-center justify-center text-center">
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-100 border border-slate-200 mb-6">
                            <RefreshCw :size="24" class="text-indigo-500 animate-spin-smooth" />
                        </span>
                        <p class="text-sm font-medium text-slate-500">Loading billing reports...</p>
                    </div>
                    <div v-else-if="billingReportsPayload.data.length > 0" key="table-wrapper" class="overflow-x-auto">
                        <table class="w-full min-w-[1200px] border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Name</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Transactions</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left whitespace-nowrap">Gross Revenue</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left whitespace-nowrap">Costs</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left whitespace-nowrap">Sharable Revenue</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left whitespace-nowrap">Our Share</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left whitespace-nowrap">Their Share</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Created</th>
                                <th class="px-8 py-5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr
                                    v-for="billingReport in billingReportsPayload.data"
                                    :key="billingReport.id"
                                    class="group hover:bg-indigo-50/20 transition-colors cursor-pointer"
                                    @click="openReportDetail(billingReport)"
                                >
                                    <td class="px-8 py-4 whitespace-nowrap">
                                        <span class="text-sm font-bold text-indigo-950">{{ billingReport.name }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-700 tabular-nums">{{ billingReport.total_transactions ?? 0 }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-700">{{ formatMoney(billingReport.gross_revenue) }}</td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm text-slate-700">{{ formatMoney(billingReport.costs) }}</span>
                                            <span
                                                v-if="billingReport.cost_breakdown && Object.keys(billingReport.cost_breakdown).length"
                                                v-tooltip="{
                                                    value: getCostBreakdownTooltipHtml(billingReport),
                                                    escape: false,
                                                    class: 'billing-report-detail-tooltip'
                                                }"
                                                class="inline-flex cursor-pointer text-slate-400 hover:text-indigo-500 transition-colors p-0.5"
                                                @click.stop
                                            >
                                                <Info :size="16" />
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-700">{{ formatMoney(billingReport.sharable_revenue) }}</td>
                                    <td class="px-6 py-4 text-sm font-bold text-slate-800">{{ formatMoney(billingReport.our_share) }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-700">{{ formatMoney(billingReport.their_share) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-xs font-bold text-slate-600">{{ billingReport.created_at ? moment(billingReport.created_at).format('MMM YYYY, HH:mm') : '—' }}</span>
                                    </td>
                                    <td class="px-8 py-4 text-right">
                                        <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                            <button type="button" @click.stop="openReportDetail(billingReport)" class="h-8 px-3 flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-indigo-500 hover:border-indigo-200 transition-all text-xs font-bold">
                                                View
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                        </tbody>
                        </table>
                    </div>

                    <div v-else key="empty" class="py-24 px-8 flex flex-col items-center justify-center text-center">
                        <div class="h-20 w-20 rounded-3xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 mb-6">
                            <FileText :size="40" class="text-slate-500" />
                        </div>
                        <h3 class="text-lg font-bold text-indigo-950 mb-1">No billing reports found</h3>
                        <p class="text-sm text-slate-400 max-w-xs">We couldn't find any records matching your current criteria.</p>
                        <button v-if="hasActiveFilters" @click="clearAll" class="mt-6 text-xs font-black text-indigo-600 uppercase tracking-widest hover:text-indigo-700 underline underline-offset-4">
                            Clear all filters
                        </button>
                    </div>
                </Transition>
                <Pagination
                    v-if="showPaginationFooter"
                    :pagination-payload="billingReportsPayload"
                    :api-mode="true"
                    :min-pages="1"
                    @page-change="changePage"
                />
            </div>
        </div>

        <Dialog v-model:visible="dateModalVisible" header="Timeline" :modal="true" :dismissableMask="true" :draggable="false" :style="{ width: '400px' }"
            :pt="{
                root: { class: 'rounded-3xl border-none shadow-2xl overflow-hidden' },
                header: { class: 'bg-white px-6 pt-6 border-none text-indigo-900 font-black uppercase text-sm tracking-widest' },
                pcCloseButton: { root: { class: 'h-8 w-8 bg-slate-50 text-slate-400 hover:text-rose-500 transition-all !border-0 !border-none shadow-none' } }
            }">
            <div class="pb-8 pt-5 space-y-5">
                <div class="grid grid-cols-2 gap-3 bg-slate-50 p-2 rounded-2xl border border-slate-100">
                    <button v-for="opt in dateFilterOptions" :key="opt.value" @click="applyDateOption(opt)"
                        :class="['py-3 rounded-xl text-xs font-black uppercase tracking-wider transition-all', selectedDateOption?.value === opt.value ? 'bg-white text-indigo-600 shadow-sm border border-slate-100' : 'text-slate-400 hover:text-slate-600']">
                        {{ opt.label }}
                    </button>
                </div>
                <div v-if="selectedDateOption?.value === 'custom'" class="space-y-3 pt-2">
                    <DatePicker v-model="customDateFrom" class="w-full" placeholder="Start Date" />
                    <DatePicker v-model="customDateTo" class="w-full" placeholder="End Date" />
                    <button @click="applyCustomDateAndClose" class="w-full py-3.5 bg-indigo-600 text-white rounded-xl font-black text-xs uppercase tracking-widest shadow-lg shadow-indigo-100">Apply Timeline</button>
                </div>
            </div>
        </Dialog>

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
import Dialog from 'primevue/dialog';
import DatePicker from 'primevue/datepicker';
import Tooltip from 'primevue/tooltip';
import debounce from 'lodash/debounce';
import moment from 'moment';
import { formatMoney } from '@/utils/formatMoney';
import { Search, Calendar, RefreshCw, X, ChevronLeft, ChevronRight, ArrowDownWideNarrow, Check, Info, FileText } from 'lucide-vue-next';
import Pagination from '@/Partials/Pagination.vue';

export default defineComponent({
    directives: { Tooltip },
    components: {
        Dialog,
        Pagination,
        DatePicker,
        Search,
        Calendar,
        RefreshCw,
        X,
        ChevronLeft,
        ChevronRight,
        ArrowDownWideNarrow,
        Check,
        Info,
        FileText,
    },
    props: {
        projectPayload: { type: Object, default: null },
        refetchTrigger: { type: Number, default: 0 },
    },
    data() {
        return {
            moment,
            loading: false,
            fetchRequestId: 0,
            billingReportsPayload: { data: [], current_page: 1, last_page: 1, total: 0, links: [] },
            searchQuery: '',
            filters: { date_from: '', date_to: '' },
            dateModalVisible: false,
            sortModalVisible: false,
            selectedDateOption: { value: 'all', label: 'All Time' },
            selectedSortOptions: [],
            sortOptions: [
                { label: 'Newest first', value: 'created_at:desc', group: 'date' },
                { label: 'Oldest first', value: 'created_at:asc', group: 'date' },
                { label: 'Most revenue', value: 'gross_revenue:desc', group: 'revenue' },
                { label: 'Least revenue', value: 'gross_revenue:asc', group: 'revenue' },
                { label: 'Most transactions', value: 'total_transactions:desc', group: 'transactions' },
                { label: 'Fewest transactions', value: 'total_transactions:asc', group: 'transactions' },
            ],
            customDateFrom: null,
            customDateTo: null,
            dateFilterOptions: [
                { value: 'all', label: 'All Time' },
                { value: 'today', label: 'Today' },
                { value: 'this_week', label: 'Week' },
                { value: 'this_month', label: 'Month' },
                { value: 'this_year', label: 'Year' },
                { value: 'custom', label: 'Custom' },
            ],
            initialLoadComplete: false,
        };
    },
    watch: {
        refetchTrigger() {
            this.fetchBillingReports(this.billingReportsPayload.current_page || 1);
        },
        loading(val) {
            if (!val && (this.billingReportsPayload?.data?.length ?? 0) > 0) this.initialLoadComplete = true;
        },
        'billingReportsPayload.data': {
            handler(data) {
                if (!this.loading && data && data.length > 0) this.initialLoadComplete = true;
            },
            deep: true,
        },
    },
    computed: {
        /** Show footer only after first load; keep visible during refetch; hide when no data. */
        showPaginationFooter() {
            const hasData = (this.billingReportsPayload?.data?.length ?? 0) > 0 || (this.billingReportsPayload?.total ?? 0) > 0;
            return hasData || (this.initialLoadComplete && this.loading);
        },
        hasActiveFilters() {
            const hasTextSearch = this.searchQuery.trim().length > 0;
            const hasDateFilter = this.selectedDateOption?.value !== 'all';
            const hasSort = this.selectedSortOptions.length > 0;
            return hasTextSearch || hasDateFilter || hasSort;
        },
        selectedDateLabel() {
            if (this.selectedDateOption?.value === 'custom' && (this.filters.date_from || this.filters.date_to)) {
                return `${moment(this.filters.date_from).format('MMM D')} - ${moment(this.filters.date_to).format('MMM D')}`;
            }
            return this.selectedDateOption?.label ?? 'All Time';
        },
        filteredPagination() {
            const current = this.billingReportsPayload.current_page;
            const last = this.billingReportsPayload.last_page;
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
        fetchBillingReports(page = 1) {
            this.fetchRequestId += 1;
            const requestId = this.fetchRequestId;
            const hadData = (this.billingReportsPayload?.data?.length ?? 0) > 0;
            const minSpinMs = hadData ? 0 : 400;
            const startedAt = Date.now();
            this.loading = true;
            const url = route('show.billing.reports', { project: route().params.project });
            const sortParam = this.selectedSortOptions.length > 0 ? this.selectedSortOptions[0].value : undefined;
            const params = {
                page,
                per_page: 15,
                search: this.searchQuery.trim() || undefined,
                date_from: this.filters.date_from || undefined,
                date_to: this.filters.date_to || undefined,
                ...(sortParam ? { sort: sortParam } : {}),
            };
            const stopSpinner = () => {
                if (requestId !== this.fetchRequestId) return;
                const elapsed = Date.now() - startedAt;
                const delay = Math.max(0, minSpinMs - elapsed);
                if (delay > 0) setTimeout(() => { if (requestId === this.fetchRequestId) this.loading = false; }, delay);
                else this.loading = false;
            };
            window.axios.get(url, { params }).then(({ data }) => {
                if (requestId !== this.fetchRequestId) return;
                this.billingReportsPayload = data.billingReportsPayload ?? { data: [], current_page: 1, last_page: 1, total: 0, links: [] };
            }).catch(() => {
                if (requestId !== this.fetchRequestId) return;
                this.billingReportsPayload = { data: [], current_page: 1, last_page: 1, total: 0, links: [] };
            }).finally(stopSpinner);
        },
        refresh() {
            this.fetchBillingReports(this.billingReportsPayload.current_page || 1);
        },
        clearAll() {
            this.selectedDateOption = { value: 'all', label: 'All Time' };
            this.filters = { date_from: '', date_to: '' };
            this.selectedSortOptions = [];
            this.searchQuery = '';
            this.fetchBillingReports(1);
        },
        changePage(page) {
            this.fetchBillingReports(page);
        },
        showDateModal() {
            this.dateModalVisible = true;
        },
        showSortModal() {
            this.sortModalVisible = true;
        },
        hideSortModal() {
            this.sortModalVisible = false;
        },
        isSortActive(option) {
            return this.selectedSortOptions.some(s => s.label === option.label);
        },
        toggleSort(option) {
            const existingInGroupIndex = this.selectedSortOptions.findIndex(s => s.group === option.group);
            if (existingInGroupIndex !== -1) {
                const existing = this.selectedSortOptions[existingInGroupIndex];
                if (existing.label === option.label) {
                    this.selectedSortOptions.splice(existingInGroupIndex, 1);
                } else {
                    this.selectedSortOptions.splice(existingInGroupIndex, 1, { ...option });
                }
            } else {
                this.selectedSortOptions.push({ ...option });
            }
            this.fetchBillingReports(1);
        },
        removeSort(sort) {
            this.selectedSortOptions = this.selectedSortOptions.filter(s => s.label !== sort.label);
            this.fetchBillingReports(1);
        },
        clearSorts() {
            this.selectedSortOptions = [];
            this.hideSortModal();
            this.fetchBillingReports(1);
        },
        applyDateOption(opt) {
            this.selectedDateOption = opt;
            if (opt.value === 'all') {
                this.filters.date_from = '';
                this.filters.date_to = '';
                this.dateModalVisible = false;
                this.fetchBillingReports(1);
            } else if (opt.value !== 'custom') {
                const range = this.getDateRangeForPreset(opt.value);
                this.filters.date_from = range.start;
                this.filters.date_to = range.end;
                this.dateModalVisible = false;
                this.fetchBillingReports(1);
            }
        },
        getDateRangeForPreset(p) {
            const m = moment();
            if (p === 'today') return { start: m.format('YYYY-MM-DD'), end: m.format('YYYY-MM-DD') };
            if (p === 'this_week') return { start: m.startOf('week').format('YYYY-MM-DD'), end: m.endOf('week').format('YYYY-MM-DD') };
            if (p === 'this_month') return { start: m.startOf('month').format('YYYY-MM-DD'), end: m.endOf('month').format('YYYY-MM-DD') };
            if (p === 'this_year') return { start: m.startOf('year').format('YYYY-MM-DD'), end: m.endOf('year').format('YYYY-MM-DD') };
            return { start: '', end: '' };
        },
        applyCustomDateAndClose() {
            if (this.customDateFrom) this.filters.date_from = moment(this.customDateFrom).format('YYYY-MM-DD');
            if (this.customDateTo) this.filters.date_to = moment(this.customDateTo).format('YYYY-MM-DD');
            this.dateModalVisible = false;
            this.fetchBillingReports(1);
        },
        getCostBreakdownTooltipHtml(report) {
            if (!report || !report.cost_breakdown || !Object.keys(report.cost_breakdown).length) return '';
            const total = this.formatMoney(report.costs);
            let html = `<div class="detail-tooltip__title">Cost Breakdown</div><div class="detail-tooltip__body">`;
            for (const [costName, costAmount] of Object.entries(report.cost_breakdown)) {
                const amount = this.formatMoney(costAmount);
                html += `<div class="detail-tooltip__row"><span class="detail-tooltip__label">${this.escapeHtml(costName)}</span><span class="detail-tooltip__value">${this.escapeHtml(amount)}</span></div>`;
            }
            html += `<div class="detail-tooltip__row" style="margin-top:8px;padding-top:8px;border-top:1px solid #e2e8f0;font-weight:700;"><span class="detail-tooltip__label">Total</span><span class="detail-tooltip__value">${this.escapeHtml(total)}</span></div>`;
            html += '</div>';
            return html;
        },
        escapeHtml(text) {
            if (text == null) return '';
            const div = document.createElement('div');
            div.textContent = String(text);
            return div.innerHTML;
        },
        openReportDetail(billingReport) {
            if (!billingReport?.id || !this.projectPayload?.id) return;
            this.$inertia.visit(route('show.billing.report', {
                project: this.projectPayload.id,
                billing_report: billingReport.id,
            }));
        },
    },
    created() {
        this.debouncedSearch = debounce(() => this.fetchBillingReports(1), 400);
        this.fetchBillingReports(1);
    },
});
</script>

<style scoped>
.content-switch-leave-active,
.content-switch-enter-active {
    transition: opacity 0.25s ease, transform 0.25s ease;
}
.content-switch-leave-from,
.content-switch-enter-to {
    opacity: 1;
    transform: translateY(0);
}
.content-switch-leave-to,
.content-switch-enter-from {
    opacity: 0;
    transform: translateY(-6px);
}
</style>

<!-- Unscoped: Detail tooltips (report summary & cost breakdown) match subscribers/subscriptions/transactions -->
<style>
.billing-report-detail-tooltip {
    max-width: 380px;
    min-width: 260px;
    padding: 0;
    border-radius: 12px;
    background: #fff !important;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 10px 25px -5px rgba(0, 0, 0, 0.15);
}
.billing-report-detail-tooltip .p-tooltip-arrow {
    display: none;
}
.billing-report-detail-tooltip .p-tooltip-text {
    padding: 14px 16px;
    overflow: hidden;
    max-height: 70vh;
    overflow-y: auto;
    background: transparent !important;
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    text-align: left;
}
.billing-report-detail-tooltip .detail-tooltip__title {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: #1e293b;
    padding-bottom: 10px;
    margin-bottom: 10px;
    border-bottom: 1px solid #e2e8f0;
}
.billing-report-detail-tooltip .detail-tooltip__body {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.billing-report-detail-tooltip .detail-tooltip__row {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 12px;
    font-size: 0.6875rem;
}
.billing-report-detail-tooltip .detail-tooltip__label {
    flex-shrink: 0;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.billing-report-detail-tooltip .detail-tooltip__value {
    color: #334155;
    font-weight: 500;
    word-break: break-word;
    text-align: right;
}
</style>
