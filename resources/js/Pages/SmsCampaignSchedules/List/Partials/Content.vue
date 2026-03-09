<template>
    <div class="min-h-screen bg-slate-50/50 p-4 lg:p-8 font-sans antialiased text-slate-700">
        <div class="max-w-[1600px] mx-auto mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h1 class="text-2xl font-black tracking-tight text-indigo-950">Sms Schedules</h1>

                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 block mb-0.5">Total</span>
                        <span class="text-xl font-bold text-indigo-900 tabular-nums leading-none">
                            {{ (smsCampaignSchedulesPayload?.total ?? 0).toLocaleString() }}
                        </span>
                    </div>
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
                        <p class="text-sm font-medium text-slate-500">Loading SMS schedules...</p>
                    </div>
                    <div v-else-if="smsCampaignSchedulesPayload?.data?.length > 0" key="table-wrapper" class="overflow-x-auto">
                        <table class="w-full min-w-[900px] border-collapse [&_th]:whitespace-nowrap [&_td]:whitespace-nowrap">
                            <thead>
                                <tr class="bg-slate-50/50 border-b border-slate-100">
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Subscriber</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Next message</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Messages sent</th>
                                    <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Campaign</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <tr
                                    v-for="row in smsCampaignSchedulesPayload.data"
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
                                    <td class="px-6 py-4 text-xs text-slate-600 whitespace-normal align-top">
                                        <div class="flex flex-col gap-1">
                                            <span v-if="row.next_message_date_milli_seconds_left == null" class="text-slate-400 text-[10px]">—</span>
                                            <Countdown v-else :time="row.next_message_date_milli_seconds_left" />
                                            <span class="whitespace-nowrap text-[10px] text-slate-500">{{ row.next_message_date ? moment(row.next_message_date).format('DD MMM YY HH:mm') : '—' }}</span>
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
                            </tbody>
                        </table>
                    </div>

                    <div v-else key="empty" class="py-24 px-8 flex flex-col items-center justify-center text-center">
                        <div class="h-20 w-20 rounded-3xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 mb-6">
                            <CalendarClock :size="40" class="text-slate-500" />
                        </div>
                        <h3 class="text-lg font-bold text-indigo-950 mb-1">No SMS schedules found</h3>
                        <p class="text-sm text-slate-400 max-w-xs">There are no campaign schedules for this project yet.</p>
                    </div>
                </Transition>
                <Pagination
                    v-if="showPaginationFooter"
                    :pagination-payload="payload"
                    :update-data="['smsCampaignSchedulesPayload']"
                    :min-pages="1"
                    @page-change="changePage"
                />
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent, computed, ref, watch } from 'vue';
import Pagination from '@/Partials/Pagination.vue';
import { router, usePage } from '@inertiajs/vue3';
import moment from 'moment';
import { RefreshCw, ChevronLeft, ChevronRight, Phone, CalendarClock } from 'lucide-vue-next';

import Tag from 'primevue/tag';
import Countdown from '@/Partials/Countdown.vue';

export default defineComponent({
    components: {
        Pagination,
        Tag,
        Countdown,
        RefreshCw,
        ChevronLeft,
        ChevronRight,
        Phone,
        CalendarClock,
    },
    props: {
        smsCampaignSchedulesPayload: { type: Object, default: () => ({ data: [], total: 0, current_page: 1, last_page: 1, links: [] }) },
    },
    setup(props) {
        const loading = ref(false);
        const initialLoadComplete = ref(false);
        const page = usePage();

        const payload = computed(() => props.smsCampaignSchedulesPayload || {});

        watch([loading, () => (payload.value?.data?.length ?? 0)], () => {
            if (!loading.value && (payload.value?.data?.length ?? 0) > 0) initialLoadComplete.value = true;
        });

        /** Show footer when there is data or pagination; hide only when no data and not refetching. */
        const showPaginationFooter = computed(() => {
            const hasData = (payload.value?.data?.length ?? 0) > 0 || (payload.value?.total ?? 0) > 0 || (payload.value?.last_page ?? 0) > 0;
            return hasData || (initialLoadComplete.value && loading.value);
        });

        const filteredPagination = computed(() => {
            const current = payload.value.current_page ?? 1;
            const last = payload.value.last_page ?? 1;
            if (last <= 1) return [];
            const pages = [];
            pages.push({ label: 'prev', page: current > 1 ? current - 1 : null });
            pages.push({ label: '1', active: current === 1, page: 1 });
            if (current > 3) pages.push({ label: '...', active: false, page: null });
            const start = Math.max(2, current - 1);
            const end = Math.min(last - 1, current + 1);
            for (let i = start; i <= end; i++) {
                if (i !== 1 && i !== last) pages.push({ label: String(i), active: current === i, page: i });
            }
            if (current < last - 2) pages.push({ label: '...', active: false, page: null });
            if (last > 1) pages.push({ label: String(last), active: current === last, page: last });
            pages.push({ label: 'next', page: current < last ? current + 1 : null });
            return pages;
        });

        function getProjectId() {
            return page.props?.project?.id ?? route().params?.project;
        }

        function changePage(pageNum) {
            const projectId = getProjectId();
            if (!projectId) return;
            router.visit(route('show.sms.campaign.schedules', { project: projectId }), { data: { page: pageNum } });
        }

        function refresh() {
            loading.value = true;
            router.reload({ only: ['smsCampaignSchedulesPayload'], onFinish: () => { loading.value = false; } });
        }

        return { loading, initialLoadComplete, showPaginationFooter, filteredPagination, changePage, refresh, moment };
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
</style>
