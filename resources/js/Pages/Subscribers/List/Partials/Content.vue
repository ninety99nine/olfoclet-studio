<template>
    <div class="min-h-screen bg-slate-50/50 p-4 lg:p-8 font-sans antialiased text-slate-700">
        <ManageSubscriberModal
            v-model="isShowingModal"
            :action="modalAction"
            :subscriber="subscriber"
            :project-permissions="projectPermissions"
            :show-addbutton="false"
            :deleting-subscriber="deletingSubscriber"
            @onDeleted="onDeleted"
            @onUpdated="onUpdated"
            @onCreated="onCreated"
            @request-delete="handleDeleteSubscriber"
        />

        <div class="max-w-[1600px] mx-auto mb-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <h1 class="text-2xl font-black tracking-tight text-indigo-950">Subscribers</h1>

                <div class="flex items-center gap-6">
                    <div class="text-right">
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 block mb-0.5">Total</span>
                        <span class="text-xl font-bold text-indigo-900 tabular-nums leading-none">
                            {{ (subscribersPayload.total ?? 0).toLocaleString() }}
                        </span>
                    </div>

                    <button
                        v-if="projectPermissions.includes('Manage subscribers')"
                        @click="showModal(null, 'create')"
                        class="h-10 px-5 flex items-center gap-2 rounded-xl bg-indigo-600 text-white font-bold hover:bg-indigo-700 transition-all shadow-md shadow-indigo-100 active:scale-95"
                    >
                        <Plus :size="14" class="text-xs" />
                        <span class="text-xs">Add Subscriber</span>
                    </button>
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

                    <button @click="showDateModal" class="h-11 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-600 text-[10px] font-black flex items-center justify-center gap-2 transition-all uppercase tracking-widest">
                        <Calendar :size="16" class="text-indigo-500" />
                        <span class="hidden lg:inline">{{ selectedDateLabel }}</span>
                    </button>

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

            <!-- Active date, filter & sort tags (same row as Sessions) -->
            <div v-if="selectedDateOption?.value !== 'all' || selectedFilterTags.length > 0 || selectedSortOptions.length > 0" class="flex flex-wrap items-center gap-2">
                <span
                    v-if="selectedDateOption?.value !== 'all'"
                    class="inline-flex items-center px-3 py-1.5 bg-indigo-50 text-indigo-700 text-xs font-semibold rounded-full"
                >
                    <template v-if="selectedDateOption?.value === 'custom' && (filters.date_from || filters.date_to)">
                        {{ moment(filters.date_from).format('MMM D') }} – {{ moment(filters.date_to).format('MMM D') }}
                    </template>
                    <template v-else>{{ selectedDateOption?.label }}</template>
                    <button type="button" aria-label="Remove date" @click="selectedDateOption = { value: 'all', label: 'All Time' }; filters.date_from = ''; filters.date_to = ''; fetchSubscribers(1)" class="ml-2 text-indigo-500 hover:text-indigo-700 font-bold leading-none">×</button>
                </span>
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
                    <div v-if="loading" key="loading" class="py-24 px-8 flex flex-col items-center justify-center text-center">
                        <span class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-slate-100 border border-slate-200 mb-6">
                            <RefreshCw :size="24" class="text-indigo-500 animate-spin-smooth" />
                        </span>
                        <p class="text-sm font-medium text-slate-500">Loading subscribers...</p>
                    </div>
                    <div v-else-if="subscriberList.length > 0" key="table-wrapper" class="overflow-x-auto">
                        <table class="w-full min-w-[1100px] border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50 border-b border-slate-100">
                                <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Subscriber</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Activity</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Subscription</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Billing</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left whitespace-nowrap">Total spend</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left whitespace-nowrap">Next billing</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left whitespace-nowrap">Next SMS</th>
                                <th class="px-6 py-5 text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] text-left">Joined</th>
                                <th class="px-8 py-5"></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr
                                    v-for="(row, index) in subscriberList"
                                    :key="row.id ?? index"
                                class="group hover:bg-indigo-50/20 transition-colors cursor-pointer"
                                @click="goToSubscriber(row.id)"
                            >
                                <td class="px-8 py-4" @click.stop="goToSubscriber(row.id)">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center text-slate-400 group-hover:text-indigo-600 group-hover:border-indigo-100 transition-all">
                                            <User :size="14" class="text-xs" />
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-indigo-950">{{ row.msisdn ?? '—' }}</div>
                                            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest">{{ row.id != null ? '#' + row.id : '—' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-1.5">
                                        <div class="px-2 py-1 bg-slate-50 rounded-lg text-center min-w-[40px]">
                                            <p class="text-[10px] font-bold text-indigo-900 leading-none">{{ row.subscriptions_count ?? 0 }}</p>
                                            <p class="text-[8px] font-black text-slate-400 uppercase mt-0.5">SUB</p>
                                        </div>
                                        <div class="px-2 py-1 bg-slate-50 rounded-lg text-center min-w-[40px]">
                                            <p class="text-[10px] font-bold text-indigo-900 leading-none">{{ row.messages_count ?? 0 }}</p>
                                            <p class="text-[8px] font-black text-slate-400 uppercase mt-0.5">MSG</p>
                                        </div>
                                    </div>
                                </td>
