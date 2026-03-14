<template>
    <app-layout title="Subscriber">
        <div class="min-h-screen bg-slate-50/50 p-4 lg:p-8 font-sans antialiased text-slate-700">
            <ManageSubscriberModal
                v-model="isShowingModal"
                :action="modalAction"
                :subscriber="subscriberForModal"
                :project-permissions="projectPermissions"
                :show-addbutton="false"
                :deleting-subscriber="deletingSubscriber"
                @onDeleted="onDeleted"
                @onUpdated="onUpdated"
                @onCreated="onCreated"
                @request-delete="handleDeleteSubscriber"
            />

            <div class="max-w-[1400px] mx-auto space-y-6">
                <!-- Back + breadcrumb (same pattern as Topics / Messages) -->
                <div class="flex flex-wrap items-center gap-2">
                    <Link
                        :href="route('show.subscribers', { project: project?.id })"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg border border-slate-200 bg-white text-slate-600 text-xs font-semibold hover:bg-slate-50 hover:border-slate-300 transition-all"
                    >
                        <ArrowLeft :size="14" />
                        Back
                    </Link>
                    <span class="flex items-center gap-2">
                        <ChevronRight :size="14" class="text-slate-300 shrink-0" />
                        <Link
                            :href="route('show.subscribers', { project: project?.id })"
                            class="text-indigo-600 hover:text-indigo-700 text-xs font-semibold hover:underline"
                        >
                            Subscribers
                        </Link>
                    </span>
                    <span class="flex items-center gap-2">
                        <ChevronRight :size="14" class="text-slate-300 shrink-0" />
                        <span class="text-slate-600 text-xs font-medium truncate max-w-[200px]" :title="subscriber?.msisdn">
                            {{ subscriber?.msisdn ?? '—' }}
                        </span>
                    </span>
                </div>

                <!-- Header card -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="p-6 lg:p-8">
                        <div class="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-6">
                            <div class="flex items-start gap-4">
                                <div class="h-14 w-14 rounded-2xl bg-indigo-50 border border-indigo-100 flex items-center justify-center shrink-0">
                                    <User :size="24" class="text-indigo-600" />
                                </div>
                                <div>
                                    <h1 class="text-2xl font-black tracking-tight text-indigo-950">
                                        {{ subscriber?.msisdn ?? '—' }}
                                    </h1>
                                    <p class="text-xs font-bold text-slate-500 uppercase tracking-widest mt-1">
                                        Subscriber #{{ subscriber?.id ?? '—' }}
                                    </p>
                                    <p class="text-sm text-slate-500 mt-2">
                                        Joined {{ subscriber?.created_at ? moment(subscriber.created_at).format('DD MMM YYYY [at] HH:mm') : '—' }}
                                    </p>
                                </div>
                            </div>
                            <div class="flex flex-wrap items-center gap-3">
                                <button
                                    v-if="projectPermissions.includes('Manage subscribers')"
                                    @click="showModal('update')"
                                    class="h-10 px-4 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-slate-600 font-bold hover:bg-slate-50 hover:border-indigo-200 hover:text-indigo-700 transition-all text-sm"
                                >
                                    <Pencil :size="14" />
                                    Edit
                                </button>
                                <button
                                    v-if="projectPermissions.includes('Manage subscribers')"
                                    @click="showModal('delete')"
                                    class="h-10 px-4 flex items-center gap-2 rounded-xl bg-white border border-slate-200 text-rose-600 font-bold hover:bg-rose-50 hover:border-rose-200 transition-all text-sm"
                                >
                                    <Trash2 :size="14" />
                                    Delete
                                </button>
                            </div>
                        </div>

                        <!-- Quick stats and next schedules (ticking state in child so tooltips on this page do not re-render every second) -->
                        <SubscriberShowStats
                            :subscriber="subscriber"
                            :schedule-next-auto-billing="scheduleNextAutoBilling"
                            :schedule-next-sms="scheduleNextSms"
                        />

                        <!-- Latest subscription & billing -->
                        <div class="grid sm:grid-cols-2 gap-4 mt-6">
                            <Link
                                v-if="subscriber?.latest_subscription"
                                :href="getLatestSubscriptionLink(subscriber.latest_subscription)"
                                v-tooltip="{
                                    value: getSubscriptionTooltipHtml(subscriber.latest_subscription),
                                    escape: false,
                                    class: 'subscriber-detail-tooltip'
                                }"
                                class="block rounded-xl p-4 border border-slate-100 bg-slate-50/80 hover:bg-indigo-50/20 hover:border-indigo-100 transition-colors cursor-pointer"
                            >
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">Latest subscription</p>
                                <div class="flex flex-wrap items-center gap-2">
                                    <span
                                        class="inline-block cursor-pointer"
                                        @click.stop.prevent="openSubscriptionDetailModal(subscriber.latest_subscription)"
                                    >
                                        <SubscriptionStatusBadge :is-active="!!subscriber.latest_subscription.is_active" class="scale-90" />
                                    </span>
                                    <span class="text-sm text-slate-600">{{ getSubscriptionPlanName(subscriber.latest_subscription) }}</span>
                                    <span class="text-xs text-slate-400">
                                        {{ formatDate(subscriber.latest_subscription.end_at) }}
                                    </span>
                                </div>
                            </Link>
                            <div v-else class="bg-slate-50/80 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">Latest subscription</p>
                                <p class="text-sm text-slate-400">No subscription</p>
                            </div>
                            <div class="bg-slate-50/80 rounded-xl p-4 border border-slate-100">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-wider mb-2">Latest billing</p>
                                <div class="flex flex-col gap-1.5">
                                    <div
                                        v-for="entry in latestBillingEntries"
                                        :key="entry.type"
                                        class="flex items-center gap-2 flex-wrap"
                                    >
                                        <span class="text-[10px] font-bold text-slate-500 w-12">{{ entry.type }}</span>
                                        <template v-if="entry.transaction">
                                            <Link
                                                :href="route('show.transaction', { project: project?.id, billing_transaction: entry.transaction.id })"
                                                v-tooltip="{
                                                    value: getBillingTransactionTooltipHtml(entry.transaction, entry.type === 'User' ? 'USER BILLING' : 'AUTO BILLING'),
                                                    escape: false,
                                                    class: 'billing-detail-tooltip',
                                                    position: 'bottom'
                                                }"
                                                class="inline-flex items-center gap-2 cursor-pointer hover:opacity-90"
                                            >
                                                <BillingStatusBadge
                                                    :billing-transaction="entry.transaction"
                                                    class="scale-90"
                                                />
                                                <span class="text-xs text-slate-500 tabular-nums">{{ formatDate(entry.transaction.created_at) }}</span>
                                            </Link>
                                        </template>
                                        <template v-else>
                                            <span class="text-sm text-slate-400">—</span>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Metadata (collapsible) -->
                        <div class="mt-6 pt-6 border-t border-slate-100">
                            <button
                                type="button"
                                class="flex items-center gap-2 text-sm font-bold text-slate-600 hover:text-indigo-600"
                                @click="showMetadata = !showMetadata"
                            >
                                <ChevronDown :size="16" class="transition-transform" :class="{ 'rotate-180': showMetadata }" />
                                Metadata
                            </button>
                            <div v-show="showMetadata" class="mt-2 p-4 bg-slate-50 text-slate-800 rounded-xl text-xs font-mono overflow-x-auto border border-slate-200">
                                <template v-if="metadataHasData">
                                    <pre class="m-0">{{ JSON.stringify(resolvedMetadata, null, 2) }}</pre>
                                </template>
                                <p v-else class="m-0 text-slate-500">No metadata for this subscriber.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tabs -->
                <div class="bg-white border border-slate-200 rounded-2xl shadow-sm overflow-hidden">
                    <div class="border-b border-slate-200 flex overflow-x-auto overflow-y-hidden">
                        <button
                            v-for="tab in tabs"
                            :key="tab.id"
                            type="button"
                            class="px-6 py-4 text-sm font-bold whitespace-nowrap transition-colors border-b-2 -mb-px"
                            :class="activeTab === tab.id ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
                            @click="activeTab = tab.id"
                        >
                            {{ tab.label }} ({{ tabCount(tab.id) }})
                        </button>
                    </div>
                    <div class="p-6 min-h-[280px] overflow-visible">
                        <!-- Subscriptions -->
                        <div v-show="activeTab === 'subscriptions'" class="space-y-4">
                            <div v-if="loadingTabs.subscriptions" class="flex items-center justify-center py-12">
                                <RefreshCw :size="24" class="text-indigo-500 animate-spin" />
                            </div>
                            <template v-else>
                                <table v-if="tabData.subscriptions.data?.length" class="w-full border-collapse text-sm">
                                    <thead>
                                        <tr class="border-b border-slate-200">
                                            <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">Plan</th>
                                            <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">Start</th>
                                            <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">End</th>
                                            <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">Status</th>
                                            <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">Transaction</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <tr
                                            v-for="sub in tabData.subscriptions.data"
                                            :key="sub.id"
                                            class="hover:bg-indigo-50/20 cursor-pointer transition-colors"
                                            @click="goToSubscription(sub.id)"
                                        >
                                            <td class="py-3 px-2 font-medium text-slate-800">{{ sub.pricing_plan?.name ?? '—' }}</td>
                                            <td class="py-3 px-2 text-slate-600">{{ formatDate(sub.start_at) }}</td>
                                            <td class="py-3 px-2 text-slate-600">{{ formatDate(sub.end_at) }}</td>
                                            <td class="py-3 px-2" @click.stop>
                                                <span
                                                    v-tooltip="{
                                                        value: getSubscriptionTooltipHtml(sub),
                                                        escape: false,
                                                        class: 'billing-detail-tooltip',
                                                        position: 'bottom'
                                                    }"
                                                    class="inline-block w-fit cursor-pointer"
                                                    @click="openSubscriptionDetailModal(sub)"
                                                >
                                                    <SubscriptionStatusBadge :is-active="!!sub.is_active" class="scale-90" />
                                                </span>
                                            </td>
                                            <td class="py-3 px-2" @click.stop>
                                                <Link
                                                    v-if="getBillingTransaction(sub)?.id"
                                                    :href="route('show.transaction', { project: project?.id, billing_transaction: getBillingTransaction(sub).id })"
                                                    v-tooltip="{
                                                        value: getBillingTransactionTooltipHtml(getBillingTransaction(sub), getBillingTransaction(sub).created_using_auto_billing ? 'AUTO BILLING' : 'USER BILLING'),
                                                        escape: false,
                                                        class: 'billing-detail-tooltip',
                                                        position: 'bottom'
                                                    }"
                                                    class="text-indigo-600 hover:text-indigo-800 font-medium text-sm cursor-pointer"
                                                >
                                                    #{{ getBillingTransaction(sub).id }}
                                                </Link>
                                                <span
                                                    v-else
                                                    v-tooltip="{
                                                        value: getTrialSubscriptionTooltipHtml(sub),
                                                        escape: false,
                                                        class: 'billing-detail-tooltip',
                                                        position: 'bottom'
                                                    }"
                                                >
                                                    <Tag value="Trial" severity="secondary" class="text-xs trial-badge" />
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p v-else class="text-slate-500 text-center py-8">No subscriptions.</p>
                                <Pagination
                                    v-if="tabData.subscriptions.total > 0 && !loadingTabs.subscriptions"
                                    :pagination-payload="tabData.subscriptions"
                                    :api-mode="true"
                                    @page-change="(p) => fetchTab('subscriptions', p)"
                                />
                            </template>
                        </div>

                        <!-- Messages -->
                        <div v-show="activeTab === 'messages'" class="space-y-4">
                            <div v-if="loadingTabs.messages" class="flex items-center justify-center py-12">
                                <RefreshCw :size="24" class="text-indigo-500 animate-spin" />
                            </div>
                            <template v-else>
                                <table v-if="tabData.messages.data?.length" class="w-full border-collapse text-sm">
                                    <thead>
                                        <tr class="border-b border-slate-200">
                                            <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">Type</th>
                                            <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">Content</th>
                                            <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">Status</th>
                                            <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">Delivery</th>
                                            <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <tr
                                            v-for="msg in tabData.messages.data"
                                            :key="msg.id"
                                            class="hover:bg-slate-50/50 cursor-pointer transition-colors"
                                            @click="goToSubscriberMessage(msg.id)"
                                        >
                                            <td class="py-3 px-2">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800 whitespace-nowrap">{{ formatMessageType(msg.type) }}</span>
                                            </td>
                                            <td class="py-3 px-2 text-slate-600 max-w-md whitespace-normal break-words">{{ msg.content ?? '—' }}</td>
                                            <td class="py-3 px-2">
                                                <SubscriberMessageStatusBadge :subscriber-message="msg" class="scale-90 origin-left" />
                                            </td>
                                            <td class="py-3 px-2">
                                                <SubscriberMessageDeliveryStatusBadge :subscriber-message="msg" class="scale-90 origin-left" />
                                            </td>
                                            <td class="py-3 px-2 text-slate-500 text-xs">{{ formatDate(msg.created_at) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p v-else class="text-slate-500 text-center py-8">No messages.</p>
                                <Pagination
                                    v-if="tabData.messages.total > 0 && !loadingTabs.messages"
                                    :pagination-payload="tabData.messages"
                                    :api-mode="true"
                                    @page-change="(p) => fetchTab('messages', p)"
                                />
                            </template>
                        </div>

                        <!-- Billing (user-initiated and auto-billed) -->
                        <div v-show="activeTab === 'billing'" class="space-y-4">
                            <p class="text-xs text-slate-500 mb-2">
                                User-initiated and auto-billed transactions.
                            </p>
                            <div v-if="loadingTabs.billing" class="flex items-center justify-center py-12">
                                <RefreshCw :size="24" class="text-indigo-500 animate-spin" />
                            </div>
                            <template v-else>
                                <table v-if="tabData.billing.data?.length" class="w-full border-collapse text-sm">
                                    <thead>
                                        <tr class="border-b border-slate-200">
                                            <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">Amount</th>
                                            <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">Funds before</th>
                                            <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">Funds after</th>
                                            <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">Type</th>
                                            <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">Status</th>
                                            <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">Failure reason</th>
                                            <th class="text-left py-3 px-2 text-[10px] font-black text-slate-400 uppercase tracking-wider">Date</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        <tr
                                            v-for="tx in tabData.billing.data"
                                            :key="tx.id"
                                            class="hover:bg-indigo-50/20 cursor-pointer transition-colors"
                                            @click="goToBillingTransaction(tx.id)"
                                        >
                                            <td class="py-3 px-2 font-medium text-slate-800">{{ formatAmount(tx) }}</td>
                                            <td class="py-3 px-2 text-slate-600 tabular-nums">{{ formatMoney(tx.funds_before_deduction) }}</td>
                                            <td class="py-3 px-2 text-slate-600 tabular-nums">{{ formatMoney(tx.is_successful ? tx.funds_after_deduction : tx.funds_before_deduction) }}</td>
                                            <td class="py-3 px-2 text-slate-600">{{ tx.created_using_auto_billing ? 'Auto' : 'User' }}</td>
                                            <td class="py-3 px-2" @click.stop>
                                                <span
                                                    v-tooltip="{
                                                        value: getBillingTransactionTooltipHtml(tx, tx.created_using_auto_billing ? 'AUTO BILLING' : 'USER BILLING'),
                                                        escape: false,
                                                        class: 'billing-detail-tooltip',
                                                        position: 'bottom'
                                                    }"
                                                    class="inline-block cursor-pointer"
                                                    @click="openBillingDetailModalFromTransaction(tx)"
                                                >
                                                    <BillingStatusBadge :billing-transaction="tx" class="scale-90" />
                                                </span>
                                            </td>
                                            <td class="py-3 px-2 text-slate-600 text-xs max-w-[200px] truncate" :title="tx.failure_reason || (tx.failure_type ? tx.failure_type : null)">
                                                <template v-if="!tx.is_successful && (tx.failure_reason || tx.failure_type)">
                                                    {{ tx.failure_reason || tx.failure_type }}
                                                </template>
                                                <span v-else class="text-slate-300">—</span>
                                            </td>
                                            <td class="py-3 px-2 text-slate-500 text-xs">{{ formatDate(tx.created_at) }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                                <p v-else class="text-slate-500 text-center py-8">No billing transactions.</p>
                                <Pagination
                                    v-if="tabData.billing.total > 0 && !loadingTabs.billing"
                                    :pagination-payload="tabData.billing"
                                    :api-mode="true"
                                    @page-change="(p) => fetchTab('billing', p)"
                                />
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Billing transaction detail modal (from Subscriptions tab status click) -->
        <Dialog
            v-model:visible="billingDetailModalVisible"
            :header="billingDetailModalTitle"
            :modal="true"
            :dismissableMask="true"
            :draggable="false"
            :style="{ width: '420px' }"
            :pt="{
                root: { class: 'rounded-3xl border-none shadow-2xl overflow-hidden' },
                header: { class: 'bg-white px-6 pt-5 pb-0 border-none text-indigo-900 font-black uppercase text-sm tracking-widest' },
                pcCloseButton: { root: { class: 'h-8 w-8 bg-slate-50 text-slate-400 hover:text-rose-500 transition-all !border-0 !border-none shadow-none' } },
                content: { class: 'pt-4 pb-6' }
            }"
            @hide="closeBillingDetailModal"
        >
            <template v-if="billingDetailRecord">
                <div class="space-y-4">
                    <div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-2.5 text-sm">
                        <span class="text-slate-500 font-semibold uppercase text-xs">STATUS</span>
                        <span>
                            <Tag :value="billingDetailRecord.is_successful ? 'Successful' : 'Unsuccessful'" :severity="billingDetailRecord.is_successful ? 'success' : 'warn'" :class="['text-xs', { 'tag-amber': !billingDetailRecord.is_successful }]" />
                        </span>
                        <span class="text-slate-500 font-semibold uppercase text-xs">DATE</span>
                        <span class="text-slate-800">{{ billingDetailRecord.created_at ? moment(billingDetailRecord.created_at).format('DD MMM YYYY HH:mm') : '—' }}</span>
                        <span class="text-slate-500 font-semibold uppercase text-xs">AMOUNT</span>
                        <span class="text-slate-800">{{ formatBillingAmount(billingDetailRecord.amount) }}</span>
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
                            <Button :label="copyFeedback === 'amount' ? 'Copied!' : 'Copy amount'" size="small" severity="secondary" text outlined class="text-xs detail-modal-copy-btn" @click="copyDetailValue(formatBillingAmount(billingDetailRecord.amount), 'amount')">
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
            </template>
            <template v-else>
                <p class="text-slate-500 text-sm">No billing transaction for this subscription.</p>
            </template>
        </Dialog>

        <!-- Subscription detail modal (same as Subscribers list page) -->
        <Dialog
            v-model:visible="subscriptionDetailModalVisible"
            :header="subscriptionDetailModalTitle"
            :modal="true"
            :dismissableMask="true"
            :draggable="false"
            :style="{ width: '420px' }"
            :pt="{
                root: { class: 'rounded-3xl border-none shadow-2xl overflow-hidden' },
                header: { class: 'bg-white px-6 pt-5 pb-0 border-none text-indigo-900 font-black uppercase text-sm tracking-widest' },
                pcCloseButton: { root: { class: 'h-8 w-8 bg-slate-50 text-slate-400 hover:text-rose-500 transition-all !border-0 !border-none shadow-none' } },
                content: { class: 'pt-4 pb-6' }
            }"
            @hide="closeSubscriptionDetailModal"
        >
            <template v-if="subscriptionDetailRecord">
                <div class="space-y-4">
                    <div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-2.5 text-sm">
                        <span class="text-slate-500 font-semibold uppercase text-xs">Plan</span>
                        <span class="text-slate-800">{{ getSubscriptionPlanName(subscriptionDetailRecord) }}</span>
                        <span class="text-slate-500 font-semibold uppercase text-xs">Start</span>
                        <span class="text-slate-800">{{ formatSubscriptionDate(subscriptionDetailRecord.start_at) }}</span>
                        <span class="text-slate-500 font-semibold uppercase text-xs">End</span>
                        <span class="text-slate-800">{{ formatSubscriptionDate(subscriptionDetailRecord.end_at) }}</span>
                        <span class="text-slate-500 font-semibold uppercase text-xs">Status</span>
                        <span>
                            <Tag :value="subscriptionDetailRecord.is_active ? 'Active' : 'Inactive'" :severity="subscriptionDetailRecord.is_active ? 'success' : 'warn'" :class="['text-xs', { 'tag-amber': !subscriptionDetailRecord.is_active }]" />
                        </span>
                        <template v-if="subscriptionDetailRecord.cancelled_at">
                            <span class="text-slate-500 font-semibold uppercase text-xs">Cancelled</span>
                            <span class="text-slate-800">{{ formatSubscriptionDate(subscriptionDetailRecord.cancelled_at) }}</span>
                        </template>
                        <template v-if="subscriptionDetailRecord.created_using_auto_billing">
                            <span class="col-span-2 text-slate-500 italic text-xs">Created via auto billing</span>
                        </template>
                    </div>
                    <div class="border-t border-slate-100 pt-4">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Quick actions</p>
                        <div class="flex flex-wrap gap-2">
                            <Button :label="copyFeedback === 'subscriptionId' ? 'Copied!' : 'Copy subscription ID'" size="small" text outlined class="text-xs detail-modal-copy-btn" @click="copyDetailValue(String(subscriptionDetailRecord.id), 'subscriptionId')">
                                <template #icon><Copy :size="12" /></template>
                            </Button>
                            <Button :label="copyFeedback === 'plan' ? 'Copied!' : 'Copy plan name'" size="small" text outlined class="text-xs detail-modal-copy-btn" @click="copyDetailValue(getSubscriptionPlanName(subscriptionDetailRecord), 'plan')">
                                <template #icon><Copy :size="12" /></template>
                            </Button>
                            <Button :label="copyFeedback === 'start' ? 'Copied!' : 'Copy start date'" size="small" text outlined class="text-xs detail-modal-copy-btn" @click="copyDetailValue(formatSubscriptionDate(subscriptionDetailRecord.start_at), 'start')">
                                <template #icon><Copy :size="12" /></template>
                            </Button>
                            <Button :label="copyFeedback === 'end' ? 'Copied!' : 'Copy end date'" size="small" text outlined class="text-xs detail-modal-copy-btn" @click="copyDetailValue(formatSubscriptionDate(subscriptionDetailRecord.end_at), 'end')">
                                <template #icon><Copy :size="12" /></template>
                            </Button>
                        </div>
                    </div>
                </div>
            </template>
        </Dialog>
    </app-layout>
