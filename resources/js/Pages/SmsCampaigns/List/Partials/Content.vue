<template>
    <div class="p-4 lg:p-8 font-sans antialiased text-slate-700">
        <ManageSmsCampaignModal
            v-model="isShowingModal"
            :action="modalAction"
            :sms-campaign="smsCampaign"
            :content-to-send-options="contentToSendOptions"
            :schedule-type-options="scheduleTypeOptions"
            :show-addbutton="false"
            @onDeleted="onDeleted"
        />

        <div class="max-w-[1600px] mx-auto mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h1 class="text-2xl font-black tracking-tight text-indigo-950">SMS Campaigns</h1>

                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 block mb-0.5">Total</span>
                        <span class="text-xl font-bold text-indigo-900 tabular-nums leading-none">
                            {{ (smsCampaignsPayload.total ?? 0).toLocaleString() }}
                        </span>
                    </div>

                    <button
                        v-if="projectPermissions.includes('Manage sms campaigns')"
                        @click="showModal(null, 'create')"
                        class="h-10 px-5 flex items-center gap-2 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100 active:scale-95"
                    >
                        <Plus :size="14" class="text-xs" />
                        <span class="text-xs">Add SMS Campaign</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Project send messages note -->
        <div class="max-w-[1600px] mx-auto mb-4 flex items-center gap-2">
            <span v-if="projectPayload.can_send_messages" class="text-sm text-slate-600">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-emerald-50 text-emerald-700 font-bold text-xs">Sending enabled</span>
                — You can turn off "Send messages" in project settings (affects all SMS campaigns).
            </span>
            <span v-else class="text-sm text-slate-600">
                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-lg bg-rose-50 text-rose-700 font-bold text-xs">Sending disabled</span>
                — Turn on "Send messages" in project settings to allow campaigns to send.
            </span>
            <el-popover :width="380" trigger="hover">
                <template #reference>
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-lg bg-slate-100 text-slate-400 hover:text-indigo-500 hover:bg-indigo-50 transition-colors cursor-help">
                        <Info :size="14" />
                    </span>
                </template>
                <template #default>
                    <p v-if="projectPayload.can_send_messages" class="text-sm text-slate-600 leading-relaxed">
                        Turning off "Send Messages" from the project settings means no SMS campaign can send, even if the campaign's "Send Messages" option is on. Any running campaigns will complete their current sprint before stopping.
                    </p>
                    <p v-else class="text-sm text-slate-600 leading-relaxed">
                        Turning on "Send Messages" in project settings allows campaigns to send when their own "Send Messages" option is enabled.
                    </p>
                </template>
            </el-popover>
        </div>

        <div class="max-w-[1600px] mx-auto space-y-4">
            <div class="flex flex-col xl:flex-row items-center justify-between gap-4">
                <div class="flex-grow w-full xl:w-auto flex items-center gap-2">
                    <button
                        @click="refresh"
                        class="h-11 w-11 flex items-center justify-center rounded-xl bg-white border border-slate-200 text-indigo-500 hover:text-indigo-700 hover:border-indigo-200 transition-all shadow-sm"
                        title="Refresh"
                    >
                        <RefreshCw :size="18" class="text-base block" />
                    </button>
                </div>

                <div v-if="paginationLinks.length > 0" class="flex items-center gap-1">
                    <template v-for="(link, index) in paginationLinks" :key="index">
                        <Link
                            v-if="link.url"
                            :href="link.url"
                            preserve-scroll
                            class="h-9 min-w-[36px] flex items-center justify-center rounded-lg transition-all font-bold text-[10px]"
                            :class="link.active ? 'bg-indigo-600 text-white shadow-sm px-3' : 'bg-transparent text-slate-500 hover:bg-slate-100'"
                        >
                            <ChevronLeft v-if="isPrevLabel(link.label)" :size="16" />
                            <ChevronRight v-else-if="isNextLabel(link.label)" :size="16" />
                            <span v-else v-html="link.label" />
                        </Link>
                        <span
                            v-else
                            class="h-9 min-w-[36px] flex items-center justify-center rounded-lg text-slate-300 cursor-default font-bold text-[10px]"
                        >
                            <ChevronLeft v-if="isPrevLabel(link.label)" :size="16" />
                            <ChevronRight v-else-if="isNextLabel(link.label)" :size="16" />
                            <span v-else v-html="link.label" />
                        </span>
                    </template>
                </div>
            </div>

            <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
                <div v-if="smsCampaignsPayload.data.length === 0" class="py-24 px-8 flex flex-col items-center justify-center text-center">
                    <div class="h-20 w-20 rounded-3xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 mb-6">
                        <MessageSquare :size="40" class="text-slate-500" />
                    </div>
                    <h3 class="text-lg font-bold text-indigo-950 mb-1">No SMS campaigns</h3>
                    <p class="text-sm text-slate-400 max-w-xs">Create a campaign to start sending messages to your subscribers.</p>
                    <button
                        v-if="projectPermissions.includes('Manage sms campaigns')"
                        @click="showModal(null, 'create')"
                        class="mt-6 h-10 px-5 flex items-center gap-2 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100"
                    >
                        <Plus :size="14" />
                        <span class="text-xs">Add SMS Campaign</span>
                    </button>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full min-w-[1000px] border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left whitespace-nowrap">Name</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left whitespace-nowrap">Send messages</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Status</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Sprints</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Total</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Pending</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Processed</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Progress</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Last sprint</th>
                                <th class="px-8 py-5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="smsCampaign in smsCampaignsPayload.data"
                                :key="smsCampaign.id"
                                class="group hover:bg-indigo-50/20 transition-colors cursor-pointer"
                                @click="goToCampaign(smsCampaign)"
                            >
                                <td class="px-8 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-indigo-950">{{ smsCampaign.name }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <SmsCampaignCanSendSmsBadge :sms-campaign="smsCampaign" />
                                </td>
                                <td class="px-6 py-4">
                                    <SmsCampaignStatusBadge :sms-campaign-batch-job="getLatestSmsCampaignBatchJob(smsCampaign)" />
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-medium text-slate-800">{{ smsCampaign.sms_campaign_batch_jobs_count }}</span>
                                </td>
                                <td class="px-6 py-4 text-center bg-slate-50/50">
                                    <span class="text-sm font-medium text-slate-800 tabular-nums">{{ getLatestSmsCampaignBatchJob(smsCampaign).total_jobs ?? '—' }}</span>
                                </td>
                                <td class="px-6 py-4 text-center bg-slate-50/50">
                                    <span class="text-sm font-medium text-slate-800 tabular-nums">{{ getLatestSmsCampaignBatchJob(smsCampaign).pending_jobs ?? '—' }}</span>
                                </td>
                                <td class="px-6 py-4 text-center bg-slate-50/50">
                                    <span class="text-sm font-medium text-slate-800 tabular-nums">{{ getLatestSmsCampaignBatchJob(smsCampaign).processed_jobs ?? '—' }}</span>
                                </td>
                                <td class="px-6 py-4 text-center bg-slate-50/50">
                                    <span class="text-sm font-bold text-emerald-600 tabular-nums">{{ getLatestSmsCampaignBatchJob(smsCampaign).progress ?? '' }}{{ getLatestSmsCampaignBatchJob(smsCampaign).progress != null ? '%' : '—' }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-xs font-bold text-slate-600">{{ formatLastSprint(getLatestSmsCampaignBatchJob(smsCampaign).created_at) }}</span>
                                </td>
                                <td class="px-8 py-4 text-right" @click.stop>
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                        <Link
                                            v-if="projectPermissions.includes('View sms campaigns')"
                                            :href="route('show.sms.campaign.job.batches', { project: route().params.project, sms_campaign: smsCampaign.id })"
                                            class="h-8 px-3 flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-indigo-500 hover:border-indigo-200 transition-all text-xs font-bold"
                                        >
                                            View
                                        </Link>
                                        <button
                                            v-if="projectPermissions.includes('Manage sms campaigns')"
                                            type="button"
                                            @click.stop="showModal(smsCampaign, 'update')"
                                            class="h-8 px-3 flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-indigo-500 hover:border-indigo-200 transition-all text-xs font-bold"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            v-if="projectPermissions.includes('Manage sms campaigns')"
                                            type="button"
                                            @click.stop="showModal(smsCampaign, 'delete')"
                                            class="h-8 w-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:border-rose-200 transition-all"
                                            title="Delete campaign"
                                        >
                                            <Trash2 :size="12" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <Pagination
                    v-if="(smsCampaignsPayload.data?.length ?? 0) > 0 || (smsCampaignsPayload.total ?? 0) > 0"
                    :pagination-payload="smsCampaignsPayload"
                    :update-data="['smsCampaignsPayload']"
                    :min-pages="1"
                    @page-change="changePage"
                />
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Pagination from '@/Partials/Pagination.vue';
import ManageSmsCampaignModal from './ManageSmsCampaignModal.vue';
import SmsCampaignCanSendSmsBadge from '../JobBatches/List/Partials/SmsCampaignCanSendSmsBadge.vue';
import SmsCampaignStatusBadge from '../JobBatches/List/Partials/SmsCampaignStatusBadge.vue';
import { Plus, RefreshCw, ChevronLeft, ChevronRight, Info, MessageSquare, Trash2 } from 'lucide-vue-next';
import moment from 'moment';

export default defineComponent({
    components: {
        ManageSmsCampaignModal,
        Pagination,
        SmsCampaignCanSendSmsBadge,
        SmsCampaignStatusBadge,
        Link,
        Plus,
        RefreshCw,
        ChevronLeft,
        ChevronRight,
        Info,
        MessageSquare,
        Trash2,
    },
    props: {
        smsCampaignsPayload: { type: Object, required: true },
        contentToSendOptions: { type: Array, default: () => [] },
        scheduleTypeOptions: { type: Array, default: () => [] },
        projectPayload: { type: Object, default: null },
    },
    data() {
        return {
            isShowingModal: false,
            modalAction: null,
            smsCampaign: null,
        };
    },
    computed: {
        projectPermissions() {
            return this.$page?.props?.projectPermissions ?? [];
        },
        paginationLinks() {
            const links = this.smsCampaignsPayload?.links ?? [];
            if (links.length <= 1) return [];
            return links;
        },
    },
    methods: {
        changePage(page) {
            const project = route().params.project;
            if (!project) return;
            router.get(route('show.sms.campaigns', { project }), { page }, { preserveState: true });
        },
        refresh() {
            this.$inertia.reload();
        },
        isPrevLabel(label) {
            if (typeof label !== 'string') return false;
            const t = label.replace(/&[^;]+;/g, '').trim().toLowerCase();
            return t === 'previous' || t === '« previous';
        },
        isNextLabel(label) {
            if (typeof label !== 'string') return false;
            const t = label.replace(/&[^;]+;/g, '').trim().toLowerCase();
            return t === 'next' || t === 'next »';
        },
        onDeleted() {
            this.smsCampaign = null;
            this.$inertia.reload();
        },
        getLatestSmsCampaignBatchJob(smsCampaign) {
            if (smsCampaign.latest_sms_campaign_batch_job?.length) {
                return smsCampaign.latest_sms_campaign_batch_job[0];
            }
            return {};
        },
        formatLastSprint(createdAt) {
            if (!createdAt) return '—';
            return moment(createdAt).format('MMM D, YYYY h:mm A');
        },
        showModal(smsCampaign, action) {
            this.smsCampaign = smsCampaign;
            this.modalAction = action;
            this.isShowingModal = true;
        },
        goToCampaign(smsCampaign) {
            if (!this.projectPermissions.includes('View sms campaigns') || !smsCampaign?.id) return;
            this.$inertia.visit(route('show.sms.campaign.job.batches', {
                project: route().params.project,
                sms_campaign: smsCampaign.id,
            }));
        },
    },
});
</script>