<td class="px-6 py-4">
                                <span
                                    v-if="row.latest_subscription?.cancelled_at"
                                    v-tooltip="{
                                        value: getSubscriptionTooltipHtml(row.latest_subscription),
                                        escape: false,
                                        class: 'subscriber-detail-tooltip'
                                    }"
                                    class="inline-flex items-center px-2.5 py-1 rounded-md bg-amber-100 text-amber-800 text-[10px] font-bold cursor-pointer w-fit whitespace-nowrap"
                                    @click.stop="openDetailModal(row, 'subscription')"
                                >
                                    Cancelled {{ moment(row.latest_subscription.cancelled_at).format('DD MMM YY') }}
                                </span>
                                <span
                                    v-else-if="row.latest_subscription"
                                    v-tooltip="{
                                        value: getSubscriptionTooltipHtml(row.latest_subscription),
                                        escape: false,
                                        class: 'subscriber-detail-tooltip'
                                    }"
                                    class="inline-block cursor-pointer w-fit"
                                    @click.stop="openDetailModal(row, 'subscription')"
                                >
                                    <SubscriptionStatusBadge
                                        :is-active="getLatestSubscriptionActiveStatus(row)"
                                        class="scale-90 origin-left"
                                    />
                                </span>
                                <span v-else class="text-[9px] font-bold text-slate-300 uppercase">None</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-black text-slate-500 uppercase w-8">User</span>
                                            <span
                                                v-if="row.latest_user_billing_transaction"
                                                v-tooltip="{
                                                    value: getBillingTransactionTooltipHtml(row.latest_user_billing_transaction, 'USER BILLING'),
                                                    escape: false,
                                                    class: 'billing-detail-tooltip',
                                                    position: 'bottom'
                                                }"
                                                class="inline-block cursor-pointer"
                                                @click.stop="openDetailModal(row, 'user_billing')"
                                            >
                                                <BillingStatusBadge :billing-transaction="getLatestUserBillingTransaction(row)" class="scale-75 origin-left" />
                                            </span>
                                            <span v-else class="text-[10px] text-slate-200">—</span>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-black text-slate-500 uppercase w-8">Auto</span>
                                            <span
                                                v-if="row.latest_auto_billing_transaction"
                                                v-tooltip="{
                                                    value: getBillingTransactionTooltipHtml(row.latest_auto_billing_transaction, 'AUTO BILLING'),
                                                    escape: false,
                                                    class: 'billing-detail-tooltip',
                                                    position: 'bottom'
                                                }"
                                                class="inline-block cursor-pointer"
                                                @click.stop="openDetailModal(row, 'auto_billing')"
                                            >
                                                <BillingStatusBadge :billing-transaction="getLatestAutoBillingTransaction(row)" class="scale-75 origin-left" />
                                            </span>
                                            <span v-else class="text-[10px] text-slate-200">—</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm font-semibold text-slate-800 tabular-nums">{{ formatMoney(row.total_spend_amount ?? 0) }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <template v-if="row.next_auto_billing">
                                        <p class="text-xs font-semibold text-indigo-800">{{ formatScheduleDate(row.next_auto_billing.at) }}</p>
                                        <p v-if="row.next_auto_billing.pricing_plan_name" class="text-[10px] text-indigo-600 mt-0.5">{{ row.next_auto_billing.pricing_plan_name }}</p>
                                    </template>
                                    <template v-else-if="row.auto_billing_cancelled">
                                        <span class="text-[10px] text-amber-600 font-medium whitespace-nowrap" title="Subscriber turned off auto billing">Cancelled</span>
                                    </template>
                                    <span v-else class="text-[10px] text-slate-300">—</span>
                                </td>
                                <td class="px-6 py-4">
                                    <template v-if="row.next_sms">
                                        <p class="text-xs font-semibold text-violet-800">{{ formatScheduleDate(row.next_sms.at) }}</p>
                                        <p v-if="row.next_sms.sms_campaign_name" class="text-[10px] text-violet-600 mt-0.5">{{ row.next_sms.sms_campaign_name }}</p>
                                    </template>
                                    <span v-else class="text-[10px] text-slate-300">—</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <p class="text-xs font-bold text-slate-600">{{ row.created_at ? moment(row.created_at).format('DD MMM YY') : '—' }}</p>
                                    <p class="text-[9px] font-medium text-slate-400">{{ row.created_at ? moment(row.created_at).format('hh:mm A') : '' }}</p>
                                </td>
                                <td class="px-8 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-all">
                                        <button @click.stop="goToSubscriber(row.id)" class="h-8 px-3 flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-indigo-500 hover:border-indigo-200 transition-all text-xs font-bold">
                                            View
                                        </button>
                                        <button v-if="projectPermissions.includes('Manage subscribers')" @click.stop="showModal(row, 'delete')" class="h-8 w-8 flex items-center justify-center rounded-lg bg-white border border-slate-200 text-slate-400 hover:text-rose-500 hover:border-rose-200 transition-all" title="Delete subscriber">
                                            <Trash2 :size="12" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                        </table>
                    </div>

                    <div v-else key="empty" class="py-24 px-8 flex flex-col items-center justify-center text-center">
                        <div class="h-20 w-20 rounded-3xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-500 mb-6">
                            <Users :size="40" class="text-slate-500" />
                        </div>
                        <h3 class="text-lg font-bold text-indigo-950 mb-1">No subscribers found</h3>
                        <p class="text-sm text-slate-400 max-w-xs">We couldn't find any records matching your current criteria.</p>
                        <button v-if="hasActiveFilters" @click="clearAll" class="mt-6 text-xs font-black text-indigo-600 uppercase tracking-widest hover:text-indigo-700 underline underline-offset-4">
                            Clear all filters
                        </button>
                    </div>
                </Transition>
                <Pagination
                    :pagination-payload="subscribersPayload"
                    :api-mode="true"
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

        <Dialog v-model:visible="filterModalVisible" header="Filters" :modal="true" :dismissableMask="true" :draggable="false" :style="{ width: '360px' }"
            :pt="{
                root: { class: 'rounded-3xl border-none shadow-2xl overflow-hidden' },
                header: { class: 'bg-white px-6 pt-5 pb-0 border-none text-indigo-900 font-semibold text-sm' },
                pcCloseButton: { root: { class: 'h-8 w-8 bg-slate-50 text-slate-400 hover:text-rose-500 transition-all !border-0 !border-none shadow-none' } }
            }">
            <div class="pt-3 pb-6">
                <p class="text-slate-500 text-xs mb-4">Narrow the list by status or scheduling.</p>
                <div class="space-y-3">
                    <div>
                        <label class="text-xs text-slate-500 block mb-1">Subscription</label>
                        <Select v-model="tempFilters.status" :options="subscriptionStatusOptions" option-label="label" option-value="value" class="w-full" showClear placeholder="Any" />
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 block mb-1">User billing</label>
                        <Select v-model="tempFilters.billingStatus" :options="userBillingOptions" option-label="label" option-value="value" class="w-full" showClear placeholder="Any" />
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 block mb-1">Auto billing</label>
                        <Select v-model="tempFilters.autoBillingStatus" :options="autoBillingOptions" option-label="label" option-value="value" class="w-full" showClear placeholder="Any" />
                    </div>
                    <div>
                        <label class="text-xs text-slate-500 block mb-1">Total spend</label>
                        <Select v-model="tempFilters.spendStatus" :options="spendStatusOptions" option-label="label" option-value="value" class="w-full" showClear placeholder="Any" />
                    </div>
                </div>
                <div class="mt-4">
                    <button type="button" @click="showMoreFilterOptions = !showMoreFilterOptions"
                        class="flex items-center gap-2 w-full text-left text-slate-500 hover:text-slate-700 text-xs font-medium py-1.5 transition-colors">
                        <ChevronDown :size="14" class="transition-transform" :class="{ 'rotate-180': showMoreFilterOptions }" />
                        {{ showMoreFilterOptions ? 'Fewer options' : 'More options' }}
                    </button>
                    <Transition name="filter-more">
                        <div v-show="showMoreFilterOptions" class="mt-3 pt-3 border-t border-slate-100 space-y-3">
                            <div>
                                <label class="text-xs text-slate-500 block mb-1">Scheduled for billing</label>
                                <Select v-model="tempFilters.scheduledBilling" :options="scheduledBillingOptions" option-label="label" option-value="value" class="w-full" showClear placeholder="Any" />
                            </div>
                            <div>
                                <label class="text-xs text-slate-500 block mb-1">Scheduled for SMS</label>
                                <Select v-model="tempFilters.scheduledSms" :options="scheduledSmsOptions" option-label="label" option-value="value" class="w-full" showClear placeholder="Any" />
                            </div>
                            <div>
                                <label class="text-xs text-slate-500 block mb-1">Cancelled auto billing</label>
                                <Select v-model="tempFilters.cancelledAutoBilling" :options="cancelledAutoBillingOptions" option-label="label" option-value="value" class="w-full" showClear placeholder="Any" />
                            </div>
                        </div>
                    </Transition>
                </div>
                <button @click="hideFilterModal" class="w-full py-3 mt-5 bg-indigo-600 text-white rounded-xl text-sm font-semibold hover:bg-indigo-700 transition-colors">Apply</button>
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

        <!-- Detail modal (subscription or billing) with quick actions -->
        <Dialog
            v-model:visible="detailModalVisible"
            :header="detailModalTitle"
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
            @hide="closeDetailModal"
        >
            <template v-if="detailModalType === 'subscription' && detailModalRecord">
                <div class="space-y-4">
                    <div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-2.5 text-sm">
                        <span class="text-slate-500 font-semibold uppercase text-xs">Plan</span>
                        <span class="text-slate-800">{{ getSubscriptionPlanName(detailModalRecord) }}</span>
                        <span class="text-slate-500 font-semibold uppercase text-xs">Start</span>
                        <span class="text-slate-800">{{ formatSubscriptionDate(detailModalRecord.start_at) }}</span>
                        <span class="text-slate-500 font-semibold uppercase text-xs">End</span>
                        <span class="text-slate-800">{{ formatSubscriptionDate(detailModalRecord.end_at) }}</span>
                        <span class="text-slate-500 font-semibold uppercase text-xs">Status</span>
                        <span>
                            <Tag :value="detailModalRecord.is_active ? 'Active' : 'Inactive'" :severity="detailModalRecord.is_active ? 'success' : 'warn'" :class="['text-xs', { 'tag-amber': !detailModalRecord.is_active }]" />
                        </span>
                        <template v-if="detailModalRecord.cancelled_at">
                            <span class="text-slate-500 font-semibold uppercase text-xs">Cancelled</span>
                            <span class="text-slate-800">{{ formatSubscriptionDate(detailModalRecord.cancelled_at) }}</span>
                        </template>
                        <template v-if="detailModalRecord.created_using_auto_billing">
                            <span class="col-span-2 text-slate-500 italic text-xs">Created via auto billing</span>
                        </template>
                    </div>
                    <div class="border-t border-slate-100 pt-4">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Quick actions</p>
                        <div class="flex flex-wrap gap-2">
                            <Button :label="copyFeedback === 'subscriptionId' ? 'Copied!' : 'Copy subscription ID'" size="small" text outlined class="text-xs detail-modal-copy-btn" @click="copyDetailValue(String(detailModalRecord.id), 'subscriptionId')">
                                <template #icon><Copy :size="12" /></template>
                            </Button>
                            <Button :label="copyFeedback === 'plan' ? 'Copied!' : 'Copy plan name'" size="small" text outlined class="text-xs detail-modal-copy-btn" @click="copyDetailValue(getSubscriptionPlanName(detailModalRecord), 'plan')">
                                <template #icon><Copy :size="12" /></template>
                            </Button>
                            <Button :label="copyFeedback === 'start' ? 'Copied!' : 'Copy start date'" size="small" text outlined class="text-xs detail-modal-copy-btn" @click="copyDetailValue(formatSubscriptionDate(detailModalRecord.start_at), 'start')">
                                <template #icon><Copy :size="12" /></template>
                            </Button>
                            <Button :label="copyFeedback === 'end' ? 'Copied!' : 'Copy end date'" size="small" text outlined class="text-xs detail-modal-copy-btn" @click="copyDetailValue(formatSubscriptionDate(detailModalRecord.end_at), 'end')">
                                <template #icon><Copy :size="12" /></template>
                            </Button>
                        </div>
                    </div>
                </div>
            </template>
            <template v-else-if="(detailModalType === 'user_billing' || detailModalType === 'auto_billing') && detailModalRecord">
                <div class="space-y-4">
                    <div class="grid grid-cols-[auto_1fr] gap-x-4 gap-y-2.5 text-sm">
                        <span class="text-slate-500 font-semibold uppercase text-xs">STATUS</span>
                        <span>
                            <Tag :value="detailModalRecord.is_successful ? 'Successful' : 'Unsuccessful'" :severity="detailModalRecord.is_successful ? 'success' : 'warn'" :class="['text-xs', { 'tag-amber': !detailModalRecord.is_successful }]" />
                        </span>
                        <span class="text-slate-500 font-semibold uppercase text-xs">DATE</span>
                        <span class="text-slate-800">{{ detailModalRecord.created_at ? moment(detailModalRecord.created_at).format('DD MMM YYYY HH:mm') : '—' }}</span>
                        <span class="text-slate-500 font-semibold uppercase text-xs">AMOUNT</span>
                        <span class="text-slate-800">{{ formatBillingAmount(detailModalRecord.amount) }}</span>
                        <span class="text-slate-500 font-semibold uppercase text-xs">DESCRIPTION</span>
                        <span class="text-slate-800 break-words">{{ detailModalRecord.description || '—' }}</span>
                        <template v-if="!detailModalRecord.is_successful">
                            <span class="text-slate-500 font-semibold uppercase text-xs">FAILURE</span>
                            <span class="text-slate-800 break-words">{{ detailModalRecord.failure_reason || '—' }}</span>
                            <span class="text-slate-500 font-semibold uppercase text-xs">FAILURE TYPE</span>
                            <span class="text-slate-800">{{ detailModalRecord.failure_type || '—' }}</span>
                        </template>
                        <span class="text-slate-500 font-semibold uppercase text-xs">REF</span>
                        <span class="text-slate-800 font-mono text-xs break-all">{{ detailModalRecord.reference_code || '—' }}</span>
                    </div>
                    <div class="border-t border-slate-100 pt-4">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">COPY DETAILS</p>
                        <div class="grid grid-cols-2 gap-2">
                            <Button :label="copyFeedback === 'all' ? 'Copied!' : 'Copy all details'" size="small" severity="secondary" text outlined class="text-xs detail-modal-copy-btn" @click="copyAllBillingDetails">
                                <template #icon><Copy :size="12" /></template>
                            </Button>
                            <Button :label="copyFeedback === 'billingId' ? 'Copied!' : 'Copy billing ID'" size="small" severity="secondary" text outlined class="text-xs detail-modal-copy-btn" @click="copyDetailValue(String(detailModalRecord.id), 'billingId')">
                                <template #icon><Copy :size="12" /></template>
                            </Button>
                            <Button :label="copyFeedback === 'ref' ? 'Copied!' : 'Copy reference code'" size="small" severity="secondary" text outlined class="text-xs detail-modal-copy-btn" @click="copyDetailValue(detailModalRecord.reference_code || '', 'ref')">
                                <template #icon><Copy :size="12" /></template>
                            </Button>
                            <Button :label="copyFeedback === 'amount' ? 'Copied!' : 'Copy amount'" size="small" severity="secondary" text outlined class="text-xs detail-modal-copy-btn" @click="copyDetailValue(formatBillingAmount(detailModalRecord.amount), 'amount')">
                                <template #icon><Copy :size="12" /></template>
                            </Button>
                            <Button :label="copyFeedback === 'description' ? 'Copied!' : 'Copy description'" size="small" severity="secondary" text outlined class="text-xs detail-modal-copy-btn" @click="copyDetailValue(detailModalRecord.description || '', 'description')">
                                <template #icon><Copy :size="12" /></template>
                            </Button>
                            <Button :label="copyFeedback === 'date' ? 'Copied!' : 'Copy date'" size="small" severity="secondary" text outlined class="text-xs detail-modal-copy-btn" @click="copyDetailValue(detailModalRecord.created_at ? moment(detailModalRecord.created_at).format('DD MMM YYYY HH:mm') : '', 'date')">
                                <template #icon><Copy :size="12" /></template>
                            </Button>
                        </div>
                    </div>
                </div>
            </template>
        </Dialog>
    </div>