</template>

<script>
import { defineComponent, ref, computed, watch } from 'vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import ManageSubscriberModal from '@/Pages/Subscribers/List/Partials/ManageSubscriberModal.vue';
import SubscriptionStatusBadge from '@/Pages/Subscribers/List/Partials/SubscriptionStatusBadge.vue';
import BillingStatusBadge from '@/Pages/Subscribers/List/Partials/BillingStatusBadge.vue';
import SubscriberMessageStatusBadge from '@/Pages/SubscriberMessages/List/Partials/SubscriberMessageStatusBadge.vue';
import SubscriberMessageDeliveryStatusBadge from '@/Pages/SubscriberMessages/List/Partials/SubscriberMessageDeliveryStatusBadge.vue';
import Pagination from '@/Partials/Pagination.vue';
import Dialog from 'primevue/dialog';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Tooltip from 'primevue/tooltip';
import { User, Pencil, Trash2, ArrowLeft, ChevronRight, ChevronDown, RefreshCw, Copy } from 'lucide-vue-next';
import moment from 'moment';
import { formatMoney } from '@/utils/formatMoney';
import SubscriberShowStats from './Partials/SubscriberShowStats.vue';

const emptyPaginated = () => ({ data: [], current_page: 1, last_page: 1, total: 0, from: 0, to: 0, links: [] });

