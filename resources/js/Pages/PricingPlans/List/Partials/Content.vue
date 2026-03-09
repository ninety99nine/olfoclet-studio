<template>
    <div class="min-h-screen bg-slate-50/50 p-4 lg:p-8 font-sans antialiased text-slate-700">
        <ManagePricingPlanModal
            v-model="isShowingModal"
            :action="modalAction"
            :pricing-plan="pricingPlan"
            :parent-pricing-plan="parentPricingPlan"
            :auto-billing-reminders="autoBillingReminders"
            :breadcrumbs="breadcrumbs"
            @onDeleted="onDeleted"
        />

        <div class="max-w-[1600px] mx-auto mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black tracking-tight text-indigo-950">
                        {{ parentPricingPlan ? parentPricingPlan.name : 'Pricing Plans' }}
                    </h1>
                    <p v-if="parentPricingPlan" class="text-sm text-slate-500 mt-0.5">Folder</p>
                </div>

                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 block mb-0.5">Total</span>
                        <span class="text-xl font-bold text-indigo-900 tabular-nums leading-none">
                            {{ (pricingPlansPayload.total ?? 0).toLocaleString() }}
                        </span>
                    </div>

                    <button
                        v-if="projectPermissions.includes('Manage pricing plans')"
                        @click="showModal(null, 'create')"
                        class="h-10 px-5 flex items-center gap-2 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100 active:scale-95"
                    >
                        <Plus :size="14" class="text-xs" />
                        <span class="text-xs">Add Pricing Plan</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Breadcrumbs when inside a folder -->
        <div v-if="parentPricingPlan" class="max-w-[1600px] mx-auto mb-4">
            <div class="flex items-center gap-2 flex-wrap">
                <button
                    type="button"
                    @click="goBack"
                    class="h-9 px-3 flex items-center gap-2 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-xs font-bold transition-all"
                >
                    <ArrowLeft :size="14" />
                    Back
                </button>
                <button
                    type="button"
                    @click="navigateTo(null)"
                    class="px-3 py-1.5 rounded-lg text-xs font-semibold text-indigo-600 hover:bg-indigo-50 transition-colors"
                >
                    Pricing Plans
                </button>
                <template v-for="(crumb, idx) in breadcrumbs" :key="crumb.id">
                    <span class="text-slate-300">/</span>
                    <button
                        type="button"
                        @click="navigateTo(crumb)"
                        class="px-3 py-1.5 rounded-lg text-xs font-semibold text-indigo-600 hover:bg-indigo-50 transition-colors"
                    >
                        {{ crumb.name }}
                    </button>
                </template>
            </div>
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

            </div>

            <div class="bg-white border border-slate-200 rounded-3xl overflow-hidden shadow-sm">
                <div class="overflow-x-auto">
                    <table class="w-full border-collapse min-w-[900px]">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Name</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Description</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Type</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Active</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Tags</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Duration</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Price</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Subscriptions</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-center">Popularity</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Created</th>
                                <th class="px-8 py-5 text-right"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr
                                v-for="plan in pricingPlansPayload.data"
                                :key="plan.id"
                                :class="[
                                    'group transition-colors',
                                    plan.is_folder
                                        ? 'bg-slate-50/60 hover:bg-slate-100/80 cursor-pointer'
                                        : 'hover:bg-indigo-50/20',
                                ]"
                                @click="plan.is_folder && openFolder(plan.id)"
                            >
                                <td class="px-8 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-2">
                                        <Folder v-if="plan.is_folder" :size="16" class="text-slate-400 shrink-0" />
                                        <span class="text-sm font-bold text-indigo-950">{{ plan.name }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 max-w-[200px] truncate" :title="plan.description || ''">
                                    {{ plan.description ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <FolderStatusBadge :pricing-plan="plan" />
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <ActiveStatusBadge :pricing-plan="plan" />
                                </td>
                                <td class="px-6 py-4">
                                    <div v-if="!plan.tags?.length" class="text-slate-400 text-sm">—</div>
                                    <div v-else class="flex flex-wrap gap-1 justify-center">
                                        <span
                                            v-for="(tag, index) in plan.tags"
                                            :key="index"
                                            class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100"
                                        >
                                            {{ tag }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 text-center">
                                    {{ plan.duration_in_words ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-700 text-center font-medium">
                                    {{ formatMoney(plan.price) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 text-center tabular-nums">
                                    {{ plan.subscriptions_count ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="text-sm font-bold text-emerald-600">{{ getPercentageOfCoverage(plan.subscriptions_count) }}%</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-slate-600">
                                    {{ plan.created_at ? moment(plan.created_at).fromNow() : '—' }}
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                        <Link
                                            v-if="projectPermissions.includes('View pricing plans') && plan.is_folder"
                                            :href="route('show.pricing.plan', { project: $page.props.project?.id ?? route().params.project, pricing_plan: plan.id })"
                                            class="h-8 px-3 flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-indigo-500 hover:border-indigo-200 transition-all text-xs font-bold"
                                            @click.stop
                                        >
                                            View
                                        </Link>
                                        <button
                                            v-if="projectPermissions.includes('Manage pricing plans')"
                                            type="button"
                                            @click.stop="showModal(plan, 'update')"
                                            class="h-8 px-3 flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-indigo-500 hover:border-indigo-200 transition-all text-xs font-bold"
                                        >
                                            Edit
                                        </button>
                                        <button
                                            v-if="projectPermissions.includes('Manage pricing plans')"
                                            type="button"
                                            @click.stop="showModal(plan, 'delete')"
                                            class="h-8 w-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:border-rose-200 transition-all"
                                            title="Delete"
                                        >
                                            <Trash2 :size="12" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div v-if="pricingPlansPayload.data.length === 0" class="py-24 px-8 flex flex-col items-center justify-center text-center">
                    <div class="h-20 w-20 rounded-3xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 mb-6">
                        <CreditCard :size="40" class="text-slate-500" />
                    </div>
                    <h3 class="text-lg font-bold text-indigo-950 mb-1">No pricing plans</h3>
                    <p class="text-sm text-slate-400 max-w-xs">There are no pricing plans in this folder yet.</p>
                    <button
                        v-if="projectPermissions.includes('Manage pricing plans')"
                        type="button"
                        @click="showModal(null, 'create')"
                        class="mt-6 h-10 px-5 flex items-center gap-2 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100"
                    >
                        <Plus :size="14" />
                        Add Pricing Plan
                    </button>
                </div>

                <Pagination
                    v-else-if="pricingPlansPayload.total > 0"
                    class="border-t border-slate-100"
                    :pagination-payload="pricingPlansPayload"
                    :update-data="['pricingPlansPayload']"
                />
            </div>
        </div>
    </div>
</template>

<script>
import { defineComponent } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import moment from 'moment';
import { formatMoney } from '@/utils/formatMoney';
import { Plus, RefreshCw, ArrowLeft, Trash2, CreditCard, Folder } from 'lucide-vue-next';
import ActiveStatusBadge from './ActiveStatusBadge.vue';
import FolderStatusBadge from './FolderStatusBadge.vue';
import Pagination from '@/Partials/Pagination.vue';
import ManagePricingPlanModal from './ManagePricingPlanModal.vue';

export default defineComponent({
    components: {
        Link,
        ManagePricingPlanModal,
        Pagination,
        ActiveStatusBadge,
        FolderStatusBadge,
        Plus,
        RefreshCw,
        ArrowLeft,
        Trash2,
        CreditCard,
        Folder,
    },
    props: {
        breadcrumbs: { type: Array, default: () => [] },
        totalSubscriptions: { type: Number, default: 0 },
        autoBillingReminders: { type: Array, default: () => [] },
        parentPricingPlan: { type: Object, default: null },
        pricingPlansPayload: {
            type: Object,
            default: () => ({ data: [], total: 0, links: [] }),
        },
    },
    data() {
        return {
            moment,
            modalAction: null,
            isShowingModal: false,
            pricingPlan: null,
        };
    },
    computed: {
        projectPermissions() {
            return this.$page?.props?.projectPermissions ?? [];
        },
    },
    methods: {
        formatMoney,
        getPercentageOfCoverage(subscriptionsCount) {
            if (this.totalSubscriptions > 0 && subscriptionsCount != null) {
                return Math.round((subscriptionsCount / this.totalSubscriptions) * 100);
            }
            return 0;
        },
        showModal(pricingPlan, action) {
            this.pricingPlan = pricingPlan;
            this.modalAction = action;
            this.isShowingModal = true;
        },
        onDeleted() {
            this.pricingPlan = null;
        },
        refresh() {
            router.reload({ only: ['pricingPlansPayload'] });
        },
        goBack() {
            window.history.back();
        },
        navigateTo(pricingPlan) {
            const project = this.$page?.props?.project?.id ?? route().params.project;
            if (pricingPlan) {
                router.get(route('show.pricing.plan', { project, pricing_plan: pricingPlan.id }));
            } else {
                router.get(route('show.pricing.plans', { project }));
            }
        },
        openFolder(pricingPlanId) {
            const project = this.$page?.props?.project?.id ?? route().params.project;
            router.get(route('show.pricing.plan', { project, pricing_plan: pricingPlanId }));
        },
    },
});
</script>
