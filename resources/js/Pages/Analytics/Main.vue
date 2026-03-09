<template>
    <AppLayout title="Analytics">
        <div class="min-h-screen bg-slate-50 pb-12">
            <!-- Header + Controls -->
            <div class="bg-white border-b border-slate-200">
                <div class="max-w-7xl mx-auto px-6 py-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-2xl font-semibold text-slate-900">Analytics & Insights</h1>
                        <p class="mt-1 text-sm text-slate-500">{{ projectName }} • {{ timeRangeLabel }}</p>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <button
                            v-for="tab in quickRanges"
                            :key="tab.value"
                            type="button"
                            class="px-4 py-2 rounded-lg text-sm font-medium transition-colors"
                            :class="currentRange === tab.value
                                ? 'bg-indigo-600 text-white'
                                : 'bg-slate-100 text-slate-700 hover:bg-slate-200'"
                            @click="onRangeClick(tab)"
                        >
                            {{ tab.label }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Custom date range modal -->
            <Dialog
                v-model:visible="customDateModalVisible"
                header="Custom date range"
                :modal="true"
                :dismissableMask="true"
                :style="{ width: '380px' }"
                :pt="{ root: { class: 'rounded-xl shadow-xl' } }"
                @hide="customDateModalVisible = false"
            >
                <div class="space-y-4 pb-2">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Start date</label>
                        <DatePicker v-model="customDateStart" dateFormat="yy-mm-dd" class="w-full" showIcon />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">End date</label>
                        <DatePicker v-model="customDateEnd" dateFormat="yy-mm-dd" class="w-full" showIcon />
                    </div>
                </div>
                <template #footer>
                    <div class="flex justify-end gap-2">
                        <Button label="Cancel" severity="secondary" outlined @click="customDateModalVisible = false" />
                        <Button label="Apply" :disabled="!customDateStart || !customDateEnd" @click="applyCustomRange" />
                    </div>
                </template>
            </Dialog>

            <!-- Main Content -->
            <div class="max-w-7xl mx-auto px-6 py-8">
                <!-- KPI Cards (always show layout; skeleton until data loads) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div
                        v-for="(card, index) in kpiCardDefinitions"
                        :key="index"
                        class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm"
                    >
                        <p class="text-sm font-medium text-slate-500 truncate">{{ card.title }}</p>
                        <div v-if="loadingOverview || kpiValue(card.key) === null" class="mt-2 h-3 w-10 rounded-full bg-slate-100/80 animate-pulse" aria-hidden="true" />
                        <p v-else class="mt-1 text-2xl font-semibold text-slate-900 tabular-nums">
                            {{ kpiValue(card.key) }}
                        </p>
                    </div>
                </div>

                <!-- Row 1: 50/50 - Subscribers | New subscriptions -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-200">
                            <h2 class="text-lg font-semibold text-slate-900">Subscribers over time</h2>
                        </div>
                        <div class="p-6 h-72 relative">
                            <div v-if="loadingSubscribersChart" class="absolute inset-0 flex items-center justify-center bg-white/60 z-20">
                                <div class="animate-spin rounded-full h-10 w-10 border-2 border-indigo-600 border-t-transparent" />
                            </div>
                            <div v-if="!loadingSubscribersChart && !subscribersChartData.labels?.length" class="absolute inset-0 flex items-center justify-center text-slate-400">
                                <p class="text-lg font-medium">No data for this period</p>
                            </div>
                            <canvas v-if="subscribersChartData.labels?.length" :key="'subscribers-' + chartCanvasKey" ref="subscribersChartCanvas" class="w-full h-full" />
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-200">
                            <h2 class="text-lg font-semibold text-slate-900">New subscriptions over time</h2>
                        </div>
                        <div class="p-6 h-72 relative">
                            <div v-if="loadingSubscriptionsOverTime" class="absolute inset-0 flex items-center justify-center bg-white/60 z-20">
                                <div class="animate-spin rounded-full h-10 w-10 border-2 border-indigo-600 border-t-transparent" />
                            </div>
                            <div v-if="!loadingSubscriptionsOverTime && !subscriptionsOverTimeData.labels?.length" class="absolute inset-0 flex items-center justify-center text-slate-400">
                                <p class="text-lg font-medium">No data for this period</p>
                            </div>
                            <canvas v-if="subscriptionsOverTimeData.labels?.length" :key="'subscriptions-ot-' + chartCanvasKey" ref="subscriptionsOverTimeCanvas" class="w-full h-full" />
                        </div>
                    </div>
                </div>

                <!-- Row 2: 100% - Transactions over time -->
                <div class="mb-8">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-200">
                            <h2 class="text-lg font-semibold text-slate-900">Transactions over time</h2>
                        </div>
                        <div class="p-6 h-72 relative">
                            <div v-if="loadingTransactionsChart" class="absolute inset-0 flex items-center justify-center bg-white/60 z-20">
                                <div class="animate-spin rounded-full h-10 w-10 border-2 border-indigo-600 border-t-transparent" />
                            </div>
                            <div v-if="!loadingTransactionsChart && !transactionsChartData.labels?.length" class="absolute inset-0 flex items-center justify-center text-slate-400">
                                <p class="text-lg font-medium">No data for this period</p>
                            </div>
                            <canvas v-if="transactionsChartData.labels?.length" :key="'transactions-' + chartCanvasKey" ref="transactionsChartCanvas" class="w-full h-full" />
                        </div>
                    </div>
                </div>

                <!-- Row 3: 50/50 - Freemium vs paid | Active subscriptions by plan -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-200">
                            <h2 class="text-lg font-semibold text-slate-900">Freemium vs paid (active)</h2>
                        </div>
                        <div class="p-6 h-72 relative">
                            <div v-if="loadingSubscriptionMix" class="absolute inset-0 flex items-center justify-center bg-white/60 z-20">
                                <div class="animate-spin rounded-full h-10 w-10 border-2 border-indigo-600 border-t-transparent" />
                            </div>
                            <div v-if="!loadingSubscriptionMix && (!subscriptionMixData.values?.length || !subscriptionMixData.values.some(v => v > 0))" class="absolute inset-0 flex items-center justify-center text-slate-400">
                                <p class="text-lg font-medium">No active subscriptions</p>
                            </div>
                            <canvas v-if="subscriptionMixData.values?.length && subscriptionMixData.values.some(v => v > 0)" :key="'mix-' + chartCanvasKey" ref="subscriptionMixCanvas" class="w-full h-full" />
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-200">
                            <h2 class="text-lg font-semibold text-slate-900">Active subscriptions by plan</h2>
                        </div>
                        <div class="p-6 h-72 relative">
                            <div v-if="loadingSubscriptionsByPlan" class="absolute inset-0 flex items-center justify-center bg-white/60 z-20">
                                <div class="animate-spin rounded-full h-10 w-10 border-2 border-indigo-600 border-t-transparent" />
                            </div>
                            <div v-if="!loadingSubscriptionsByPlan && !subscriptionsByPlanData.labels?.length" class="absolute inset-0 flex items-center justify-center text-slate-400">
                                <p class="text-lg font-medium">No active subscriptions</p>
                            </div>
                            <canvas v-if="subscriptionsByPlanData.labels?.length" :key="'byplan-' + chartCanvasKey" ref="subscriptionsByPlanCanvas" class="w-full h-full" />
                        </div>
                    </div>
                </div>

                <!-- Row 4: 50/50 - Messages sent over time | Revenue over time -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-200">
                            <h2 class="text-lg font-semibold text-slate-900">Messages sent over time</h2>
                        </div>
                        <div class="p-6 h-72 relative">
                            <div v-if="loadingMessagesChart" class="absolute inset-0 flex items-center justify-center bg-white/60 z-20">
                                <div class="animate-spin rounded-full h-10 w-10 border-2 border-indigo-600 border-t-transparent" />
                            </div>
                            <div v-if="!loadingMessagesChart && !messagesChartData.labels?.length" class="absolute inset-0 flex items-center justify-center text-slate-400">
                                <p class="text-lg font-medium">No data for this period</p>
                            </div>
                            <canvas v-if="messagesChartData.labels?.length" :key="'messages-' + chartCanvasKey" ref="messagesChartCanvas" class="w-full h-full" />
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-slate-200">
                            <h2 class="text-lg font-semibold text-slate-900">Revenue over time</h2>
                        </div>
                        <div class="p-6 h-72 relative">
                            <div v-if="loadingRevenueChart" class="absolute inset-0 flex items-center justify-center bg-white/60 z-20">
                                <div class="animate-spin rounded-full h-10 w-10 border-2 border-indigo-600 border-t-transparent" />
                            </div>
                            <div v-if="!loadingRevenueChart && !revenueChartData.labels?.length" class="absolute inset-0 flex items-center justify-center text-slate-400">
                                <p class="text-lg font-medium">No revenue in this period</p>
                            </div>
                            <canvas v-if="revenueChartData.labels?.length" :key="'revenue-' + chartCanvasKey" ref="revenueChartCanvas" class="w-full h-full" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { defineComponent, ref, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import DatePicker from 'primevue/datepicker';
import AppLayout from '@/Layouts/AppLayout.vue';
import axios from 'axios';

export default defineComponent({
    name: 'Analytics',
    components: { AppLayout, Dialog, Button, DatePicker },
    setup() {
        const toast = useToast();
        const page = usePage();
        return { toast, page };
    },
    data() {
        return {
            currentRange: '30d',
            loadingOverview: false,
            loadingSubscribersChart: false,
            loadingMessagesChart: false,
            loadingTransactionsChart: false,
            loadingSubscriptionMix: false,
            loadingSubscriptionsByPlan: false,
            loadingRevenueChart: false,
            loadingSubscriptionsOverTime: false,
            overview: null,
            subscribersChartData: { labels: [], values: [] },
            messagesChartData: { labels: [], values: [] },
            transactionsChartData: { labels: [], values: [], values_successful: [] },
            subscriptionMixData: { labels: [], values: [] },
            subscriptionsByPlanData: { labels: [], values: [] },
            revenueChartData: { labels: [], values: [] },
            subscriptionsOverTimeData: { labels: [], values: [] },
            chartInstances: {},
            customDateModalVisible: false,
            customDateStart: null,
            customDateEnd: null,
            quickRanges: [
                { value: 'today', label: 'Today' },
                { value: '7d', label: '7 days' },
                { value: '30d', label: '30 days' },
                { value: '90d', label: '90 days' },
                { value: 'ytd', label: 'YTD' },
                { value: 'custom', label: 'Custom' },
            ],
        };
    },
    computed: {
        project() {
            return this.page.props.project ?? null;
        },
        projectName() {
            return this.project?.name ?? 'Project';
        },
        timeRangeLabel() {
            if (this.currentRange === 'custom' && this.customDateStart && this.customDateEnd) {
                const s = this.formatDateLabel(this.customDateStart);
                const e = this.formatDateLabel(this.customDateEnd);
                return `${s} – ${e}`;
            }
            const found = this.quickRanges.find((r) => r.value === this.currentRange);
            return found ? found.label : this.currentRange;
        },
        chartCanvasKey() {
            if (this.currentRange === 'custom' && this.customDateStart && this.customDateEnd) {
                return 'custom-' + this.toDateString(this.customDateStart) + '-' + this.toDateString(this.customDateEnd);
            }
            return this.currentRange;
        },
        kpiCardDefinitions() {
            return [
                { key: 'total_subscribers', title: 'Total Subscribers' },
                { key: 'new_subscribers', title: 'New Subscribers (period)' },
                { key: 'paid_subscribers', title: 'Paid Subscribers (ever)' },
                { key: 'active_subscriptions', title: 'Active Subscriptions' },
                { key: 'active_free_subscriptions', title: 'Freemium (active)' },
                { key: 'active_paid_subscriptions', title: 'Paid (active)' },
                { key: 'total_transactions', title: 'Transactions' },
                { key: 'successful_transactions', title: 'Successful Transactions' },
                { key: 'unsuccessful_transactions', title: 'Unsuccessful Transactions' },
                { key: 'transaction_success_rate', title: 'Transaction Success Rate' },
                { key: 'total_revenue', title: 'Total Revenue' },
                { key: 'total_subscriber_messages', title: 'Message Sent' },
                { key: 'message_delivery_rate', title: 'Message Delivery Rate' },
                { key: 'successful_subscriber_messages', title: 'Messages Sent' },
            ];
        },
    },
    watch: {
        currentRange() {
            this.destroyAllCharts();
            this.loadAll();
        },
    },
    methods: {
        getApiParams() {
            const params = { range: this.currentRange };
            if (this.currentRange === 'custom' && this.customDateStart && this.customDateEnd) {
                params.start = this.toDateString(this.customDateStart);
                params.end = this.toDateString(this.customDateEnd);
            }
            return params;
        },
        onRangeClick(tab) {
            if (tab.value === 'custom') {
                this.customDateModalVisible = true;
                if (!this.customDateStart && !this.customDateEnd) {
                    const end = new Date();
                    const start = new Date();
                    start.setDate(start.getDate() - 30);
                    this.customDateStart = start;
                    this.customDateEnd = end;
                }
            } else {
                this.currentRange = tab.value;
            }
        },
        applyCustomRange() {
            if (!this.customDateStart || !this.customDateEnd) return;
            if (this.customDateStart > this.customDateEnd) {
                [this.customDateStart, this.customDateEnd] = [this.customDateEnd, this.customDateStart];
            }
            this.currentRange = 'custom';
            this.customDateModalVisible = false;
            this.loadAll();
        },
        toDateString(d) {
            if (!d) return null;
            const date = d instanceof Date ? d : new Date(d);
            return date.toISOString().slice(0, 10);
        },
        formatDateLabel(d) {
            if (!d) return '';
            const date = d instanceof Date ? d : new Date(d);
            return date.toLocaleDateString('en-GB', { day: 'numeric', month: 'short', year: 'numeric' });
        },
        apiBase() {
            if (!this.project?.id) return '';
            return `/api/projects/${this.project.id}/analytics`;
        },
        kpiValue(key) {
            if (!this.overview) return null;
            const o = this.overview;
            switch (key) {
                case 'total_revenue': {
                    const v = (o.total_revenue ?? 0);
                    return v > 0 ? `P${v.toLocaleString(undefined, { minimumFractionDigits: 2 })}` : '—';
                }
                case 'message_delivery_rate':
                    return o.message_delivery_rate != null ? `${o.message_delivery_rate}%` : '—';
                case 'transaction_success_rate':
                    return o.transaction_success_rate != null ? `${o.transaction_success_rate}%` : '—';
                default:
                    return (o[key] ?? 0).toLocaleString();
            }
        },
        async fetchOverview() {
            if (!this.apiBase()) return;
            try {
                this.loadingOverview = true;
                const { data } = await axios.get(`${this.apiBase()}/overview`, {
                    params: this.getApiParams(),
                });
                this.overview = data;
            } catch (err) {
                const msg = err?.response?.data?.message ?? err?.message ?? 'Failed to load overview';
                this.toast.add({ severity: 'warn', summary: 'Analytics', detail: msg, life: 5000 });
            } finally {
                this.loadingOverview = false;
            }
        },
        async fetchSubscribersOverTime() {
            if (!this.apiBase()) return;
            try {
                this.loadingSubscribersChart = true;
                const { data } = await axios.get(`${this.apiBase()}/subscribers-over-time`, { params: this.getApiParams() });
                this.subscribersChartData = { labels: data.labels ?? [], values: data.values ?? [] };
                this.$nextTick(() => requestAnimationFrame(() => this.renderSubscribersChart()));
            } catch (err) {
                this.toast.add({ severity: 'warn', summary: 'Analytics', detail: err?.response?.data?.message ?? err?.message ?? 'Failed to load', life: 5000 });
            } finally {
                this.loadingSubscribersChart = false;
            }
        },
        async fetchMessagesOverTime() {
            if (!this.apiBase()) return;
            try {
                this.loadingMessagesChart = true;
                const { data } = await axios.get(`${this.apiBase()}/messages-over-time`, { params: this.getApiParams() });
                this.messagesChartData = { labels: data.labels ?? [], values: data.values ?? [] };
                this.$nextTick(() => requestAnimationFrame(() => this.renderMessagesChart()));
            } catch (err) {
                this.toast.add({ severity: 'warn', summary: 'Analytics', detail: err?.response?.data?.message ?? err?.message ?? 'Failed to load', life: 5000 });
            } finally {
                this.loadingMessagesChart = false;
            }
        },
        async fetchTransactionsOverTime() {
            if (!this.apiBase()) return;
            try {
                this.loadingTransactionsChart = true;
                const { data } = await axios.get(`${this.apiBase()}/transactions-over-time`, { params: this.getApiParams() });
                this.transactionsChartData = {
                    labels: data.labels ?? [],
                    values: data.values ?? [],
                    values_successful: data.values_successful ?? [],
                };
                this.$nextTick(() => requestAnimationFrame(() => this.renderTransactionsChart()));
            } catch (err) {
                this.toast.add({ severity: 'warn', summary: 'Analytics', detail: err?.response?.data?.message ?? err?.message ?? 'Failed to load', life: 5000 });
            } finally {
                this.loadingTransactionsChart = false;
            }
        },
        async fetchSubscriptionMix() {
            if (!this.apiBase()) return;
            try {
                this.loadingSubscriptionMix = true;
                const { data } = await axios.get(`${this.apiBase()}/subscription-mix`, { params: this.getApiParams() });
                this.subscriptionMixData = { labels: data.labels ?? [], values: data.values ?? [] };
                this.$nextTick(() => requestAnimationFrame(() => this.renderSubscriptionMixChart()));
            } catch (err) {
                this.toast.add({ severity: 'warn', summary: 'Analytics', detail: err?.response?.data?.message ?? err?.message ?? 'Failed to load', life: 5000 });
            } finally {
                this.loadingSubscriptionMix = false;
            }
        },
        async fetchSubscriptionsByPlan() {
            if (!this.apiBase()) return;
            try {
                this.loadingSubscriptionsByPlan = true;
                const { data } = await axios.get(`${this.apiBase()}/subscriptions-by-plan`, { params: this.getApiParams() });
                this.subscriptionsByPlanData = { labels: data.labels ?? [], values: data.values ?? [] };
                this.$nextTick(() => requestAnimationFrame(() => this.renderSubscriptionsByPlanChart()));
            } catch (err) {
                this.toast.add({ severity: 'warn', summary: 'Analytics', detail: err?.response?.data?.message ?? err?.message ?? 'Failed to load', life: 5000 });
            } finally {
                this.loadingSubscriptionsByPlan = false;
            }
        },
        async fetchRevenueOverTime() {
            if (!this.apiBase()) return;
            try {
                this.loadingRevenueChart = true;
                const { data } = await axios.get(`${this.apiBase()}/revenue-over-time`, { params: this.getApiParams() });
                this.revenueChartData = { labels: data.labels ?? [], values: data.values ?? [] };
                this.$nextTick(() => requestAnimationFrame(() => this.renderRevenueChart()));
            } catch (err) {
                this.toast.add({ severity: 'warn', summary: 'Analytics', detail: err?.response?.data?.message ?? err?.message ?? 'Failed to load', life: 5000 });
            } finally {
                this.loadingRevenueChart = false;
            }
        },
        async fetchSubscriptionsOverTime() {
            if (!this.apiBase()) return;
            try {
                this.loadingSubscriptionsOverTime = true;
                const { data } = await axios.get(`${this.apiBase()}/subscriptions-over-time`, { params: this.getApiParams() });
                this.subscriptionsOverTimeData = { labels: data.labels ?? [], values: data.values ?? [] };
                this.$nextTick(() => requestAnimationFrame(() => this.renderSubscriptionsOverTimeChart()));
            } catch (err) {
                this.toast.add({ severity: 'warn', summary: 'Analytics', detail: err?.response?.data?.message ?? err?.message ?? 'Failed to load', life: 5000 });
            } finally {
                this.loadingSubscriptionsOverTime = false;
            }
        },
        async loadAll() {
            this.loadingOverview = true;
            this.loadingSubscribersChart = true;
            this.loadingMessagesChart = true;
            this.loadingTransactionsChart = true;
            this.loadingSubscriptionMix = true;
            this.loadingSubscriptionsByPlan = true;
            this.loadingRevenueChart = true;
            this.loadingSubscriptionsOverTime = true;
            await this.fetchOverview();
            await Promise.all([
                this.fetchSubscribersOverTime(),
                this.fetchMessagesOverTime(),
                this.fetchTransactionsOverTime(),
                this.fetchSubscriptionMix(),
                this.fetchSubscriptionsByPlan(),
                this.fetchRevenueOverTime(),
                this.fetchSubscriptionsOverTime(),
            ]);
        },
        destroyAllCharts() {
            const keys = ['subscribers', 'messages', 'transactions', 'subscriptionMix', 'subscriptionsByPlan', 'revenue', 'subscriptionsOverTime'];
            keys.forEach((key) => this.destroyChart(key));
        },
        destroyChart(key) {
            if (this.chartInstances[key]) {
                try {
                    this.chartInstances[key].destroy();
                } catch (_) {}
                this.chartInstances[key] = null;
            }
        },
        renderSubscribersChart() {
            this.destroyChart('subscribers');
            const canvas = this.$refs.subscribersChartCanvas;
            if (!canvas || !this.subscribersChartData.labels?.length) return;
            const Chart = window.Chart;
            if (!Chart) return;
            this.chartInstances.subscribers = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: this.subscribersChartData.labels,
                    datasets: [{ label: 'New Subscribers', data: this.subscribersChartData.values, borderColor: '#6366f1', backgroundColor: 'rgba(99, 102, 241, 0.1)', fill: true, tension: 0.2 }],
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true } } },
            });
        },
        renderMessagesChart() {
            this.destroyChart('messages');
            const canvas = this.$refs.messagesChartCanvas;
            if (!canvas || !this.messagesChartData.labels?.length) return;
            const Chart = window.Chart;
            if (!Chart) return;
            this.chartInstances.messages = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: this.messagesChartData.labels,
                    datasets: [{ label: 'Messages Sent', data: this.messagesChartData.values, borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.1)', fill: true, tension: 0.2 }],
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true } } },
            });
        },
        renderTransactionsChart() {
            this.destroyChart('transactions');
            const canvas = this.$refs.transactionsChartCanvas;
            if (!canvas || !this.transactionsChartData.labels?.length) return;
            const Chart = window.Chart;
            if (!Chart) return;
            const d = this.transactionsChartData;
            this.chartInstances.transactions = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: d.labels,
                    datasets: [
                        { label: 'All', data: d.values, borderColor: '#94a3b8', backgroundColor: 'rgba(148, 163, 184, 0.1)', fill: true, tension: 0.2 },
                        { label: 'Successful', data: d.values_successful, borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.1)', fill: true, tension: 0.2 },
                    ],
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true } } },
            });
        },
        renderSubscriptionMixChart() {
            this.destroyChart('subscriptionMix');
            const canvas = this.$refs.subscriptionMixCanvas;
            const vals = this.subscriptionMixData.values ?? [];
            if (!canvas || !vals.length || !vals.some((v) => v > 0)) return;
            const Chart = window.Chart;
            if (!Chart) return;
            this.chartInstances.subscriptionMix = new Chart(canvas, {
                type: 'doughnut',
                data: {
                    labels: this.subscriptionMixData.labels,
                    datasets: [{
                        data: this.subscriptionMixData.values,
                        backgroundColor: ['rgba(99, 102, 241, 0.85)', 'rgba(16, 185, 129, 0.85)'],
                        borderColor: '#fff',
                        borderWidth: 2,
                    }],
                },
                options: { responsive: true, maintainAspectRatio: false, cutout: '60%', plugins: { legend: { position: 'bottom' } } },
            });
        },
        renderSubscriptionsByPlanChart() {
            this.destroyChart('subscriptionsByPlan');
            const canvas = this.$refs.subscriptionsByPlanCanvas;
            if (!canvas || !this.subscriptionsByPlanData.labels?.length) return;
            const Chart = window.Chart;
            if (!Chart) return;
            this.chartInstances.subscriptionsByPlan = new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: this.subscriptionsByPlanData.labels,
                    datasets: [{ label: 'Active Subscriptions', data: this.subscriptionsByPlanData.values, backgroundColor: 'rgba(99, 102, 241, 0.7)', borderRadius: 4 }],
                },
                options: { indexAxis: 'y', responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true }, y: { grid: { display: false } } } },
            });
        },
        renderRevenueChart() {
            this.destroyChart('revenue');
            const canvas = this.$refs.revenueChartCanvas;
            if (!canvas || !this.revenueChartData.labels?.length) return;
            const Chart = window.Chart;
            if (!Chart) return;
            this.chartInstances.revenue = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: this.revenueChartData.labels,
                    datasets: [{ label: 'Revenue', data: this.revenueChartData.values, borderColor: '#059669', backgroundColor: 'rgba(5, 150, 105, 0.15)', fill: true, tension: 0.2 }],
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { callback: (v) => 'P' + Number(v).toLocaleString() } } } },
            });
        },
        renderSubscriptionsOverTimeChart() {
            this.destroyChart('subscriptionsOverTime');
            const canvas = this.$refs.subscriptionsOverTimeCanvas;
            if (!canvas || !this.subscriptionsOverTimeData.labels?.length) return;
            const Chart = window.Chart;
            if (!Chart) return;
            this.chartInstances.subscriptionsOverTime = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: this.subscriptionsOverTimeData.labels,
                    datasets: [{ label: 'New Subscriptions', data: this.subscriptionsOverTimeData.values, borderColor: '#7c3aed', backgroundColor: 'rgba(124, 58, 237, 0.1)', fill: true, tension: 0.2 }],
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true } } },
            });
        },
    },
    async mounted() {
        if (typeof window.Chart === 'undefined') {
            const Chart = (await import('chart.js/auto')).default;
            window.Chart = Chart;
        }
        await this.loadAll();
    },
    beforeUnmount() {
        Object.keys(this.chartInstances || {}).forEach((key) => {
            if (this.chartInstances[key]) {
                try {
                    this.chartInstances[key].destroy();
                } catch (_) {}
            }
        });
    },
});
</script>