export default defineComponent({
    name: 'SubscriberShow',
    components: {
        AppLayout,
        Link,
        ManageSubscriberModal,
        SubscriptionStatusBadge,
        BillingStatusBadge,
        SubscriberMessageStatusBadge,
        SubscriberMessageDeliveryStatusBadge,
        Pagination,
        Dialog,
        Button,
        Tag,
        User,
        Pencil,
        Trash2,
        ArrowLeft,
        ChevronRight,
        ChevronDown,
        RefreshCw,
        Copy,
        SubscriberShowStats,
    },
    directives: { Tooltip },
    props: {
        subscriber: { type: Object, required: true },
        project: { type: Object, required: true },
        scheduleNextAutoBilling: { type: Object, default: null },
        scheduleNextSms: { type: Object, default: null },
    },
    setup(props) {
        const projectPermissions = computed(() => usePage().props.projectPermissions ?? []);
        return { projectPermissions };
    },
    data() {
        return {
            activeTab: 'subscriptions',
            showMetadata: false,
            isShowingModal: false,
            modalAction: 'update',
            deletingSubscriber: false,
            loadingTabs: {
                subscriptions: false,
                messages: false,
                billing: false,
            },
            tabData: {
                subscriptions: emptyPaginated(),
                messages: emptyPaginated(),
                billing: emptyPaginated(),
            },
            tabs: [
                { id: 'subscriptions', label: 'Subscriptions' },
                { id: 'billing', label: 'Billing' },
                { id: 'messages', label: 'Messages' },
            ],
            billingDetailModalVisible: false,
            billingDetailRecord: null,
            subscriptionDetailModalVisible: false,
            subscriptionDetailRecord: null,
            copyFeedback: null,
        };
    },
    computed: {
        subscriberForModal() {
            return this.subscriber ?? null;
        },
        latestBillingEntries() {
            const user = this.subscriber?.latest_user_billing_transaction ?? null;
            const auto = this.subscriber?.latest_auto_billing_transaction ?? null;
            const withDate = (tx, type) => (tx ? { type, transaction: tx, created_at: tx.created_at } : null);
            const arr = [withDate(user, 'User'), withDate(auto, 'Auto')].filter(Boolean);
            arr.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
            const first = arr[0] ?? { type: 'User', transaction: null };
            const second = arr[1] ?? { type: first.type === 'User' ? 'Auto' : 'User', transaction: null };
            return [first, second];
        },
        billingDetailModalTitle() {
            if (!this.billingDetailRecord || !this.billingDetailRecord.id) return 'Billing transaction';
            const type = this.billingDetailRecord.created_using_auto_billing ? 'AUTO BILLING' : 'USER BILLING';
            return `${type} #${this.billingDetailRecord.id}`;
        },
        subscriptionDetailModalTitle() {
            if (!this.subscriptionDetailRecord?.id) return 'Subscription';
            return `Subscription #${this.subscriptionDetailRecord.id}`;
        },
        resolvedMetadata() {
            const meta = this.subscriber?.metadata;
            if (meta == null) return null;
            if (typeof meta === 'object' && !Array.isArray(meta)) return meta;
            if (typeof meta === 'string') {
                try {
                    const parsed = JSON.parse(meta);
                    return typeof parsed === 'object' && parsed !== null && !Array.isArray(parsed) ? parsed : null;
                } catch (_) {
                    return null;
                }
            }
            return null;
        },
        metadataHasData() {
            const meta = this.resolvedMetadata;
            return !!meta && Object.keys(meta).length > 0;
        },
    },
    watch: {
        activeTab(id) {
            this.loadTabIfNeeded(id);
        },
    },
    mounted() {
        this.showMetadata = this.metadataHasData;
        this.loadTabIfNeeded(this.activeTab);
    },
    methods: {
        tabCount(tabId) {
            const s = this.subscriber;
            if (!s) return 0;
            switch (tabId) {
                case 'subscriptions': return s.subscriptions_count ?? 0;
                case 'messages': return s.messages_count ?? 0;
                case 'billing': return (s.user_billing_transactions_count ?? 0) + (s.auto_billing_transactions_count ?? 0);
                default: return 0;
            }
        },
        moment,
        formatMoney,
        formatDate(val) {
            if (!val) return '—';
            return moment(val).format('DD MMM YYYY HH:mm');
        },
        formatMessageType(type) {
            if (!type) return '—';
            const map = {
                Content: 'Content',
                PaymentConfirmation: 'Payment confirmation',
                AutoBillingReminder: 'Auto billing reminder',
                AutoBillingDisabled: 'Auto billing disabled',
            };
            return map[type] ?? type;
        },
        formatScheduleDate(isoString) {
            if (!isoString) return '—';
            return moment(isoString).format('DD MMM YYYY HH:mm');
        },
        getSubscriptionPlanName(sub) {
            return sub?.pricing_plan?.name ?? '—';
        },
        formatSubscriptionDate(val) {
            return val ? moment(val).format('DD MMM YYYY HH:mm') : '—';
        },
        getSubscriptionTooltipHtml(sub) {
            if (!sub) return '';
            const plan = this.getSubscriptionPlanName(sub);
            const start = this.formatSubscriptionDate(sub.start_at);
            const end = this.formatSubscriptionDate(sub.end_at);
            const status = sub.is_active ? 'Active' : 'Inactive';
            const statusClass = sub.is_active ? 'detail-tooltip__badge detail-tooltip__badge--success' : 'detail-tooltip__badge detail-tooltip__badge--warn';
            let html = `<div class="detail-tooltip__title">Subscription #${sub.id}</div><div class="detail-tooltip__body"><div class="detail-tooltip__row"><span class="detail-tooltip__label">Plan</span><span class="detail-tooltip__value">${plan}</span></div><div class="detail-tooltip__row"><span class="detail-tooltip__label">Start</span><span class="detail-tooltip__value">${start}</span></div><div class="detail-tooltip__row"><span class="detail-tooltip__label">End</span><span class="detail-tooltip__value">${end}</span></div><div class="detail-tooltip__row"><span class="detail-tooltip__label">Status</span><span class="${statusClass}">${status}</span></div>`;
            if (sub.cancelled_at) html += `<div class="detail-tooltip__row"><span class="detail-tooltip__label">Cancelled</span><span class="detail-tooltip__value">${this.formatSubscriptionDate(sub.cancelled_at)}</span></div>`;
            if (sub.created_using_auto_billing) html += '<div class="detail-tooltip__meta">Created via auto billing</div>';
            html += '</div><div class="detail-tooltip__click-hint">Click to view subscription</div>';
            return html;
        },
        formatBillingAmount(amount) {
            return formatMoney(amount);
        },
        getLatestSubscriptionLink(sub) {
            const tx = this.getBillingTransaction(sub);
            if (tx?.id && this.project?.id) {
                return route('show.transaction', { project: this.project.id, billing_transaction: tx.id });
            }
            if (sub?.id && this.project?.id) {
                return route('show.subscription', { project: this.project.id, subscription: sub.id });
            }
            return '#';
        },
        getBillingTransaction(sub) {
            return sub?.latest_billing_transaction ?? sub?.latestBillingTransaction ?? null;
        },
        getTrialSubscriptionTooltipHtml(sub) {
            const subId = sub?.id ?? '—';
            return `<div class="detail-tooltip__title">Subscription #${subId}</div><div class="detail-tooltip__body"><div class="detail-tooltip__row detail-tooltip__row--full"><span class="detail-tooltip__value detail-tooltip__value--wrap">Trial — no billing transaction for this subscription.</span></div></div>`;
        },
        getBillingTooltipForSubscription(sub) {
            const tx = this.getBillingTransaction(sub);
            if (!tx || !tx.id) {
                return this.getTrialSubscriptionTooltipHtml(sub) + '<div class="detail-tooltip__click-hint">Click for details</div>';
            }
            return this.getBillingTransactionTooltipHtml(tx, (tx.created_using_auto_billing ? 'AUTO BILLING' : 'USER BILLING'), 'Click to view transaction');
        },
        openBillingDetailModal(sub) {
            this.billingDetailRecord = this.getBillingTransaction(sub);
            this.billingDetailModalVisible = true;
            this.copyFeedback = null;
        },
        openBillingDetailModalFromTransaction(tx) {
            this.billingDetailRecord = tx;
            this.billingDetailModalVisible = true;
            this.copyFeedback = null;
        },
        closeBillingDetailModal() {
            this.billingDetailModalVisible = false;
            this.billingDetailRecord = null;
            this.copyFeedback = null;
        },
        openSubscriptionDetailModal(sub) {
            if (!sub) return;
            this.subscriptionDetailRecord = sub;
            this.subscriptionDetailModalVisible = true;
            this.copyFeedback = null;
        },
        closeSubscriptionDetailModal() {
            this.subscriptionDetailModalVisible = false;
            this.subscriptionDetailRecord = null;
            this.copyFeedback = null;
        },
        async copyDetailValue(text, key) {
            if (!text) return;
            try {
                await navigator.clipboard.writeText(text);
                this.copyFeedback = key;
                setTimeout(() => { this.copyFeedback = null; }, 1800);
            } catch (_) {}
        },
        async copyAllBillingDetails() {
            const r = this.billingDetailRecord;
            if (!r) return;
            const lines = [];
            lines.push(`Billing #${r.id}`);
            lines.push(`Status: ${r.is_successful ? 'Successful' : 'Unsuccessful'}`);
            lines.push(`Date: ${r.created_at ? moment(r.created_at).format('DD MMM YYYY HH:mm') : '—'}`);
            const amount = this.formatBillingAmount(r.amount);
            if (amount !== '—') lines.push(`Amount: ${amount}`);
            if (r.description) lines.push(`Description: ${r.description}`);
            if (!r.is_successful && r.failure_type) lines.push(`Failure type: ${r.failure_type}`);
            if (!r.is_successful && r.failure_reason) lines.push(`Failure reason: ${r.failure_reason}`);
            if (r.reference_code) lines.push(`Ref: ${r.reference_code}`);
            const text = lines.join('\n');
            try {
                await navigator.clipboard.writeText(text);
                this.copyFeedback = 'all';
                setTimeout(() => { this.copyFeedback = null; }, 1800);
            } catch (_) {}
        },
        getBillingTransactionTooltipHtml(tx, typeLabel, clickHint = 'Click to view transaction') {
            if (!tx || !tx.id) return '';
            const status = tx.is_successful ? 'Successful' : 'Unsuccessful';
            const statusClass = tx.is_successful ? 'detail-tooltip__badge detail-tooltip__badge--success' : 'detail-tooltip__badge detail-tooltip__badge--warn';
            const date = tx.created_at ? moment(tx.created_at).format('DD MMM YYYY HH:mm') : '—';
            const amount = this.formatBillingAmount(tx.amount);
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
            html += `</div><div class="detail-tooltip__click-hint">${clickHint}</div>`;
            return html;
        },
        formatAmount(tx) {
            if (!tx) return '—';
            return formatMoney(tx.amount ?? tx);
        },
        showModal(action) {
            this.modalAction = action;
            this.isShowingModal = true;
        },
        loadTabIfNeeded(id) {
            const key = id;
            const routeNames = {
                subscriptions: 'subscriber.subscriptions',
                messages: 'subscriber.messages',
                billing: 'subscriber.billing.transactions',
            };
            const routeName = routeNames[key];
            if (!routeName || this.tabData[key].data?.length > 0) return;
            this.fetchTab(key, 1);
        },
        async fetchTab(key, page = 1) {
            const routeNames = {
                subscriptions: 'subscriber.subscriptions',
                messages: 'subscriber.messages',
                billing: 'subscriber.billing.transactions',
            };
            const routeName = routeNames[key];
            if (!routeName) return;
            this.loadingTabs[key] = true;
            try {
                const url = route(routeName, { project: this.project?.id, subscriber: this.subscriber?.id });
                const { data } = await window.axios.get(url, { params: { page, per_page: 10 } });
                this.tabData[key] = data.data ?? emptyPaginated();
            } catch (_) {
                this.tabData[key] = emptyPaginated();
            } finally {
                this.loadingTabs[key] = false;
            }
        },
        goToSubscription(subscriptionId) {
            if (!this.project?.id || !subscriptionId) return;
            router.visit(route('show.subscription', { project: this.project.id, subscription: subscriptionId }));
        },
        goToBillingTransaction(billingTransactionId) {
            if (!this.project?.id || !billingTransactionId) return;
            router.visit(route('show.transaction', { project: this.project.id, billing_transaction: billingTransactionId }));
        },
        goToSubscriberMessage(subscriberMessageId) {
            if (!this.project?.id || !subscriberMessageId) return;
            router.visit(route('show.subscriber.message', { project: this.project.id, subscriber_message: subscriberMessageId }));
        },
        onDeleted() {
            router.visit(route('show.subscribers', { project: this.project?.id }));
        },
        onUpdated() {
            router.reload();
        },
        onCreated() {},
        handleDeleteSubscriber(subscriberId) {
            const project = this.project?.id;
            if (!project || !subscriberId) return;
            const url = route('delete.subscriber', { project, subscriber: subscriberId });
            this.deletingSubscriber = true;
            window.axios
                .delete(url)
                .then(() => {
                    this.isShowingModal = false;
                    this.onDeleted();
                })
                .catch(() => {})
                .finally(() => (this.deletingSubscriber = false));
        },
    },
});
</script>