</template>

<script>
import { defineComponent } from 'vue';
import Dialog from 'primevue/dialog';
import Select from 'primevue/select';
import DatePicker from 'primevue/datepicker';
import Button from 'primevue/button';
import Tag from 'primevue/tag';
import Tooltip from 'primevue/tooltip';
import debounce from 'lodash/debounce';
import moment from 'moment';
import { Plus, Search, Calendar, Filter, RefreshCw, X, ChevronLeft, ChevronRight, ChevronDown, User, Trash2, Users, Copy, ArrowDownWideNarrow, Check } from 'lucide-vue-next';

import { router, usePage } from '@inertiajs/vue3';
import { useToast } from 'primevue/usetoast';
import { formatMoney as formatMoneyUtil } from '@/utils/formatMoney';
import ManageSubscriberModal from './ManageSubscriberModal.vue';
import Pagination from '@/Partials/Pagination.vue';
import SubscriptionStatusBadge from './SubscriptionStatusBadge.vue';
import BillingStatusBadge from './BillingStatusBadge.vue';

export default defineComponent({
    setup() {
        const toast = useToast();
        const page = usePage();
        return { toast, page };
    },
    components: {
        Dialog, Select, DatePicker, Button, Tag, ManageSubscriberModal, Pagination, SubscriptionStatusBadge, BillingStatusBadge,
        Plus, Search, Calendar, Filter, RefreshCw, X, ChevronLeft, ChevronRight, ChevronDown, User, Trash2, Users, Copy, ArrowDownWideNarrow, Check,
    },
    directives: { Tooltip },
    props: {
        projectPermissions: { type: Array, default: () => [] },
        refetchTrigger: { type: Number, default: 0 },
    },
    data() {
        return {
            moment,
            loading: false,
            isShowingModal: false,
            modalAction: null,
            subscriber: null,
            deletingSubscriber: false,
            subscribersPayload: { data: [], current_page: 1, last_page: 1, total: 0, links: [] },
            searchQuery: '',
            filters: { status: '', billingStatus: '', autoBillingStatus: '', spendStatus: '', scheduledBilling: '', scheduledSms: '', cancelledAutoBilling: '', date_from: '', date_to: '' },
            tempFilters: { status: '', billingStatus: '', autoBillingStatus: '', spendStatus: '', scheduledBilling: '', scheduledSms: '', cancelledAutoBilling: '' },
            dateModalVisible: false,
            filterModalVisible: false,
            sortModalVisible: false,
            selectedDateOption: { value: 'all', label: 'All Time' },
            selectedSortOptions: [],
            sortOptions: [
                { label: 'Newest first', value: 'created_at:desc', group: 'date' },
                { label: 'Oldest first', value: 'created_at:asc', group: 'date' },
                { label: 'Most subscriptions', value: 'subscriptions_count:desc', group: 'subscriptions' },
                { label: 'Fewest subscriptions', value: 'subscriptions_count:asc', group: 'subscriptions' },
                { label: 'Most messages', value: 'messages_count:desc', group: 'messages' },
                { label: 'Fewest messages', value: 'messages_count:asc', group: 'messages' },
                { label: 'Most spend', value: 'total_spend_amount:desc', group: 'spend' },
                { label: 'Least spend', value: 'total_spend_amount:asc', group: 'spend' },
                { label: 'ID descending', value: 'id:desc', group: 'id' },
                { label: 'ID ascending', value: 'id:asc', group: 'id' },
            ],
            customDateFrom: null,
            customDateTo: null,
            subscriptionStatusOptions: [{ label: 'ALL', value: '' }, { label: 'ACTIVE', value: 'active' }, { label: 'INACTIVE', value: 'inactive' }],
            userBillingOptions: [{ label: 'ANY', value: '' }, { label: 'SUCCESSFUL', value: 'successful' }, { label: 'UNSUCCESSFUL', value: 'unsuccessful' }],
            autoBillingOptions: [{ label: 'ANY', value: '' }, { label: 'SUCCESSFUL', value: 'successful' }, { label: 'UNSUCCESSFUL', value: 'unsuccessful' }],
            spendStatusOptions: [{ label: 'Any', value: '' }, { label: 'Has spent', value: 'has_spent' }, { label: 'Has not spent', value: 'has_not_spent' }],
            scheduledBillingOptions: [
                { label: 'Any', value: '' },
                { label: 'Scheduled (later)', value: 'scheduled' },
                { label: 'Past due', value: 'past_due' },
            ],
            scheduledSmsOptions: [
                { label: 'Any', value: '' },
                { label: 'Scheduled (later)', value: 'scheduled' },
                { label: 'Past due', value: 'past_due' },
            ],
            scheduleFilterOptions: [{ label: 'Any', value: '' }, { label: 'Yes', value: 'yes' }],
            cancelledAutoBillingOptions: [
                { label: 'Any', value: '' },
                { label: 'Yes', value: 'yes' },
                { label: 'No', value: 'no' },
            ],
            dateFilterOptions: [{ value: 'all', label: 'All Time' }, { value: 'today', label: 'Today' }, { value: 'this_week', label: 'Week' }, { value: 'this_month', label: 'Month' }, { value: 'this_year', label: 'Year' }, { value: 'custom', label: 'Custom' }],
            detailModalVisible: false,
            detailModalType: null,
            detailModalRow: null,
            copyFeedback: null,
            showMoreFilterOptions: false,
            initialLoadComplete: false,
        };
    },
    watch: {
        loading(val) {
            if (!val && this.subscriberList.length > 0) this.initialLoadComplete = true;
        },
        subscriberList(list) {
            if (!this.loading && list && list.length > 0) this.initialLoadComplete = true;
        },
        refetchTrigger() {
            this.fetchSubscribers(this.subscribersPayload.current_page || 1);
        },
    },
    computed: {
        subscriberList() {
            return (this.subscribersPayload?.data && Array.isArray(this.subscribersPayload.data))
                ? this.subscribersPayload.data
                : [];
        },
        detailModalTitle() {
            if (!this.detailModalType || !this.detailModalRow) return '';
            if (this.detailModalType === 'subscription' && this.detailModalRow.latest_subscription) {
                return `Subscription #${this.detailModalRow.latest_subscription.id}`;
            }
            if (this.detailModalType === 'user_billing' && this.detailModalRow.latest_user_billing_transaction) {
                return `User billing #${this.detailModalRow.latest_user_billing_transaction.id}`;
            }
            if (this.detailModalType === 'auto_billing' && this.detailModalRow.latest_auto_billing_transaction) {
                return `Auto billing #${this.detailModalRow.latest_auto_billing_transaction.id}`;
            }
            return '';
        },
        detailModalRecord() {
            if (!this.detailModalType || !this.detailModalRow) return null;
            if (this.detailModalType === 'subscription') return this.detailModalRow.latest_subscription ?? null;
            if (this.detailModalType === 'user_billing') return this.detailModalRow.latest_user_billing_transaction ?? null;
            if (this.detailModalType === 'auto_billing') return this.detailModalRow.latest_auto_billing_transaction ?? null;
            return null;
        },
        // RESET CONDITION
        hasActiveFilters() {
            const hasTextSearch = this.searchQuery.trim().length > 0;
            const hasDateFilter = this.selectedDateOption?.value !== 'all';
            const hasLogicFilters = [
                this.filters.status, this.filters.billingStatus, this.filters.autoBillingStatus, this.filters.spendStatus,
                this.filters.scheduledBilling, this.filters.scheduledSms, this.filters.cancelledAutoBilling
            ].some(val => val !== '');
            const hasSort = this.selectedSortOptions.length > 0;

            return hasTextSearch || hasDateFilter || hasLogicFilters || hasSort;
        },
        selectedDateLabel() {
            if (this.selectedDateOption?.value === 'custom' && (this.filters.date_from || this.filters.date_to)) {
                return `${moment(this.filters.date_from).format('MMM D')} - ${moment(this.filters.date_to).format('MMM D')}`;
            }
            return this.selectedDateOption?.label ?? 'All Time';
        },
        selectedFilterTags() {
            const tags = [];
            if (this.filters.status) tags.push({ key: 'status', label: this.filters.status === 'active' ? 'Subscription: Active' : 'Subscription: Inactive' });
            if (this.filters.billingStatus) tags.push({ key: 'billingStatus', label: this.filters.billingStatus === 'successful' ? 'User billing: Successful' : 'User billing: Unsuccessful' });
            if (this.filters.autoBillingStatus) tags.push({ key: 'autoBillingStatus', label: this.filters.autoBillingStatus === 'successful' ? 'Auto billing: Successful' : 'Auto billing: Unsuccessful' });
            if (this.filters.scheduledBilling) {
                const sub = this.filters.scheduledBilling === 'scheduled' ? 'Scheduled (later)' : 'Past due';
                tags.push({ key: 'scheduledBilling', label: `Scheduled for billing: ${sub}` });
            }
            if (this.filters.scheduledSms) {
                const sub = this.filters.scheduledSms === 'scheduled' ? 'Scheduled (later)' : 'Past due';
                tags.push({ key: 'scheduledSms', label: `Scheduled for SMS: ${sub}` });
            }
            if (this.filters.cancelledAutoBilling) {
                const sub = this.filters.cancelledAutoBilling === 'yes' ? 'Yes' : 'No';
                tags.push({ key: 'cancelledAutoBilling', label: `Cancelled auto billing: ${sub}` });
            }
            return tags;
        },
        filteredPagination() {
            const current = this.subscribersPayload.current_page;
            const last = this.subscribersPayload.last_page;
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
        }
    },
    methods: {
        fetchSubscribers(page = 1) {
            const minSpinMs = 800;
            const startedAt = Date.now();
            this.loading = true;
            this.$nextTick(() => {
                const url = route('show.subscribers', { project: route().params.project });
                const sortParam = this.selectedSortOptions.length > 0 ? this.selectedSortOptions[0].value : undefined;
                const params = {
                    page,
                    per_page: 15,
                    msisdn: this.searchQuery || undefined,
                    ...this.filters,
                    ...(sortParam ? { sort: sortParam } : {}),
                };
                const stopSpinner = () => {
                    const elapsed = Date.now() - startedAt;
                    const delay = Math.max(0, minSpinMs - elapsed);
                    if (delay > 0) setTimeout(() => { this.loading = false; }, delay);
                    else this.loading = false;
                };
                window.axios.get(url, { params }).then(({ data }) => {
                const raw = data?.subscribersPayload;
                this.subscribersPayload = raw && typeof raw === 'object'
                    ? {
                        data: Array.isArray(raw.data) ? raw.data : [],
                        current_page: raw.current_page ?? 1,
                        last_page: raw.last_page ?? 1,
                        total: raw.total ?? 0,
                        links: Array.isArray(raw.links) ? raw.links : [],
                    }
                    : { data: [], current_page: 1, last_page: 1, total: 0, links: [] };
            }).catch(() => {
                this.subscribersPayload = { data: [], current_page: 1, last_page: 1, total: 0, links: [] };
            }).finally(stopSpinner);
            });
        },
        refresh() { this.fetchSubscribers(this.subscribersPayload.current_page || 1); },
        applyFilters() { this.fetchSubscribers(1); },
        clearAll() {
            this.selectedDateOption = { value: 'all', label: 'All Time' };
            this.filters = { status: '', billingStatus: '', autoBillingStatus: '', spendStatus: '', scheduledBilling: '', scheduledSms: '', cancelledAutoBilling: '', date_from: '', date_to: '' };
            this.selectedSortOptions = [];
            this.searchQuery = '';
            this.fetchSubscribers(1);
        },
        changePage(page) { this.fetchSubscribers(page); },
        showDateModal() { this.dateModalVisible = true; },
        showFilterModal() {
            this.tempFilters = { ...this.filters };
            this.filterModalVisible = true;
            const hasSchedulingFilter = this.filters.spendStatus || this.filters.scheduledBilling || this.filters.scheduledSms || this.filters.cancelledAutoBilling;
            this.showMoreFilterOptions = !!hasSchedulingFilter;
        },
        hideFilterModal() { this.filters = { ...this.filters, ...this.tempFilters }; this.filterModalVisible = false; this.applyFilters(); },
        showSortModal() { this.sortModalVisible = true; },
        hideSortModal() { this.sortModalVisible = false; },
        isSortActive(option) { return this.selectedSortOptions.some(s => s.label === option.label); },
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
            this.fetchSubscribers(1);
        },
        removeSort(sort) {
            this.selectedSortOptions = this.selectedSortOptions.filter(s => s.label !== sort.label);
            this.fetchSubscribers(1);
        },
        clearSorts() {
            this.selectedSortOptions = [];
            this.hideSortModal();
            this.fetchSubscribers(1);
        },
        removeFilterTag(tag) {
            this.filters[tag.key] = '';
            if (this.tempFilters.hasOwnProperty(tag.key)) this.tempFilters[tag.key] = '';
            this.fetchSubscribers(1);
        },
        applyDateOption(opt) {
            this.selectedDateOption = opt;
            if (opt.value === 'all') { this.filters.date_from = this.filters.date_to = ''; this.dateModalVisible = false; this.applyFilters(); }
            else if (opt.value !== 'custom') {
                const range = this.getDateRangeForPreset(opt.value);
                this.filters.date_from = range.start; this.filters.date_to = range.end;
                this.dateModalVisible = false; this.applyFilters();
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
            this.dateModalVisible = false; this.applyFilters();
        },
        goToSubscriber(subscriberId) {
            const projectId = this.page?.props?.project?.id ?? route().params?.project;
            if (!projectId || !subscriberId) return;
            router.visit(route('show.subscriber', { project: projectId, subscriber: subscriberId }));
        },
        showModal(row, action) { this.subscriber = row; this.modalAction = action; this.isShowingModal = true; },
        openDetailModal(row, type) {
            this.detailModalRow = row;
            this.detailModalType = type;
            this.detailModalVisible = true;
            this.copyFeedback = null;
        },
        closeDetailModal() {
            this.detailModalVisible = false;
            this.detailModalType = null;
            this.detailModalRow = null;
            this.copyFeedback = null;
        },
        async copyAllBillingDetails() {
            const r = this.detailModalRecord;
            if (!r || (this.detailModalType !== 'user_billing' && this.detailModalType !== 'auto_billing')) return;
            const lines = [];
            lines.push(`Billing #${r.id}`);
            lines.push(`Status: ${r.is_successful ? 'Successful' : 'Unsuccessful'}`);
            lines.push(`Date: ${r.created_at ? moment(r.created_at).format('DD MMM YYYY HH:mm') : '—'}`);
            const amount = this.formatBillingAmount(r.amount);
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
        async copyDetailValue(text, key) {
            if (text == null) return;
            try {
                await navigator.clipboard.writeText(text);
                this.copyFeedback = key;
                setTimeout(() => { this.copyFeedback = null; }, 1800);
            } catch (_) {}
        },
        onDeleted() { this.refresh(); },
        onUpdated() { this.refresh(); },
        onCreated() { this.fetchSubscribers(1); },
        handleDeleteSubscriber(subscriberId) {
            const projectId = this.page?.props?.project?.id ?? route().params?.project;
            const id = Number(subscriberId);
            if (!projectId || subscriberId == null || subscriberId === '' || !Number.isInteger(id) || id <= 0) {
                this.toast.add({ severity: 'error', summary: 'Error', detail: 'Cannot delete: invalid subscriber.', life: 5000 });
                return;
            }
            const url = `/projects/${projectId}/subscribers/${id}`;
            this.deletingSubscriber = true;
            window.axios
                .delete(url)
                .then(() => {
                    this.isShowingModal = false;
                    this.onDeleted();
                    this.toast.add({ severity: 'success', summary: 'Success', detail: 'Subscriber deleted successfully.', life: 4000 });
                })
                .catch((err) => {
                    this.toast.add({ severity: 'error', summary: 'Error', detail: err.response?.data?.message || 'Subscriber delete failed.', life: 5000 });
                })
                .finally(() => { this.deletingSubscriber = false; });
        },
        getLatestSubscriptionActiveStatus(d) { return d.latest_subscription?.is_active; },
        getSubscriptionPlanName(sub) { return sub?.pricing_plan?.name ?? '—'; },
        formatSubscriptionDate(val) { return val ? moment(val).format('DD MMM YYYY HH:mm') : '—'; },
        formatScheduleDate(isoString) {
            if (!isoString) return '—';
            return moment(isoString).format('DD MMM YY HH:mm');
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
        getLatestUserBillingTransaction(d) { return d.latest_user_billing_transaction ?? {}; },
        getLatestAutoBillingTransaction(d) { return d.latest_auto_billing_transaction ?? {}; },
        formatMoney(value) {
            return formatMoneyUtil(value);
        },
        formatBillingAmount(amount) {
            return formatMoneyUtil(amount);
        },
        getBillingTransactionTooltipHtml(tx, typeLabel) {
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
            html += '</div><div class="detail-tooltip__click-hint">Click for details</div>';
            return html;
        },
    },
    created() {
        this.debouncedSearch = debounce(this.applyFilters, 400);
        this.fetchSubscribers(1);
    },
});
</script>

<style scoped>
/* Smooth transition: no content <-> content (table / empty state) */
.content-switch-leave-active,
.content-switch-enter-active {
    transition: opacity 0.25s ease, transform 0.25s ease;
}
.content-switch-leave-from,
.content-switch-enter-to {
    opacity: 1;
    transform: translateY(0);
}
.content-switch-leave-to {
    opacity: 0;
    transform: translateY(-6px);
}
.content-switch-enter-from {
    opacity: 0;
    transform: translateY(6px);
}

/* Filters modal "More options" expand/collapse */
.filter-more-enter-active,
.filter-more-leave-active {
    transition: opacity 0.2s ease, transform 0.2s ease;
}
.filter-more-enter-from,
.filter-more-leave-to {
    opacity: 0;
    transform: translateY(-4px);
}
.filter-more-enter-to,
.filter-more-leave-from {
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
    backface-visibility: hidden;
    will-change: transform;
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
:deep(.p-datepicker-input) {
    @apply h-11 bg-slate-50 border-slate-100 rounded-xl font-bold text-xs px-4 focus:bg-white transition-all;
}
:deep(.detail-modal-copy-btn) {
    color: #475569 !important;
}
:deep(.detail-modal-copy-btn:hover),
:deep(.detail-modal-copy-btn:focus) {
    color: #1e293b !important;
    background: #f1f5f9 !important;
}
:deep(.detail-modal-copy-btn .p-icon),
:deep(.detail-modal-copy-btn svg) {
    color: inherit;
}
</style>

<!-- Unscoped: Dialog is portaled to body so close button is outside component DOM -->
<style>
.p-dialog-close-button,
.p-dialog-header-actions .p-button,
.p-dialog-close-button:focus,
.p-dialog-close-button:focus-visible,
.p-dialog-header-actions .p-button:focus,
.p-dialog-header-actions .p-button:focus-visible {
    border: none !important;
    outline: none !important;
    box-shadow: none !important;
}
/* Amber styling for Inactive / Unsuccessful tags (severity="warn") */
.p-tag.p-tag-warn,
.p-tag-warn,
.tag-amber,
.p-tag.tag-amber {
    background: #fef3c7 !important;
    color: #92400e !important;
    border-color: #fcd34d !important;
}
/* Detail modal copy buttons: slate instead of green */
.detail-modal-copy-btn,
.detail-modal-copy-btn .p-icon,
.detail-modal-copy-btn svg {
    color: #475569 !important;
}
.detail-modal-copy-btn:hover,
.detail-modal-copy-btn:focus {
    color: #1e293b !important;
    background: #f1f5f9 !important;
}
.detail-modal-copy-btn:hover .p-icon,
.detail-modal-copy-btn:focus .p-icon,
.detail-modal-copy-btn:hover svg,
.detail-modal-copy-btn:focus svg {
    color: inherit !important;
}
</style>

<!-- Unscoped: Detail tooltips (subscription & billing) are portaled -->
<style>
.subscriber-detail-tooltip {
    max-width: 380px;
    min-width: 260px;
    padding: 0;
    border-radius: 12px;
    background: #fff !important;
    border: 1px solid #e2e8f0;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 10px 25px -5px rgba(0, 0, 0, 0.15);
}
.subscriber-detail-tooltip .p-tooltip-arrow {
    display: none;
}
/* Single box: no border/radius/background on inner wrapper so only .subscriber-detail-tooltip shows one edge */
.subscriber-detail-tooltip .p-tooltip-text {
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
    word-break: break-all;
    color: #475569;
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
/* Lucide MousePointerClick icon (matches lucide-vue-next) */
.subscriber-detail-tooltip .detail-tooltip__click-hint::before {
    content: '';
    display: inline-block;
    width: 14px;
    height: 14px;
    flex-shrink: 0;
    background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%236366f1' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpath d='M14 4.1 12 6'/%3E%3Cpath d='m5.1 8-2.9-.8'/%3E%3Cpath d='m6 12-1.9 2'/%3E%3Cpath d='M7.2 2.2 8 5.1'/%3E%3Cpath d='M9.037 9.69a.5.5 0 0 1 .653-.653l11 4.5a.5.5 0 0 1-.074.949l-4.35 1.04a1 1 0 0 0-.74.74l-1.04 4.35a.5.5 0 0 1-.95.074z'/%3E%3C/svg%3E") center/contain no-repeat;
}
</style>
