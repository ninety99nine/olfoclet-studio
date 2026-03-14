<template>
    <div class="min-h-screen bg-slate-50/50 p-4 lg:p-8 font-sans antialiased text-slate-700">
        <div class="max-w-[1600px] mx-auto mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h1 class="text-2xl font-black tracking-tight text-indigo-950">Sms Schedules</h1>

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
                        <p class="text-sm font-medium text-slate-500">Loading SMS schedules...</p>
                    </div>
                    <div v-else-if="scheduleList.length > 0" key="table-wrapper" class="overflow-x-auto">
                        <table class="w-full min-w-[900px] border-collapse [&_th]:whitespace-nowrap [&_td]:whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Subscriber</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Next message</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Messages sent</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Subscription</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Campaign</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <template v-if="loading && scheduleList.length > 0 && initialLoadComplete">
                                    <tr v-for="i in Math.min(scheduleList.length, 10)" :key="'skeleton-' + i" class="animate-pulse">
                                        <td class="px-6 py-4"><div class="h-10 w-10 rounded-xl bg-slate-100" /></td>
                                        <td class="px-6 py-4"><div class="h-4 w-24 bg-slate-100 rounded" /></td>
                                        <td class="px-6 py-4"><div class="h-4 w-16 bg-slate-100 rounded mx-auto" /></td>
                                        <td class="px-6 py-4"><div class="h-4 w-20 bg-slate-100 rounded" /></td>
                                        <td class="px-6 py-4"><div class="h-4 w-32 bg-slate-100 rounded" /></td>
                                    </tr>
                                </template>
                                <template v-else>
                                    <tr
                                        v-for="row in scheduleList"
                                        :key="row.id"
                                        class="group hover:bg-indigo-50/20 transition-colors cursor-pointer"
                                        @click="goToSmsCampaignSchedule(row.id)"
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
                                        <td class="px-6 py-4 text-xs text-slate-600 whitespace-normal align-top">
                                            <div class="flex flex-col gap-1">
                                                <span v-if="row.next_message_date_milli_seconds_left == null" class="text-slate-400 text-[10px]">—</span>
                                                <Countdown v-else :time="row.next_message_date_milli_seconds_left" />
                                                <span class="whitespace-nowrap text-[10px] text-slate-500">{{ row.next_message_date ? moment(row.next_message_date).format('DD MMM YY HH:mm') : '—' }}</span>
                                                <p v-if="row.next_message_block_reason" class="text-[10px] text-amber-700 mt-1 font-medium" :title="row.next_message_block_reason">
                                                    {{ row.next_message_block_reason }}
                                                </p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center whitespace-normal align-top">
                                            <div class="flex flex-col items-center gap-0.5">
                                                <span class="text-sm font-bold text-slate-800 tabular-nums">{{ row.attempts ?? '—' }}</span>
                                                <span class="text-[10px] text-slate-500 tabular-nums">
                                                    <span :class="(row.total_successful_attempts ?? 0) >= 1 ? 'text-emerald-600' : 'text-slate-500'">{{ (row.total_successful_attempts ?? 0).toLocaleString() }}</span>
                                                    <span class="text-slate-400"> sent</span>
                                                    <span class="text-slate-300"> · </span>
                                                    <span :class="(row.total_failed_attempts ?? 0) >= 1 ? 'text-rose-600' : 'text-slate-500'">{{ (row.total_failed_attempts ?? 0).toLocaleString() }}</span>
                                                    <span class="text-slate-400"> failed</span>
                                                </span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-xs text-slate-600 whitespace-normal align-top">
                                            <span class="text-slate-700">{{ row.subscription_label ?? '—' }}</span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex flex-col gap-1">
                                                <span class="text-sm font-medium text-slate-800">{{ row.sms_campaign?.name ?? '—' }}</span>
                                                <span class="campaign-status-badge inline-block w-fit">
                                                    <Tag
                                                        :value="row.sms_campaign?.can_send_messages === true ? 'Active' : 'Inactive'"
                                                        :severity="row.sms_campaign?.can_send_messages === true ? 'success' : 'warn'"
                                                        :class="['text-xs scale-90 origin-left', { 'tag-amber': row.sms_campaign?.can_send_messages !== true }]"
                                                    />
                                                </span>
                                            </div>
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
                        <h3 class="text-lg font-bold text-indigo-950 mb-1">No SMS schedules found</h3>
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

        <!-- Filter modal -->
        <Dialog v-model:visible="filterModalVisible" header="Filters" :modal="true" :dismissableMask="true" :draggable="false" :style="{ width: '360px' }"
            :pt="{
                root: { class: 'rounded-3xl border-none shadow-2xl overflow-hidden' },
                header: { class: 'bg-white px-6 pt-5 pb-0 border-none text-indigo-900 font-black uppercase text-xs tracking-widest' },
                pcCloseButton: { root: { class: 'h-8 w-8 bg-slate-50 text-slate-400 hover:text-rose-500 transition-all !border-0 !border-none shadow-none' } }
            }">
            <div class="pt-4 pb-6 space-y-4">
                <div class="space-y-1">
                    <label class="text-[11px] font-black text-slate-500 uppercase tracking-widest ml-1 block">Schedule status</label>
                    <Select v-model="tempFilters.up_for_message" :options="upForMessageOptions" option-label="label" option-value="value" class="w-full" showClear placeholder="All schedules" />
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
import Tag from 'primevue/tag';
import Countdown from '@/Partials/Countdown.vue';