<!-- Unscoped: detail tooltips (subscription & billing) are portaled -->
<style>
.subscriber-detail-tooltip {
    max-width: 420px;
    min-width: 280px;
    padding: 0;
    margin-left: -32px;
    border-radius: 12px;
    background: #fff !important;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 10px 25px -5px rgba(0, 0, 0, 0.15);
}
.subscriber-detail-tooltip .p-tooltip-arrow {
    display: none;
}
.subscriber-detail-tooltip .p-tooltip-text {
    padding: 14px 16px;
    overflow-x: auto;
    overflow-y: auto;
    max-height: 70vh;
    background: transparent !important;
    border: none !important;
    border-radius: 0 !important;
    box-shadow: none !important;
    text-align: left;
}
.subscriber-detail-tooltip .detail-tooltip__title {
    font-size: 0.75rem;
    font-weight: 700;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    color: #1e293b;
    padding-bottom: 10px;
    margin-bottom: 10px;
    border-bottom: 1px solid #e2e8f0;
}
.subscriber-detail-tooltip .detail-tooltip__body {
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.subscriber-detail-tooltip .detail-tooltip__row {
    display: flex;
    align-items: baseline;
    justify-content: space-between;
    gap: 12px;
    font-size: 0.6875rem;
}
.subscriber-detail-tooltip .detail-tooltip__row--full {
    flex-direction: column;
    align-items: stretch;
    gap: 4px;
}
.subscriber-detail-tooltip .detail-tooltip__label {
    flex-shrink: 0;
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.subscriber-detail-tooltip .detail-tooltip__value {
    color: #334155;
    font-weight: 500;
    word-break: break-word;
    text-align: right;
}
.subscriber-detail-tooltip .detail-tooltip__value--wrap {
    text-align: left;
    line-height: 1.5;
    margin-top: 1px;
}
.subscriber-detail-tooltip .detail-tooltip__value--mono {
    font-family: ui-monospace, monospace;
    font-size: 0.65rem;
    word-break: keep-all;
    white-space: nowrap;
    color: #475569;
    min-width: 0;
}
.subscriber-detail-tooltip .detail-tooltip__badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 6px;
    font-size: 0.625rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
.subscriber-detail-tooltip .detail-tooltip__badge--success {
    background: #d1fae5;
    color: #065f46;
}
.subscriber-detail-tooltip .detail-tooltip__badge--warn {
    background: #fef3c7;
    color: #92400e;
}
.subscriber-detail-tooltip .detail-tooltip__meta {
    margin-top: 8px;
    padding-top: 8px;
    border-top: 1px solid #e2e8f0;
    font-size: 0.625rem;
    font-style: italic;
    color: #64748b;
}
.subscriber-detail-tooltip .detail-tooltip__click-hint {
    margin-top: 10px;
    padding-top: 8px;
    border-top: 1px solid #e2e8f0;
    font-size: 0.65rem;
    font-weight: 600;
    color: #6366f1;
    display: flex;
    align-items: center;
    gap: 6px;
}
.subscriber-detail-tooltip .detail-tooltip__click-hint::before {
    content: '';
    display: inline-block;
    width: 14px;
    height: 14px;
    flex-shrink: 0;
    background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236366f1' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M14 4.1 12 6'/%3E%3Cpath d='m5.1 8-2.9-.8'/%3E%3Cpath d='m6 12-1.9 2'/%3E%3Cpath d='M7.2 2.2 8 5.1'/%3E%3Cpath d='M9.037 9.69a.5.5 0 0 1 .653-.653l11 4.5a.5.5 0 0 1-.074.949l-4.35 1.04a1 1 0 0 0-.74.74l-1.04 4.35a.5.5 0 0 1-.95.074z'/%3E%3C/svg%3E") center/contain no-repeat;
}
/* Inactive status badge: amber */
.p-tag.p-tag-warn, .p-tag-warn, .tag-amber, .p-tag.tag-amber {
    background: #fef3c7 !important;
    color: #92400e !important;
    border-color: #fcd34d !important;
}
.p-dialog-close-button, .p-dialog-header-actions .p-button,
.p-dialog-close-button:focus, .p-dialog-close-button:focus-visible {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
}
.trial-badge.p-tag {
    background: #f5f3ff !important;
    color: #5b21b6 !important;
    border: 1px solid #ddd6fe;
}
.detail-modal-copy-btn,
.detail-modal-copy-btn .p-icon,
.detail-modal-copy-btn svg {
    color: #475569 !important;
    background: transparent !important;
    border-color: #e2e8f0 !important;
}
.detail-modal-copy-btn:hover,
.detail-modal-copy-btn:focus {
    color: #1e293b !important;
    background: #f1f5f9 !important;
    border-color: #cbd5e1 !important;
}
</style>
