<template>
    <AppLayout :title="`Billing Report: ${billingReportPayload?.name ?? 'Report'}`">
        <div class="min-h-screen bg-slate-50 pb-12">
            <div class="max-w-[1600px] mx-auto px-6 pt-6 pb-12">
                <!-- Flash error (e.g. PDF generation failed) -->
                <div v-if="$page.props.flash?.error" class="mb-6 p-4 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-sm">
                    {{ $page.props.flash.error }}
                </div>

                <!-- Back + breadcrumb (same pattern as Topics / Messages) -->
                <div class="flex flex-wrap items-center gap-2 mb-6">
                    <Link
                        :href="route('show.billing.reports', { project: projectPayload?.id })"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 text-xs font-semibold hover:bg-slate-50 hover:border-slate-300 transition-all"
                    >
                        <ArrowLeft :size="14" />
                        Back
                    </Link>
                    <span class="flex items-center gap-2">
                        <ChevronRight :size="14" class="text-slate-300 shrink-0" />
                        <Link
                            :href="route('show.billing.reports', { project: projectPayload?.id })"
                            class="text-indigo-600 hover:text-indigo-700 text-xs font-semibold hover:underline"
                        >
                            Billing Reports
                        </Link>
                    </span>
                    <span class="flex items-center gap-2">
                        <ChevronRight :size="14" class="text-slate-300 shrink-0" />
                        <span class="text-slate-600 text-xs font-medium truncate max-w-[280px]" :title="billingReportPayload?.name">
                            {{ billingReportPayload?.name ?? '—' }}
                        </span>
                    </span>
                </div>

                <!-- Summary card -->
                <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden mb-8">
                    <div class="px-8 py-6 border-b border-slate-100">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h1 class="text-2xl font-black tracking-tight text-indigo-950">
                                    {{ billingReportPayload?.name ?? '—' }}
                                </h1>
                                <p class="text-sm text-slate-500 mt-1">
                                    {{ projectPayload?.name }} • Report period: {{ periodLabel }}
                                </p>
                            </div>
                            <div class="flex flex-wrap items-center gap-3">
                                <a
                                    :href="detailedPdfUrl"
                                    class="h-10 px-4 flex items-center gap-2 rounded-xl bg-indigo-600 text-white font-semibold hover:bg-indigo-700 transition-all text-sm shadow-sm"
                                >
                                    <Download :size="14" />
                                    Download Report PDF
                                </a>
                                <a
                                    :href="invoicePdfUrl"
                                    class="h-10 px-4 flex items-center gap-2 rounded-xl bg-emerald-600 text-white font-semibold hover:bg-emerald-700 transition-all text-sm shadow-sm"
                                >
                                    <Download :size="14" />
                                    Download Invoice PDF
                                </a>
                                <a
                                    v-if="billingReportPayload?.overview_pdf_path"
                                    :href="billingReportPayload.overview_pdf_path"
                                    download
                                    class="h-10 px-4 flex items-center gap-2 rounded-xl bg-indigo-50 text-indigo-700 font-semibold hover:bg-indigo-100 transition-all text-sm"
                                >
                                    <FileText :size="14" />
                                    Overview PDF
                                </a>
                                <a
                                    v-if="billingReportPayload?.invoice_pdf_path"
                                    :href="billingReportPayload.invoice_pdf_path"
                                    download
                                    class="h-10 px-4 flex items-center gap-2 rounded-xl bg-indigo-50 text-indigo-700 font-semibold hover:bg-indigo-100 transition-all text-sm"
                                >
                                    <FileText :size="14" />
                                    Invoice PDF
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="p-8">
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-6">
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Gross Revenue</p>
                                <p class="text-lg font-bold text-indigo-900 mt-0.5">{{ formatMoney(billingReportPayload?.gross_revenue) }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Costs</p>
                                <p class="text-lg font-bold text-slate-800 mt-0.5">{{ formatMoney(billingReportPayload?.costs) }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Sharable Revenue</p>
                                <p class="text-lg font-bold text-slate-800 mt-0.5">{{ formatMoney(billingReportPayload?.sharable_revenue) }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4 border border-indigo-100 bg-indigo-50/50">
                                <p class="text-[10px] font-black text-indigo-600 uppercase tracking-wider">Our Share (60%)</p>
                                <p class="text-lg font-bold text-indigo-900 mt-0.5">{{ formatMoney(billingReportPayload?.our_share) }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Their Share (40%)</p>
                                <p class="text-lg font-bold text-slate-800 mt-0.5">{{ formatMoney(billingReportPayload?.their_share) }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Total Transactions</p>
                                <p class="text-lg font-bold text-slate-800 mt-0.5 tabular-nums">{{ (billingReportPayload?.total_transactions ?? 0).toLocaleString() }}</p>
                            </div>
                        </div>
                        <!-- Cost breakdown -->
                        <div v-if="costBreakdownList.length" class="mt-8 pt-6 border-t border-slate-100">
                            <p class="text-sm font-bold text-slate-700 mb-3">Cost breakdown</p>
                            <div class="flex flex-wrap gap-3">
                                <div
                                    v-for="([name, amount], idx) in costBreakdownList"
                                    :key="idx"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 bg-slate-100 rounded-lg text-sm"
                                >
                                    <span class="text-slate-600">{{ name }}</span>
                                    <span class="font-semibold text-slate-800">{{ formatMoney(amount) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics for period -->
                <div class="mb-8">
                    <h2 class="text-lg font-bold text-indigo-950 mb-4">Analytics for this period</h2>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                            <p class="text-sm font-medium text-slate-500">Successful vs all transactions</p>
                            <div v-if="loadingOverview" class="mt-2 h-8 w-32 rounded bg-slate-100 animate-pulse" />
                            <div v-else-if="overview" class="mt-2 flex items-baseline gap-2 flex-wrap">
                                <span class="text-2xl font-bold text-slate-900">{{ overview.successful_transactions ?? 0 }}</span>
                                <span class="text-slate-500">successful</span>
                                <span class="text-slate-300">/</span>
                                <span class="text-xl font-semibold text-slate-700">{{ overview.total_transactions ?? 0 }}</span>
                                <span class="text-slate-500">total</span>
                                <span v-if="overview.transaction_success_rate != null" class="text-sm font-medium text-indigo-600">
                                    ({{ overview.transaction_success_rate }}% success rate)
                                </span>
                            </div>
                            <p v-else class="mt-2 text-slate-400 text-sm">—</p>
                        </div>
                        <div class="bg-white rounded-xl border border-slate-200 p-5 shadow-sm">
                            <p class="text-sm font-medium text-slate-500">Revenue (period)</p>
                            <div v-if="loadingOverview" class="mt-2 h-8 w-32 rounded bg-slate-100 animate-pulse" />
                            <p v-else-if="overview && overview.total_revenue != null" class="mt-2 text-2xl font-bold text-indigo-900">
                                P{{ Number(overview.total_revenue).toLocaleString(undefined, { minimumFractionDigits: 2 }) }}
                            </p>
                            <p v-else class="mt-2 text-slate-400 text-sm">—</p>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-200">
                                <h3 class="text-base font-semibold text-slate-900">Transactions over time</h3>
                            </div>
                            <div class="p-6 h-64 relative">
                                <div v-if="loadingTransactionsChart" class="absolute inset-0 flex items-center justify-center bg-white/80 z-10">
                                    <div class="animate-spin rounded-full h-10 w-10 border-2 border-indigo-600 border-t-transparent" />
                                </div>
                                <div v-else-if="!transactionsChartData.labels?.length" class="absolute inset-0 flex items-center justify-center text-slate-400 text-sm">
                                    No transaction data for this period
                                </div>
                                <canvas v-show="transactionsChartData.labels?.length" ref="transactionsChartCanvas" class="w-full h-full" />
                            </div>
                        </div>
                        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
                            <div class="px-6 py-4 border-b border-slate-200">
                                <h3 class="text-base font-semibold text-slate-900">Revenue over time</h3>
                            </div>
                            <div class="p-6 h-64 relative">
                                <div v-if="loadingRevenueChart" class="absolute inset-0 flex items-center justify-center bg-white/80 z-10">
                                    <div class="animate-spin rounded-full h-10 w-10 border-2 border-indigo-600 border-t-transparent" />
                                </div>
                                <div v-else-if="!revenueChartData.labels?.length" class="absolute inset-0 flex items-center justify-center text-slate-400 text-sm">
                                    No revenue data for this period
                                </div>
                                <canvas v-show="revenueChartData.labels?.length" ref="revenueChartCanvas" class="w-full h-full" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transactions -->
                <div class="bg-white border border-slate-200 rounded-3xl shadow-sm overflow-hidden">
                    <div class="px-8 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <h2 class="text-lg font-bold text-indigo-950">Transactions for this period</h2>
                        <div class="flex items-center gap-2 flex-wrap">
                            <a
                                :href="transactionsCsvUrl"
                                class="h-10 px-4 flex items-center gap-2 rounded-xl bg-emerald-50 text-emerald-700 font-semibold hover:bg-emerald-100 transition-all text-sm"
                            >
                                <Download :size="14" />
                                Download CSV
                            </a>
                            <a
                                :href="transactionsExcelUrl"
                                class="h-10 px-4 flex items-center gap-2 rounded-xl bg-emerald-50 text-emerald-700 font-semibold hover:bg-emerald-100 transition-all text-sm"
                            >
                                <Download :size="14" />
                                Download Excel
                            </a>
                        </div>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full border-collapse">
                            <thead>
                                <tr class="bg-slate-50/80 border-b border-slate-100">
                                    <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-wider text-left">MSISDN</th>
                                    <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-wider text-left">Status</th>
                                    <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-wider text-left">Amount</th>
                                    <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-wider text-left">Funds Before</th>
                                    <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-wider text-left">Funds After</th>
                                    <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-wider text-left">Rating</th>
                                    <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-wider text-center">Auto Billing</th>
                                    <th class="px-6 py-3 text-[10px] font-black text-slate-400 uppercase tracking-wider text-left">Date</th>
                                </tr>
                            </thead>
                            <tbody v-if="loadingTransactions && transactionsPayload.data.length === 0" class="divide-y divide-slate-100">
                                <tr v-for="i in 5" :key="'sk-' + i" class="bg-white">
                                    <td class="px-6 py-4"><div class="h-4 w-24 rounded bg-slate-100 animate-pulse" /></td>
                                    <td class="px-6 py-4"><div class="h-4 w-20 rounded bg-slate-100 animate-pulse" /></td>
                                    <td class="px-6 py-4"><div class="h-4 w-16 rounded bg-slate-100 animate-pulse" /></td>
                                    <td class="px-6 py-4"><div class="h-4 w-16 rounded bg-slate-100 animate-pulse" /></td>
                                    <td class="px-6 py-4"><div class="h-4 w-16 rounded bg-slate-100 animate-pulse" /></td>
                                    <td class="px-6 py-4"><div class="h-4 w-16 rounded bg-slate-100 animate-pulse" /></td>
                                    <td class="px-6 py-4"><div class="h-4 w-12 rounded bg-slate-100 animate-pulse mx-auto" /></td>
                                    <td class="px-6 py-4"><div class="h-4 w-20 rounded bg-slate-100 animate-pulse" /></td>
                                </tr>
                            </tbody>
                            <tbody v-else-if="transactionsPayload.data.length" class="divide-y divide-slate-100">
                                <tr
                                    v-for="tx in transactionsPayload.data"
                                    :key="tx.id"
                                    class="hover:bg-slate-50/50 transition-colors"
                                >
                                    <td class="px-6 py-3 text-sm font-medium text-slate-800">{{ tx.subscriber?.msisdn ?? '—' }}</td>
                                    <td class="px-6 py-3">
                                        <span
                                            :class="tx.is_successful ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800'"
                                            class="text-xs font-semibold px-2 py-0.5 rounded"
                                        >
                                            {{ tx.is_successful ? 'Successful' : 'Unsuccessful' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-sm font-semibold text-slate-800">{{ formatMoney(tx.amount) }}</td>
                                    <td class="px-6 py-3 text-sm text-slate-600 tabular-nums">{{ formatMoney(tx.funds_before_deduction) }}</td>
                                    <td class="px-6 py-3 text-sm text-slate-600 tabular-nums">{{ formatMoney(tx.is_successful ? tx.funds_after_deduction : tx.funds_before_deduction) }}</td>
                                    <td class="px-6 py-3 text-sm text-slate-600">{{ tx.rating_type ?? '—' }}</td>
                                    <td class="px-6 py-3 text-center">
                                        <span :class="tx.created_using_auto_billing ? 'text-emerald-600' : 'text-slate-400'" class="text-xs font-medium">
                                            {{ tx.created_using_auto_billing ? 'Yes' : 'No' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-3 text-sm text-slate-600">{{ tx.created_at ? moment(tx.created_at).format('MMM D, YYYY HH:mm') : '—' }}</td>
                                </tr>
                            </tbody>
                            <tbody v-else-if="!loadingTransactions && transactionsPayload.data.length === 0" class="divide-y divide-slate-100">
                                <tr>
                                    <td colspan="8" class="px-6 py-12 text-center text-slate-500 text-sm">
                                        No transactions in this period.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <Pagination
                        v-if="!loadingTransactions"
                        class="border-t border-slate-100"
                        :pagination-payload="transactionsPayload"
                        :api-mode="true"
                        @page-change="fetchTransactions"
                    />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script>
import { defineComponent, ref, computed, onMounted, onBeforeUnmount } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Partials/Pagination.vue';
import { ArrowLeft, ChevronRight, FileText, Download } from 'lucide-vue-next';
import moment from 'moment';
import axios from 'axios';
import { formatMoney } from '@/utils/formatMoney';

export default defineComponent({
    name: 'BillingReportsShow',
    components: { AppLayout, Link, Pagination, ArrowLeft, ChevronRight, FileText, Download },
    props: {
        projectPayload: { type: Object, default: null },
        billingReportPayload: { type: Object, default: null },
    },
    setup(props) {
        const overview = ref(null);
        const loadingOverview = ref(false);
        const transactionsChartData = ref({ labels: [], values: [], values_successful: [] });
        const revenueChartData = ref({ labels: [], values: [] });
        const loadingTransactionsChart = ref(false);
        const loadingRevenueChart = ref(false);
        const transactionsPayload = ref({ data: [], current_page: 1, last_page: 1, total: 0 });
        const loadingTransactions = ref(true);
        const chartInstances = ref({});

        const report = computed(() => props.billingReportPayload);
        const periodLabel = computed(() => {
            const r = report.value;
            if (!r?.month || !r?.year) return '—';
            const m = moment().month(r.month - 1).year(r.year);
            return m.format('MMMM YYYY');
        });

        const periodStart = computed(() => {
            const r = report.value;
            if (!r?.month || !r?.year) return null;
            return moment({ year: r.year, month: r.month - 1, day: 1 }).format('YYYY-MM-DD');
        });
        const periodEnd = computed(() => {
            const r = report.value;
            if (!r?.month || !r?.year) return null;
            return moment({ year: r.year, month: r.month - 1 }).endOf('month').format('YYYY-MM-DD');
        });

        const costBreakdownList = computed(() => {
            const cb = report.value?.cost_breakdown;
            if (!cb || typeof cb !== 'object') return [];
            return Object.entries(cb);
        });

        const transactionsCsvUrl = computed(() => {
            if (!props.billingReportPayload?.id || !props.projectPayload?.id) return '#';
            return route('billing.report.transactions.csv', {
                project: props.projectPayload.id,
                billing_report: props.billingReportPayload.id,
            });
        });
        const transactionsExcelUrl = computed(() => {
            if (!props.billingReportPayload?.id || !props.projectPayload?.id) return '#';
            return route('billing.report.transactions.excel', {
                project: props.projectPayload.id,
                billing_report: props.billingReportPayload.id,
            });
        });

        const detailedPdfUrl = computed(() => {
            if (!props.billingReportPayload?.id || !props.projectPayload?.id) return '#';
            return route('billing.report.download.pdf', {
                project: props.projectPayload.id,
                billing_report: props.billingReportPayload.id,
            });
        });

        const invoicePdfUrl = computed(() => {
            if (!props.billingReportPayload?.id || !props.projectPayload?.id) return '#';
            return route('billing.report.download.invoice.pdf', {
                project: props.projectPayload.id,
                billing_report: props.billingReportPayload.id,
            });
        });

        const apiBase = computed(() => {
            if (!props.projectPayload?.id) return '';
            return `/api/projects/${props.projectPayload.id}/analytics`;
        });

        return {
            formatMoney,
            moment,
            overview,
            loadingOverview,
            transactionsChartData,
            revenueChartData,
            loadingTransactionsChart,
            loadingRevenueChart,
            transactionsPayload,
            loadingTransactions,
            chartInstances,
            periodLabel,
            periodStart,
            periodEnd,
            costBreakdownList,
            transactionsCsvUrl,
            transactionsExcelUrl,
            detailedPdfUrl,
            invoicePdfUrl,
            apiBase,
        };
    },
    methods: {
        async fetchOverview() {
            if (!this.apiBase || !this.periodStart || !this.periodEnd) return;
            try {
                this.loadingOverview = true;
                const { data } = await axios.get(`${this.apiBase}/overview`, {
                    params: { range: 'custom', start: this.periodStart, end: this.periodEnd },
                });
                this.overview = data;
            } catch (_) {
                this.overview = null;
            } finally {
                this.loadingOverview = false;
            }
        },
        async fetchTransactionsOverTime() {
            if (!this.apiBase || !this.periodStart || !this.periodEnd) return;
            try {
                this.loadingTransactionsChart = true;
                const { data } = await axios.get(`${this.apiBase}/transactions-over-time`, {
                    params: { range: 'custom', start: this.periodStart, end: this.periodEnd },
                });
                this.transactionsChartData = {
                    labels: data.labels ?? [],
                    values: data.values ?? [],
                    values_successful: data.values_successful ?? [],
                };
                this.$nextTick(() => this.renderTransactionsChart());
            } catch (_) {
                this.transactionsChartData = { labels: [], values: [], values_successful: [] };
            } finally {
                this.loadingTransactionsChart = false;
            }
        },
        async fetchRevenueOverTime() {
            if (!this.apiBase || !this.periodStart || !this.periodEnd) return;
            try {
                this.loadingRevenueChart = true;
                const { data } = await axios.get(`${this.apiBase}/revenue-over-time`, {
                    params: { range: 'custom', start: this.periodStart, end: this.periodEnd },
                });
                this.revenueChartData = { labels: data.labels ?? [], values: data.values ?? [] };
                this.$nextTick(() => this.renderRevenueChart());
            } catch (_) {
                this.revenueChartData = { labels: [], values: [] };
            } finally {
                this.loadingRevenueChart = false;
            }
        },
        renderTransactionsChart() {
            this.destroyChart('transactions');
            const canvas = this.$refs.transactionsChartCanvas;
            const d = this.transactionsChartData;
            if (!canvas || !d.labels?.length) return;
            const Chart = window.Chart;
            if (!Chart) return;
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
        renderRevenueChart() {
            this.destroyChart('revenue');
            const canvas = this.$refs.revenueChartCanvas;
            const d = this.revenueChartData;
            if (!canvas || !d.labels?.length) return;
            const Chart = window.Chart;
            if (!Chart) return;
            this.chartInstances.revenue = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: d.labels,
                    datasets: [{ label: 'Revenue', data: d.values, borderColor: '#059669', backgroundColor: 'rgba(5, 150, 105, 0.15)', fill: true, tension: 0.2 }],
                },
                options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { x: { grid: { display: false } }, y: { beginAtZero: true, ticks: { callback: (v) => 'P' + Number(v).toLocaleString() } } } },
            });
        },
        destroyChart(key) {
            if (this.chartInstances[key]) {
                this.chartInstances[key].destroy();
                this.chartInstances[key] = null;
            }
        },
        fetchTransactions(page = 1) {
            if (!this.billingReportPayload?.id || !this.projectPayload?.id) return;
            const url = route('billing.report.transactions', {
                project: this.projectPayload.id,
                billing_report: this.billingReportPayload.id,
            });
            this.loadingTransactions = true;
            window.axios.get(url, { params: { page, per_page: 15 } }).then(({ data }) => {
                this.transactionsPayload = data.transactionsPayload;
            }).catch(() => {
                this.transactionsPayload = { data: [], current_page: 1, last_page: 1, total: 0 };
            }).finally(() => {
                this.loadingTransactions = false;
            });
        },
    },
    async mounted() {
        if (typeof window.Chart === 'undefined') {
            const Chart = (await import('chart.js/auto')).default;
            window.Chart = Chart;
        }
        await this.fetchOverview();
        await Promise.all([this.fetchTransactionsOverTime(), this.fetchRevenueOverTime()]);
        this.fetchTransactions(1);
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