export default defineComponent({
    components: {
        Dialog,
        Select,
        Pagination,
        Tag,
        Countdown,
        RefreshCw,
        ChevronLeft,
        ChevronRight,
        Phone,
        CalendarClock,
        ArrowDownWideNarrow,
        Check,
    },
    data() {
        return {
            moment,
            loading: false,
            initialLoadComplete: false,
            payload: { data: [], current_page: 1, last_page: 1, total: 0, links: [] },
            searchQuery: '',
            filters: { up_for_message: null },
            tempFilters: { up_for_message: null },
            filterModalVisible: false,
            sortModalVisible: false,
            selectedSortOptions: [],
            sortOptions: [
                { label: 'Next message soonest', value: 'next_message_date:asc', group: 'next' },
                { label: 'Next message latest', value: 'next_message_date:desc', group: 'next' },
                { label: 'ID descending', value: 'id:desc', group: 'id' },
                { label: 'ID ascending', value: 'id:asc', group: 'id' },
            ],
            upForMessageOptions: [
                { label: 'All schedules', value: null },
                { label: 'Up for message only', value: true },
            ],
        };
    },
    computed: {
        showPaginationFooter() {
            const hasData = (this.payload?.data?.length ?? 0) > 0 || (this.payload?.total ?? 0) > 0;
            return hasData || (this.initialLoadComplete && this.loading);
        },
        scheduleList() {
            return this.payload?.data ?? [];
        },
        hasActiveFilters() {
            return this.searchQuery.trim().length > 0 ||
                this.filters.up_for_message === true ||
                this.selectedSortOptions.length > 0;
        },
        selectedFilterTags() {
            const tags = [];
            if (this.filters.up_for_message === true) {
                tags.push({ key: 'up_for_message', label: 'Up for message only' });
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
                up_for_message: this.filters.up_for_message === true ? true : undefined,
                ...(sortParam ? { sort: sortParam } : {}),
            };
            const stopSpinner = () => {
                const elapsed = Date.now() - startedAt;
                const delay = Math.max(0, minSpinMs - elapsed);
                if (delay > 0) setTimeout(() => { this.loading = false; }, delay);
                else this.loading = false;
            };
            window.axios.get(route('show.sms.campaign.schedules', { project }), { params })
                .then(({ data }) => {
                    this.payload = data.smsCampaignSchedulesPayload ?? this.payload;
                })
                .finally(stopSpinner);
        },
        refresh() {
            this.fetchSchedules(this.payload.current_page || 1);
        },
        changePage(page) {
            this.fetchSchedules(page);
        },
        clearAll() {
            this.searchQuery = '';
            this.filters.up_for_message = null;
            this.tempFilters.up_for_message = null;
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
            if (tag.key === 'up_for_message') {
                this.filters.up_for_message = null;
                this.tempFilters.up_for_message = null;
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
        goToSmsCampaignSchedule(scheduleId) {
            const project = route().params?.project;
            if (!project || !scheduleId) return;
            router.visit(route('show.sms.campaign.schedule', { project, sms_campaign_schedule: scheduleId }));
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
.campaign-status-badge :deep(.p-tag) {
    width: fit-content;
    min-width: 0;
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
