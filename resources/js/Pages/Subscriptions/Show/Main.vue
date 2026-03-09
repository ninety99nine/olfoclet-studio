<template>
    <app-layout title="Subscription">
        <div class="min-h-screen bg-slate-50/50 p-4 lg:p-8 font-sans antialiased text-slate-700">
            <div class="max-w-[1400px] mx-auto space-y-6">
                <!-- Back + breadcrumb (same pattern as Topics / Messages) -->
                <div class="flex flex-wrap items-center gap-2">
                    <Link
                        :href="route('show.subscriptions', { project: project?.id })"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 text-xs font-semibold hover:bg-slate-50 hover:border-slate-300 transition-all"
                    >
                        <ArrowLeft :size="14" />
                        Back
                    </Link>
                    <span class="flex items-center gap-2">
                        <ChevronRight :size="14" class="text-slate-300 shrink-0" />
                        <Link
                            :href="route('show.subscriptions', { project: project?.id })"
                            class="text-indigo-600 hover:text-indigo-700 text-xs font-semibold hover:underline"
                        >
                            Subscriptions
                        </Link>
                    </span>
                    <span class="flex items-center gap-2">
                        <ChevronRight :size="14" class="text-slate-300 shrink-0" />
                        <span class="text-slate-600 text-xs font-medium truncate max-w-[200px]" :title="subscriptionTitle">
                            {{ subscriptionTitle }}
                        </span>
                    </span>
                </div>

                <!-- Header card -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 lg:p-8">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                            <div class="flex items-start gap-4">
                                <div class="h-14 w-14 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                                    <CreditCard :size="24" class="text-indigo-600" />
                                </div>
                                <div>
                                    <h1 class="text-2xl font-black tracking-tight text-indigo-950">
                                        {{ subscription?.pricing_plan?.name ?? '—' }}
                                    </h1>
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">
                                        Subscription #{{ subscription?.id ?? '—' }}
                                    </p>
                                    <p class="text-sm text-slate-500 mt-2">
                                        {{ subscription?.created_at ? moment(subscription.created_at).format('DD MMM YYYY [at] HH:mm') : '—' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Subscription details -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-8 pt-6 border-t border-slate-100">
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Plan</p>
                                <p class="text-sm font-bold text-indigo-900 mt-0.5">{{ subscription?.pricing_plan?.name ?? '—' }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Start</p>
                                <p class="text-sm font-bold text-indigo-900 mt-0.5">{{ formatDate(subscription?.start_at) }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">End</p>
                                <p class="text-sm font-bold text-indigo-900 mt-0.5">{{ formatDate(subscription?.end_at) }}</p>
                            </div>
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Status</p>
                                <p class="mt-0.5">
                                    <SubscriptionActiveStatusBadge :subscription="subscription" class="scale-90" />
                                </p>
                            </div>
                        </div>

                        <div v-if="subscription?.cancelled_at" class="mt-6">
                            <div class="bg-slate-50/80 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">Cancelled at</p>
                                <p class="text-sm text-slate-700">{{ formatDate(subscription?.cancelled_at) }}</p>
                            </div>
                        </div>

                        <!-- Subscriber -->
                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-3">Subscriber</p>
                            <Link
                                v-if="subscription?.subscriber"
                                :href="route('show.subscriber', { project: project?.id, subscriber: subscription.subscriber.id })"
                                class="inline-flex items-center gap-3 p-4 rounded-xl bg-slate-50 border border-slate-100 hover:bg-indigo-50 hover:border-indigo-100 transition-all group"
                            >
                                <div class="h-10 w-10 rounded-xl bg-white border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-indigo-600 group-hover:border-indigo-100 transition-all">
                                    <User :size="18" class="text-xs" />
                                </div>
                                <div class="text-left">
                                    <div class="text-sm font-bold text-indigo-950 group-hover:text-indigo-700">{{ subscription.subscriber?.msisdn ?? '—' }}</div>
                                    <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">View subscriber →</div>
                                </div>
                            </Link>
                            <p v-else class="text-sm text-slate-400">No subscriber linked</p>
                        </div>

                        <!-- Billing transaction -->
                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-3">Billing transaction</p>
                            <div
                                v-if="subscription?.latest_billing_transaction"
                                role="button"
                                tabindex="0"
                                class="block rounded-xl border border-slate-200 bg-white overflow-hidden hover:border-indigo-200 hover:bg-slate-50/50 transition-all group cursor-pointer"
                                @click="goToTransaction"
                                @keydown.enter="goToTransaction"
                                @keydown.space.prevent="goToTransaction"
                            >
                                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 p-4 sm:p-5">
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Amount</p>
                                        <p class="text-sm font-bold text-slate-800 mt-0.5">{{ formatAmount(subscription.latest_billing_transaction) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Type</p>
                                        <p class="text-sm font-bold text-slate-800 mt-0.5">{{ subscription.latest_billing_transaction.created_using_auto_billing ? 'Auto' : 'User' }}</p>
                                    </div>
                                    <div class="text-left">
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Status</p>
                                        <p class="mt-0.5 inline-block">
                                            <span
                                                v-tooltip="{
                                                    value: getBillingTransactionTooltipHtml(subscription.latest_billing_transaction),
                                                    escape: false,
                                                    class: 'billing-detail-tooltip',
                                                    position: 'bottom'
                                                }"
                                                class="inline-block cursor-pointer w-fit"
                                                @click.stop="openBillingDetailModal()"
                                            >
                                                <BillingStatusBadge :billing-transaction="subscription.latest_billing_transaction" class="scale-90" />
                                            </span>
                                        </p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Date</p>
                                        <p class="text-sm font-bold text-slate-800 mt-0.5">{{ formatDate(subscription.latest_billing_transaction.created_at) }}</p>
                                    </div>
                                </div>
                                <template v-if="subscription.latest_billing_transaction.description || (!subscription.latest_billing_transaction.is_successful && (subscription.latest_billing_transaction.failure_reason || subscription.latest_billing_transaction.failure_type))">
                                    <div class="border-t border-slate-100 px-4 sm:px-5 py-3 space-y-2">
                                        <div v-if="subscription.latest_billing_transaction.description">
                                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Description</p>
                                            <p class="text-sm text-slate-700 mt-0.5">{{ subscription.latest_billing_transaction.description }}</p>
                                        </div>
                                        <template v-if="!subscription.latest_billing_transaction.is_successful">
                                            <div v-if="subscription.latest_billing_transaction.failure_type">
                                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Failure type</p>
                                                <p class="text-sm text-slate-700 mt-0.5">{{ subscription.latest_billing_transaction.failure_type }}</p>
                                            </div>
                                            <div v-if="subscription.latest_billing_transaction.failure_reason">
                                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider">Failure reason</p>
                                                <p class="text-sm text-slate-700 mt-0.5">{{ subscription.latest_billing_transaction.failure_reason }}</p>
                                            </div>
                                        </template>
                                    </div>
                                </template>
                                <div class="border-t border-slate-100 px-4 sm:px-5 py-3 flex items-center justify-end">
                                    <span class="text-[10px] font-bold text-slate-500 uppercase tracking-widest group-hover:text-indigo-600 transition-colors">View transaction →</span>
                                </div>
                            </div>
                            <p v-else class="text-sm text-slate-400">No billing transaction</p>
                        </div>

                        <!-- Billing transaction detail modal (fields + COPY DETAILS section) -->
                        <Dialog
                            v-model:visible="billingDetailModalVisible"
                            :header="billingDetailModalTitle"
                            :modal="true"
                            :closable="true"
                            :dismissable-mask="true"
                            class="w-full max-w-md"
                            @hide="closeBillingDetailModal"
                        >
                            <template v-if="billingDetailRecord">
                                <div class="space-y-4">
                                    <div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-2.5 text-sm">
                                        <span class="text-slate-500 font-semibold uppercase text-xs">STATUS</span>
                                        <span>
                                            <Tag
                                                :value="billingDetailRecord.is_successful ? 'Successful' : 'Unsuccessful'"
                                                :severity="billingDetailRecord.is_successful ? 'success' : 'warn'"
                                                :class="['text-xs', { 'tag-amber': !billingDetailRecord.is_successful }]"
                                            />
                                        </span>
                                        <span class="text-slate-500 font-semibold uppercase text-xs">DATE</span>
                                        <span class="text-slate-800">{{ billingDetailRecord.created_at ? moment(billingDetailRecord.created_at).format('DD MMM YYYY HH:mm') : '—' }}</span>
                                        <span class="text-slate-500 font-semibold uppercase text-xs">AMOUNT</span>
                                        <span class="text-slate-800">{{ formatBillingAmountForModal(billingDetailRecord.amount) }}</span>
                                        <span class="text-slate-500 font-semibold uppercase text-xs">DESCRIPTION</span>
                                        <span class="text-slate-800 break-words">{{ billingDetailRecord.description || '—' }}</span>
                                        <template v-if="!billingDetailRecord.is_successful">
                                            <span class="text-slate-500 font-semibold uppercase text-xs">FAILURE</span>
                                            <span class="text-slate-800 break-words">{{ billingDetailRecord.failure_reason || '—' }}</span>
                                            <span class="text-slate-500 font-semibold uppercase text-xs">FAILURE TYPE</span>
                                            <span class="text-slate-800">{{ billingDetailRecord.failure_type || '—' }}</span>
                                        </template>
                                        <span class="text-slate-500 font-semibold uppercase text-xs">REF</span>
                                        <span class="text-slate-800 font-mono text-xs break-all">{{ billingDetailRecord.reference_code || '—' }}</span>
                                    </div>
                                    <div class="border-t border-slate-100 pt-4">
                                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">COPY DETAILS</p>
                                        <div class="grid grid-cols-2 gap-2">
                                            <Button :label="copyFeedback === 'all' ? 'Copied!' : 'Copy all details'" size="small" severity="secondary" text outlined class="text-xs detail-modal-copy-btn" @click="copyAllBillingDetails">
                                                <template #icon><Copy :size="12" /></template>
                                            </Button>
                                            <Button :label="copyFeedback === 'billingId' ? 'Copied!' : 'Copy billing ID'" size="small" severity="secondary" text outlined class="text-xs detail-modal-copy-btn" @click="copyDetailValue(String(billingDetailRecord.id), 'billingId')">
                                                <template #icon><Copy :size="12" /></template>
                                            </Button>
                                            <Button :label="copyFeedback === 'ref' ? 'Copied!' : 'Copy reference code'" size="small" severity="secondary" text outlined class="text-xs detail-modal-copy-btn" @click="copyDetailValue(billingDetailRecord.reference_code || '', 'ref')">
                                                <template #icon><Copy :size="12" /></template>
                                            </Button>
                                            <Button :label="copyFeedback === 'amount' ? 'Copied!' : 'Copy amount'" size="small" severity="secondary" text outlined class="text-xs detail-modal-copy-btn" @click="copyDetailValue(formatBillingAmountForModal(billingDetailRecord.amount), 'amount')">
                                                <template #icon><Copy :size="12" /></template>
                                            </Button>
                                            <Button :label="copyFeedback === 'description' ? 'Copied!' : 'Copy description'" size="small" severity="secondary" text outlined class="text-xs detail-modal-copy-btn" @click="copyDetailValue(billingDetailRecord.description || '', 'description')">
                                                <template #icon><Copy :size="12" /></template>
                                            </Button>
                                            <Button :label="copyFeedback === 'date' ? 'Copied!' : 'Copy date'" size="small" severity="secondary" text outlined class="text-xs detail-modal-copy-btn" @click="copyDetailValue(billingDetailRecord.created_at ? moment(billingDetailRecord.created_at).format('DD MMM YYYY HH:mm') : '', 'date')">
                                                <template #icon><Copy :size="12" /></template>
                                            </Button>
                                        </div>
                                    </div>
                                </div>
                                <p v-if="copyFeedback" class="mt-3 text-xs font-semibold text-green-600">{{ copyFeedback }}</p>
                            </template>
                        </Dialog>
                    </div>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import Tooltip from 'primevue/tooltip';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import AppLayout from '@/Layouts/AppLayout.vue';
import SubscriptionActiveStatusBadge from '@/Pages/Subscriptions/List/Partials/ActiveStatusBadge.vue';
import BillingStatusBadge from '@/Pages/Subscribers/List/Partials/BillingStatusBadge.vue';
import { CreditCard, User, ArrowLeft, ChevronRight, Copy } from 'lucide-vue-next';
import moment from 'moment';
import { formatMoney } from '@/utils/formatMoney';

export default defineComponent({
    name: 'SubscriptionShow',
    directives: { Tooltip },
    components: {
        AppLayout,
        Link,
        Dialog,
        Button,
        Tag,
        SubscriptionActiveStatusBadge,
        BillingStatusBadge,
        CreditCard,
        User,
        ArrowLeft,
        ChevronRight,
        Copy,
    },
    props: {
        subscription: { type: Object, required: true },
        project: { type: Object, required: true },
    },
    data() {
        return {
            billingDetailModalVisible: false,
            billingDetailRecord: null,
            copyFeedback: null,
        };
    },
    computed: {
        subscriptionTitle() {
            const plan = this.subscription?.pricing_plan?.name;
            const id = this.subscription?.id;
            return plan ? `${plan} (#${id})` : `#${id ?? '—'}`;
        },
        transactionShowUrl() {
            const tx = this.subscription?.latest_billing_transaction;
            if (!tx?.id || !this.project?.id) return '#';
            return route('show.transaction', {
                project: this.project.id,
                billing_transaction: tx.id,
            });
        },
        billingDetailModalTitle() {
            if (!this.billingDetailRecord || !this.billingDetailRecord.id) return 'Billing transaction';
            const type = this.billingDetailRecord.created_using_auto_billing ? 'AUTO BILLING' : 'USER BILLING';
            return `${type} #${this.billingDetailRecord.id}`;
        },
    },
    methods: {
        moment,
        formatDate(val) {
            if (!val) return '—';
            return moment(val).format('DD MMM YYYY HH:mm');
        },
        formatAmount(tx) {
            if (!tx) return '—';
            return formatMoney(tx.amount ?? tx);
        },
        getBillingTransactionTooltipHtml(tx) {
            if (!tx || !tx.id) return 'No billing transaction. Click for details.';
            const typeLabel = tx.created_using_auto_billing ? 'AUTO BILLING' : 'USER BILLING';
            const status = tx.is_successful ? 'Successful' : 'Unsuccessful';
            const statusClass = tx.is_successful ? 'detail-tooltip__badge detail-tooltip__badge--success' : 'detail-tooltip__badge detail-tooltip__badge--warn';
            const date = tx.created_at ? moment(tx.created_at).format('DD MMM YYYY HH:mm') : '—';
            const amount = this.formatBillingAmountForModal(tx.amount);
            const desc = tx.description || '—';
            const ref = tx.reference_code || '—';
            let html = `<div class="detail-tooltip__title">${typeLabel} #${tx.id}</div><div class="detail-tooltip__body">`;
            html += `<div class="detail-tooltip__row"><span class="detail-tooltip__label">STATUS</span><span class="${statusClass}">${status}</span></div>`;
            html += `<div class="detail-tooltip__row"><span class="detail-tooltip__label">DATE</span><span class="detail-tooltip__value">${date}</span></div>`;
            html += `<div class="detail-tooltip__row"><span class="detail-tooltip__label">AMOUNT</span><span class="detail-tooltip__value">${amount}</span></div>`;
            html += `<div class="detail-tooltip__row detail-tooltip__row--full"><span class="detail-tooltip__label">DESCRIPTION</span><span class="detail-tooltip__value detail-tooltip__value--wrap">${desc}</span></div>`;
            if (!tx.is_successful) {
                html += `<div class="detail-tooltip__row detail-tooltip__row--full"><span class="detail-tooltip__label">FAILURE</span><span class="detail-tooltip__value detail-tooltip__value--wrap">${tx.failure_reason || '—'}</span></div>`;
                html += `<div class="detail-tooltip__row"><span class="detail-tooltip__label">FAILURE TYPE</span><span class="detail-tooltip__badge detail-tooltip__badge--warn">${tx.failure_type || '—'}</span></div>`;
            }
            html += `<div class="detail-tooltip__row"><span class="detail-tooltip__label">REF</span><span class="detail-tooltip__value detail-tooltip__value--mono">${ref}</span></div>`;
            html += '</div><div class="detail-tooltip__click-hint">Click for details</div>';
            return html;
        },
        formatBillingAmountForModal(amount) {
            return formatMoney(amount);
        },
        goToTransaction() {
            if (this.transactionShowUrl && this.transactionShowUrl !== '#') {
                router.visit(this.transactionShowUrl);
            }
        },
        openBillingDetailModal() {
            const tx = this.subscription?.latest_billing_transaction;
            if (!tx) return;
            this.billingDetailRecord = tx;
            this.billingDetailModalVisible = true;
            this.copyFeedback = null;
        },
        async copyAllBillingDetails() {
            const r = this.billingDetailRecord;
            if (!r) return;
            const lines = [];
            lines.push(`Billing #${r.id}`);
            lines.push(`Status: ${r.is_successful ? 'Successful' : 'Unsuccessful'}`);
            lines.push(`Date: ${r.created_at ? moment(r.created_at).format('DD MMM YYYY HH:mm') : '—'}`);
            const amount = this.formatBillingAmountForModal(r.amount);
            lines.push(`Amount: ${amount}`);
            lines.push(`Description: ${r.description || '—'}`);
            if (!r.is_successful) {
                if (r.failure_type) lines.push(`Failure type: ${r.failure_type}`);
                if (r.failure_reason) lines.push(`Failure reason: ${r.failure_reason}`);
            }
            if (r.reference_code) lines.push(`Ref: ${r.reference_code}`);
            const text = lines.join('\n');
            try {
                await navigator.clipboard.writeText(text);
                this.copyFeedback = 'all';
                setTimeout(() => { this.copyFeedback = null; }, 1800);
            } catch (_) {}
        },
        closeBillingDetailModal() {
            this.billingDetailModalVisible = false;
            this.billingDetailRecord = null;
            this.copyFeedback = null;
        },
        copyDetailValue(text, key) {
            if (text == null || text === '') return;
            const str = String(text);
            navigator.clipboard.writeText(str).then(() => {
                this.copyFeedback = 'Copied to clipboard';
                setTimeout(() => { this.copyFeedback = null; }, 2000);
            }).catch(() => {});
        },
    },
});
</script>

<style>
.detail-modal-copy-btn { color: #64748b !important; }
.detail-modal-copy-btn:hover { color: #4f46e5 !important; }
.subscription-show-billing-tooltip {
    max-width: 420px;
    min-width: 280px;
    padding: 0;
    margin-left: -14px;
    border-radius: 12px;
    background: #fff !important;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 10px 25px -5px rgba(0, 0, 0, 0.15);
}
.subscription-show-billing-tooltip .p-tooltip-arrow { display: none; }
.subscription-show-billing-tooltip .p-tooltip-text {
    padding: 14px 16px;
    overflow-x: auto;
    max-height: 70vh;
    overflow-y: auto;
    background: transparent !important;
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    text-align: left;
}
.subscription-show-billing-tooltip .subscription-show-billing-tooltip__title {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: #1e293b;
    padding-bottom: 10px;
    margin-bottom: 10px;
    border-bottom: 1px solid #e2e8f0;
}
.subscription-show-billing-tooltip .subscription-show-billing-tooltip__body { display: flex; flex-direction: column; gap: 8px; }
.subscription-show-billing-tooltip .subscription-show-billing-tooltip__row {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 12px;
    font-size: 0.6875rem;
}
.subscription-show-billing-tooltip .subscription-show-billing-tooltip__label {
    flex-shrink: 0;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.subscription-show-billing-tooltip .subscription-show-billing-tooltip__value {
    color: #334155;
    font-weight: 500;
    word-break: break-word;
    text-align: right;
}
.subscription-show-billing-tooltip .subscription-show-billing-tooltip__row--full { flex-direction: column; align-items: flex-start; gap: 4px; }
.subscription-show-billing-tooltip .subscription-show-billing-tooltip__value--wrap { word-break: break-word; white-space: normal; }
.subscription-show-billing-tooltip .subscription-show-billing-tooltip__value--mono {
    font-family: ui-monospace, monospace;
    font-size: 0.7rem;
    word-break: keep-all;
    white-space: nowrap;
    color: #475569;
    min-width: 0;
}
.subscription-show-billing-tooltip .subscription-show-billing-tooltip__badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 0.6875rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.subscription-show-billing-tooltip .subscription-show-billing-tooltip__badge--success { background: #dcfce7; color: #166534; }
.subscription-show-billing-tooltip .subscription-show-billing-tooltip__badge--warn { background: #fef3c7; color: #92400e; }
.subscription-show-billing-tooltip .subscription-show-billing-tooltip__click-hint {
    margin-top: 10px;
    padding-top: 10px;
    border-top: 1px solid #e2e8f0;
    font-size: 0.75rem;
    font-weight: 600;
    color: #4f46e5;
    letter-spacing: 0.02em;
}
.subscription-show-billing-tooltip .subscription-show-billing-tooltip__click-hint::before {
    content: '';
    display: inline-block;
    width: 12px;
    height: 12px;
    margin-right: 6px;
    vertical-align: -2px;
    background: currentColor;
    mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'%3E%3Cpath d='M15 3h6v6M10 14L21 3M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6'/%3E%3C/svg%3E") center/contain no-repeat;
    -webkit-mask: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'%3E%3Cpath d='M15 3h6v6M10 14L21 3M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6'/%3E%3C/svg%3E") center/contain no-repeat;
}
</style>
