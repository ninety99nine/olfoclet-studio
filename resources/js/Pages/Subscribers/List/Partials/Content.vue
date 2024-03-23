<template>

    <div>

        <create-subscriber-modal v-model="isShowingModal" :action="modalAction" :subscriber="subscriber" />

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <!-- Table -->
            <div class="flex flex-col">
                <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="py-2 align-middle inline-block min-w-full sm:px-6 lg:px-8">
                    <div class="shadow overflow-hidden border-b border-gray-200 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">

                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-100 border-r border-dotted border-r-teal-300">

                            </th>
                            <th scope="col" colspan="4" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-100">
                                <div class="font-bold text-center text-teal-500">LAST SUBSCRIPTION</div>
                            </th>
                            <th scope="col" colspan="5" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-100 border-r border-dotted border-r-fuchsia-300">

                            </th>
                            <th scope="col" colspan="5" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-100">
                                <div class="font-bold text-center text-fuchsia-500">LAST USER BILLING</div>
                            </th>
                            <th scope="col" colspan="5" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-100 border-r border-dotted border-r-violet-300">

                            </th>
                            <th scope="col" colspan="5" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-100">
                                <div class="font-bold text-center text-violet-500">LAST AUTO BILLING</div>
                            </th>
                            <th scope="col" colspan="5" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-emerald-100 border-r border-dotted border-r-emerald-300">

                            </th>
                            <th scope="col" colspan="2" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-emerald-100">
                                <div class="font-bold text-center text-emerald-500">LAST MESSAGE</div>
                            </th>
                            <th scope="col" colspan="2" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">

                            </th>
                        </tr>
                        <tr>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Mobile</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-100 border-r border-dotted border-r-teal-300">
                                <span>Total Subscriptions</span>
                            </th>

                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-100">
                                <span>Status</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-100">
                                <span>Start At</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-100">
                                <span>End At</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-100">
                                <span>Cancelled</span>
                            </th>

                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-100">
                                <span>Total User Billing</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-100">
                                <span>Total Successful</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-100">
                                <span>% Successful</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-100">
                                <span>Total Unsuccessful</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-100 border-r border-dotted border-r-fuchsia-300">
                                <span>% Unsuccessful</span>
                            </th>

                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-100">
                                <span>Status</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-100">
                                <span>Amount</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-100">
                                <span>Funds Before Deduction</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-100">
                                <span>Funds After Deduction</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-100">
                                <span>Date</span>
                            </th>

                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-100">
                                <span>Total Auto Billing</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-100">
                                <span>Total Successful</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-100">
                                <span>% Successful</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-100">
                                <span>Total Unsuccessful</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-100 border-r border-dotted border-r-violet-300">
                                <span>% Unsuccessful</span>
                            </th>

                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-100">
                                <span>Status</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-100">
                                <span>Amount</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-100">
                                <span>Funds Before Deduction</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-100">
                                <span>Funds After Deduction</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-100">
                                <span>Date</span>
                            </th>

                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-emerald-100">
                                <span>Total Messages</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-emerald-100">
                                <span>Total As Content</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-emerald-100">
                                <span>Total As Payment Confirmation</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-emerald-100">
                                <span>Total As Auto Billing Reminder</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-emerald-100 border-r border-dotted border-r-emerald-300">
                                <span>% Content Received</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-emerald-100">
                                <span>Content</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-emerald-100">
                                <span>Sent Date</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Created</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Actions</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="subscriber in subscribersPayload.data" :key="subscriber.id">
                                <!-- Mobile -->
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ subscriber.msisdn }}</div>
                                </td>

                                <!-- Subscriptions Count -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50 border-r border-dotted border-r-teal-300">
                                    <span v-if="subscriber.subscriptions_count == 0">...</span>
                                    <span v-else>{{ subscriber.subscriptions_count }}</span>
                                </td>

                                <!-- Last Subscription Status -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50">
                                    <span v-if="subscriber.latest_subscription == null">...</span>
                                    <SubscriptionStatusBadge v-else :isActive="getLatestSubscriptionStatus(subscriber) == 'Active'"></SubscriptionStatusBadge>
                                </td>
                                <!-- Last Subscription Start At -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50">
                                    {{ getLatestSubscription(subscriber).start_at == null ? '...' : moment(getLatestSubscription(subscriber).start_at).format('lll') }}
                                </td>
                                <!-- Last Subscription End At -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50">
                                    {{ getLatestSubscription(subscriber).end_at == null ? '...' : moment(getLatestSubscription(subscriber).end_at).format('lll') }}
                                </td>
                                <!-- Last Subscription Cancelled At -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50">
                                    {{ getLatestSubscription(subscriber).cancelled_at == null ? '...' : moment(getLatestSubscription(subscriber).cancelled_at).format('lll') }}
                                </td>

                                <!-- User Billing Transactions Count -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50">
                                    <span v-if="subscriber.user_billing_transactions_count == 0">...</span>
                                    <span v-else>{{ subscriber.user_billing_transactions_count }}</span>
                                </td>
                                <!-- User Billing Successful Count -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50">
                                    <span v-if="subscriber.successful_user_billing_transactions_count == 0">...</span>
                                    <span v-else>{{ subscriber.successful_user_billing_transactions_count }}</span>
                                </td>
                                <!-- User Billing Successful Percentage -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50">
                                    <span v-if="getPercentageOfSuccessfulUserBilling(subscriber) == 0">...</span>
                                    <span v-else class="text-lg text-green-600">{{ getPercentageOfSuccessfulUserBilling(subscriber) }}%</span>
                                </td>
                                <!-- User Billing Failed Count -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50">
                                    <span v-if="subscriber.unsuccessful_user_billing_transactions_count == 0">...</span>
                                    <span v-else>{{ subscriber.unsuccessful_user_billing_transactions_count }}</span>
                                </td>
                                <!-- User Billing Unsuccessful Percentage -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50 border-r border-dotted border-r-fuchsia-300">
                                    <span v-if="getPercentageOfUnsuccessfulUserBilling(subscriber) == 0">...</span>
                                    <span v-else class="text-lg text-yellow-600">{{ getPercentageOfUnsuccessfulUserBilling(subscriber) }}%</span>
                                </td>

                                <!-- Last User Billing Status -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50">
                                    <span v-if="subscriber.latest_user_billing_transaction == null">...</span>
                                    <BillingStatusBadge v-else :billingTransaction="getLatestUserBillingTransaction(subscriber)"></BillingStatusBadge>
                                </td>

                                <!-- Last User Billing Amount -->
                                <td class="px-6 py-3 whitespace-nowrap text-md text-gray-500 text-center bg-fuchsia-50 font-bold">
                                    {{ getLatestUserBillingTransaction(subscriber).amount == null ? '...' : getLatestUserBillingTransaction(subscriber).amount.amount_with_currency }}
                                </td>
                                <!-- Last User Billing Funds Before Deduction -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50">
                                    {{ getLatestUserBillingTransaction(subscriber).funds_before_deduction == null ? '...' : getLatestUserBillingTransaction(subscriber).funds_before_deduction.amount_with_currency }}
                                </td>
                                <!-- Last User Billing Funds After Deduction -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50">
                                    {{ getLatestUserBillingTransaction(subscriber).funds_after_deduction == null ? '...' : getLatestUserBillingTransaction(subscriber).funds_after_deduction.amount_with_currency }}
                                </td>
                                <!-- Last User Billing Date -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50">
                                    {{ getLatestUserBillingTransaction(subscriber).created_at == null ? '...' : moment(getLatestUserBillingTransaction(subscriber).created_at).format('lll') }}
                                </td>

                                <!-- Auto Billing Transactions Count -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-violet-50">
                                    <span v-if="subscriber.auto_billing_transactions_count == 0">...</span>
                                    <span v-else>{{ subscriber.auto_billing_transactions_count }}</span>
                                </td>
                                <!-- Auto Billing Successful Count -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-violet-50">
                                    <span v-if="subscriber.successful_auto_billing_transactions_count == 0">...</span>
                                    <span v-else>{{ subscriber.successful_auto_billing_transactions_count }}</span>
                                </td>
                                <!-- Auto Billing Successful Percentage -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-violet-50">
                                    <span v-if="getPercentageOfSuccessfulAutoBilling(subscriber) == 0">...</span>
                                    <span v-else class="text-lg text-green-600">{{ getPercentageOfSuccessfulAutoBilling(subscriber) }}%</span>
                                </td>
                                <!-- Auto Billing Failed Count -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-violet-50">
                                    <span v-if="subscriber.unsuccessful_auto_billing_transactions_count == 0">...</span>
                                    <span v-else>{{ subscriber.unsuccessful_auto_billing_transactions_count }}</span>
                                </td>
                                <!-- Auto Billing Unsuccessful Percentage -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-violet-50 border-r border-dotted border-r-violet-300">
                                    <span v-if="getPercentageOfUnsuccessfulAutoBilling(subscriber) == 0">...</span>
                                    <span v-else class="text-lg text-green-600">{{ getPercentageOfUnsuccessfulAutoBilling(subscriber) }}%</span>
                                </td>

                                <!-- Last Auto Billing Status -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-violet-50">
                                    <span v-if="subscriber.latest_auto_billing_transaction == null">...</span>
                                    <BillingStatusBadge v-else :billingTransaction="getLatestAutoBillingTransaction(subscriber)"></BillingStatusBadge>
                                </td>
                                <!-- Last Auto Billing Amount -->
                                <td class="px-6 py-3 whitespace-nowrap text-md text-gray-500 text-center bg-violet-50 font-bold">
                                    {{ getLatestAutoBillingTransaction(subscriber).amount == null ? '...' : getLatestAutoBillingTransaction(subscriber).amount.amount_without_currency }}
                                </td>
                                <!-- Last Auto Billing Funds Before Deduction -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-violet-50">
                                    {{ getLatestAutoBillingTransaction(subscriber).funds_before_deduction == null ? '...' : getLatestAutoBillingTransaction(subscriber).funds_before_deduction.amount_without_currency }}
                                </td>
                                <!-- Last Auto Billing Funds After Deduction -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-violet-50">
                                    {{ getLatestAutoBillingTransaction(subscriber).funds_after_deduction == null ? '...' : getLatestAutoBillingTransaction(subscriber).funds_after_deduction.amount_without_currency }}
                                </td>
                                <!-- Last Auto Billing Date -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-violet-50">
                                    {{ getLatestAutoBillingTransaction(subscriber).created_at == null ? '...' : moment(getLatestAutoBillingTransaction(subscriber).created_at).format('lll') }}
                                </td>

                                <!-- Total Messages -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-emerald-100">
                                    <span v-if="subscriber.messages_count == 0">...</span>
                                    <span v-else>{{ subscriber.messages_count }}</span>
                                </td>
                                <!-- Total Messages As Content -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-emerald-100">
                                    <span v-if="subscriber.messages_as_content_count == 0">...</span>
                                    <span v-else>{{ subscriber.messages_as_content_count }}</span>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-emerald-100">
                                    <span v-if="subscriber.messages_as_payment_confirmation_count == 0">...</span>
                                    <span v-else>{{ subscriber.messages_as_payment_confirmation_count }}</span>
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-emerald-100">
                                    <span v-if="subscriber.messages_as_auto_billing_reminder_count == 0">...</span>
                                    <span v-else>{{ subscriber.messages_as_auto_billing_reminder_count }}</span>
                                </td>
                                <!-- Total Messages Percentage -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-emerald-100 border-r border-dotted border-r-emerald-300">
                                    <span v-if="getPercentageOfCoverage(subscriber.messages_count) == 0">...</span>
                                    <span v-else class="text-lg text-green-600">{{ getPercentageOfCoverage(subscriber.messages_count) }}%</span>
                                </td>

                                <!-- Last Message Content -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 bg-emerald-100">
                                    <div v-if="getLatestMessage(subscriber).content == null" class="text-center">...</div>
                                    <div v-else>{{ getLatestMessage(subscriber).content }}</div>
                                </td>

                                <!-- Last Message Date -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 bg-emerald-100">
                                    <div v-if="getLatestMessage(subscriber).created_at == null" class="text-center">...</div>
                                    <div v-else>{{ moment(getLatestMessage(subscriber).created_at).format('lll') }}</div>
                                </td>

                                <!-- Created Date -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ subscriber.created_at == null ? '...' : moment(subscriber.created_at).format('lll') }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <a v-if="$inertia.page.props.projectPermissions.includes('Manage subscribers')" href="#" @click.prevent="showModal(subscriber, 'update')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <a v-if="$inertia.page.props.projectPermissions.includes('Manage subscribers')" href="#" @click.prevent="showModal(subscriber, 'delete')" class="text-red-600 hover:text-red-900">Delete</a>
                                </td>
                            </tr>

                            <tr v-if="subscribersPayload.data.length == 0">
                                <!-- Content -->
                                <td :colspan="8" class="px-6 py-3 whitespace-nowrap">
                                    <div class="text-center text-gray-900 text-sm p-6">No subscribers</div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                    </div>
                </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <pagination class="mt-6" :paginationPayload="subscribersPayload" :updateData="['subscribersPayload']" />

        </div>

    </div>

