<template>

    <div>

        <div class="grid grid-cols-12 gap-8">

            <div class="col-span-6">
                <div class="bg-gray-50 border-b px-6 py-4 rounded-t text-gray-500 text-sm mb-4">
                    <div class="text-2xl font-semibold leading-6 text-gray-500">Billing Transactions</div>
                </div>
            </div>

            <div class="col-span-6">

                <!-- Search -->
                <div class="mb-4">
                    <jet-input id="search" type="text" class="w-full mt-1 block" v-model="form.search" placeholder = "26772000001" />
                    <jet-input-error :message="form.errors.search" class="mt-2" />
                </div>

            </div>

        </div>

        <div class="bg-white shadow-xl sm:rounded-lg">

            <!-- Table -->
            <div class="flex flex-col overflow-y-auto">
                <div class="align-middle inline-block min-w-full">
                    <div class="shadow border-b border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">

                                <tr>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-dotted border-r-teal-300">

                                    </th>
                                    <th scope="col" colspan="8" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50 border-r border-dotted border-r-fuchsia-300">
                                        <div class="font-bold text-teal-500">TRANSACTION</div>
                                    </th>
                                    <th scope="col" colspan="5" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50 border-r border-dotted border-r-violet-300">
                                        <div class="font-bold text-fuchsia-500">SUBSCRIPTION</div>
                                    </th>
                                    <th scope="col" colspan="7" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50">
                                        <div class="font-bold text-violet-500">SUBSCRIPTION PLAN</div>
                                    </th>
                                </tr>

                                <tr>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-dotted border-r-teal-300">
                                        <span>Mobile</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Status</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Amount</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Funds Before Deduction</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Funds After Deduction</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Rating Type</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Billed Automatically</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Description</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50 border-r border-dotted border-r-fuchsia-300">
                                        <span>Created</span>
                                    </th>


                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Start</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>End</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Cancelled</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Active</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50 border-r border-dotted border-r-violet-300">
                                        <span>Created</span>
                                    </th>

                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50">
                                        <span>Name</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50">
                                        <span>Description</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50">
                                        <span>Active</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50">
                                        <span>Tags</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50">
                                        <span>Duration</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50">
                                        <span>Price</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50">
                                        <span>Created</span>
                                    </th>

                                </tr>

                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">

                                <tr v-for="billingTransaction in billingTransactionsPayload.data" :key="billingTransaction.id">

                                    <!-- Mobile -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 border-r border-dotted border-r-teal-300">
                                        <div class="text-sm text-gray-900">{{ billingTransaction.subscriber.msisdn }}</div>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-teal-50">
                                        <BillingTransactionStatusBadge :billingTransaction="billingTransaction"></BillingTransactionStatusBadge>
                                    </td>

                                    <!-- Amount -->
                                    <td class="px-6 py-3 whitespace-nowrap text-md text-gray-500 text-center font-bold bg-teal-50">
                                        <span>{{ billingTransaction.amount.amount_with_currency }}</span>
                                    </td>

                                    <!-- Funds Before Deduction -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50">
                                        {{ billingTransaction.funds_before_deduction == null ? '...' : billingTransaction.funds_before_deduction.amount_with_currency }}
                                    </td>

                                    <!-- Funds After Deduction -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50">
                                        {{ billingTransaction.funds_after_deduction == null ? '...' : billingTransaction.funds_after_deduction.amount_with_currency }}
                                    </td>

                                    <!-- Rating Type -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50">
                                        <span v-if="billingTransaction.rating_type == null">...</span>
                                        <RatingTypeBadge v-else :billingTransaction="billingTransaction"></RatingTypeBadge>
                                    </td>

                                    <!-- Billed Automatically -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50">
                                        <CreatedUsingAutoBillingBadge :billingTransaction="billingTransaction"></CreatedUsingAutoBillingBadge>
                                    </td>

                                    <!-- Description -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50">
                                        <span>{{ billingTransaction.description }}</span>
                                    </td>

                                    <!-- Created Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 bg-teal-50 border-r border-dotted border-r-fuchsia-300">
                                        {{ billingTransaction.created_at == null ? '...' : moment(billingTransaction.created_at).format('lll') }}
                                    </td>




                                    <!-- Subscription Start Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-fuchsia-50">
                                        {{ billingTransaction.subscription == null ? '...' : moment(billingTransaction.subscription.start_at).format('lll') }}
                                    </td>
                                    <!-- Subscription End Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-fuchsia-50">
                                        {{ billingTransaction.subscription == null ? '...' : moment(billingTransaction.subscription.end_at).format('lll') }}
                                    </td>
                                    <!-- Subscription Cancelled Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-fuchsia-50">
                                        {{ billingTransaction.subscription == null || billingTransaction.subscription.cancelled_at == null ? '...' : moment(billingTransaction.subscription.cancelled_at).format('lll') }}
                                    </td>
                                    <!-- Subscription Status -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50">
                                        <span v-if="billingTransaction.subscription == null">...</span>
                                        <SubscriptionActiveStatusBadge v-else :subscription="billingTransaction.subscription"></SubscriptionActiveStatusBadge>
                                    </td>
                                    <!-- Subscription Created Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-fuchsia-50 border-r border-dotted border-r-violet-300">
                                        {{ billingTransaction.subscription == null ? '...' : moment(billingTransaction.subscription.created_at).format('lll') }}
                                    </td>




                                    <!-- Subscription Plan Name -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-violet-50">
                                        <div v-if="billingTransaction.subscription_plan == null">...</div>
                                        <div v-else class="text-sm text-gray-900">{{ billingTransaction.subscription_plan.name }}</div>
                                    </td>

                                    <!-- Subscription Plan Description -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-violet-50">
                                        <div v-if="billingTransaction.subscription_plan == null">...</div>
                                        <div v-else>{{ billingTransaction.subscription_plan.description == null ? '...' : billingTransaction.subscription_plan.description }}</div>
                                    </td>

                                    <!-- Subscription Plan Active -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-violet-50">
                                        <div v-if="billingTransaction.subscription_plan == null">...</div>
                                        <SubscriptionPlanActiveStatusBadge v-else :subscriptionPlan="billingTransaction.subscription_plan"></SubscriptionPlanActiveStatusBadge>
                                    </td>

                                    <!-- Subscription Plan Tags -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-violet-50">
                                        <div v-if="billingTransaction.subscription_plan == null">...</div>
                                        <div v-else-if="billingTransaction.subscription_plan.tags.length == 0">...</div>
                                        <div v-else class="flex space-x-2">
                                            <div v-for="(tag, index) in billingTransaction.subscription_plan.tags" :key="index" class="bg-blue-50 text-blue-500 border border-blue-300 py-1 px-2 text-xs rounded">
                                                {{ tag }}
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Subscription Plan Duration -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-violet-50">
                                        <div v-if="billingTransaction.subscription_plan == null">...</div>
                                        <div v-else>{{ billingTransaction.subscription_plan.duration_in_words == null ? '...' : billingTransaction.subscription_plan.duration_in_words }}</div>
                                    </td>

                                    <!-- Subscription Plan Price -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-violet-50">
                                        <div v-if="billingTransaction.subscription_plan == null">...</div>
                                        <div v-else>{{ billingTransaction.subscription_plan.price == null ? '...' : billingTransaction.subscription_plan.price.amount_with_currency }}</div>
                                    </td>
                                    <!-- Subscription Plan Created Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-violet-50">
                                        <div v-if="billingTransaction.subscription_plan == null">...</div>
                                        <div v-else>{{ billingTransaction.subscription_plan.created_at == null ? '...' : moment(billingTransaction.subscription_plan.created_at).format('lll') }}</div>
                                    </td>

                                </tr>

                                <tr v-if="billingTransactionsPayload.data.length == 0">

                                    <!-- Content -->
                                    <td :colspan="10" class="px-6 py-3 whitespace-nowrap">
                                        <div class="text-center text-gray-900 text-sm p-6">No billing transactions</div>
                                    </td>
                                    <td :colspan="10" class="px-6 py-3 whitespace-nowrap">
                                        <div class="text-center text-gray-900 text-sm p-6">No billing transactions</div>
                                    </td>

                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <pagination class="mt-6" :paginationPayload="billingTransactionsPayload" :updateData="['billingTransactionsPayload']" />

        </div>

    </div>

</template>
<script>

    import SubscriptionPlanActiveStatusBadge from './../../../SubscriptionPlans/List/Partials/ActiveStatusBadge.vue';
    import SubscriptionActiveStatusBadge from './../../../Subscriptions/List/Partials/ActiveStatusBadge.vue';
    import BillingTransactionStatusBadge from './BillingTransactionStatusBadge.vue';
    import CreatedUsingAutoBillingBadge from './CreatedUsingAutoBillingBadge.vue';
    import Pagination from '../../../../Partials/Pagination.vue';
    import JetInputError from '@/Components/InputError.vue';
    import RatingTypeBadge from './RatingTypeBadge.vue';
    import JetInput from '@/Components/TextInput.vue';
    import { defineComponent } from 'vue';
    import moment from "moment";

    export default defineComponent({
        components: {
            SubscriptionPlanActiveStatusBadge, SubscriptionActiveStatusBadge, Pagination, RatingTypeBadge, BillingTransactionStatusBadge, CreatedUsingAutoBillingBadge,
            JetInputError, JetInput
        },
        props: {
            billingTransactionsPayload: Object
        },
        data() {
            return {
                refreshContentInterval: null,
                moment: moment,
                form: this.$inertia.form({
                    search: ''
                })
            }
        },
        methods: {
            refreshContent()
            {
                this.$inertia.reload();
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