</template>
<script>

    import SubscriptionStatusBadge from './SubscriptionStatusBadge.vue'
    import CreateSubscriberModal from './ManageSubscriberModal.vue'
    import Pagination from '../../../../Partials/Pagination.vue'
    import BillingStatusBadge from './BillingStatusBadge.vue'
    import { defineComponent } from 'vue'
    import moment from "moment";

    export default defineComponent({
        components: {
            CreateSubscriberModal, Pagination, SubscriptionStatusBadge, BillingStatusBadge
        },
        props: {
            totalMessages: Number,
            subscribersPayload: Object
        },
        data() {
            return {
                refreshContentInterval: null,
                isShowingModal: false,
                modalAction: null,
                subscriber: null,
                moment: moment
            }
        },
        methods: {
            refreshContent()
            {
                this.$inertia.reload();
            },
            getLatestMessage(subscriber){
                return subscriber.latest_message ?? {};
            },
            getLatestSubscription(subscriber){
                return subscriber.latest_subscription ?? {};
            },
            getLatestSubscriptionStatus(subscriber){
                return (subscriber.latest_subscription ?? {}).status;
            },
            getLatestUserBillingTransaction(subscriber){
                return subscriber.latest_user_billing_transaction ?? {};
            },
            getLatestAutoBillingTransaction(subscriber){
                return (subscriber.latest_auto_billing_transaction ?? {});
            },
            getPercentageOfSuccessfulUserBilling(subscriber) {
                if( subscriber.user_billing_transactions_count > 0 && subscriber.successful_user_billing_transactions_count > 0){
                    return Math.round((subscriber.successful_user_billing_transactions_count / subscriber.user_billing_transactions_count) * 100)
                }

                return 0;
            },
            getPercentageOfUnsuccessfulUserBilling(subscriber) {
                if( subscriber.user_billing_transactions_count > 0 && subscriber.unsuccessful_user_billing_transactions_count > 0){
                    return Math.round((subscriber.unsuccessful_user_billing_transactions_count / subscriber.user_billing_transactions_count) * 100)
                }

                return 0;
            },
            getPercentageOfSuccessfulAutoBilling(subscriber) {
                if( subscriber.auto_billing_transactions_count > 0 && subscriber.successful_auto_billing_transactions_count > 0){
                    return Math.round((subscriber.successful_auto_billing_transactions_count / subscriber.auto_billing_transactions_count) * 100)
                }

                return 0;
            },
            getPercentageOfUnsuccessfulAutoBilling(subscriber) {
                if( subscriber.auto_billing_transactions_count > 0 && subscriber.unsuccessful_auto_billing_transactions_count > 0){
                    return Math.round((subscriber.unsuccessful_auto_billing_transactions_count / subscriber.auto_billing_transactions_count) * 100)
                }

                return 0;
            },
            getPercentageOfCoverage(MessagesCount){
                if( this.totalMessages > 0 ){
                    return Math.round((MessagesCount / this.totalMessages) * 100)
                }

                return 0;
            },
            showModal(subscriber, action){
                this.subscriber = subscriber;
                this.modalAction = action;
                this.isShowingModal = true
            },
            cleanUp()
            {
                clearInterval( this.refreshContentInterval );
                this.refreshContentInterval = null;
            }
        },
        created() {

            //  Keep refreshing this page content every 5 seconds
            this.refreshContentInterval = setInterval(function() {
                this.refreshContent();
            }.bind(this), 5000);
        },
        unmounted() {
            this.cleanUp()
        }
    })
</script>
