<template>

    <div>

        <div class="grid grid-cols-12 gap-4">

            <div class="col-span-6">
                <div class="bg-gray-50 border-b px-6 py-4 rounded-t text-gray-500 text-sm mb-4">
                    <div class="text-2xl font-semibold leading-6 text-gray-500">Auto Billing Schedules</div>
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
                                    <th scope="col" colspan="7" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50 border-r border-dotted border-r-fuchsia-300">
                                        <div class="font-bold text-teal-500">AUTO BILLING SCHEDULE</div>
                                    </th>
                                    <th scope="col" colspan="8" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50 border-r border-dotted border-r-violet-300">
                                        <div class="font-bold text-fuchsia-500">LAST AUTO BILLING TRANSACTION</div>
                                    </th>
                                    <th scope="col" colspan="6" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50 border-r border-dotted border-r-teal-300">
                                        <div class="font-bold text-violet-500">AUTO BILLING REMINDER</div>
                                    </th>
                                    <th scope="col" colspan="6" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <div class="font-bold text-teal-500">PRICING PLAN</div>
                                    </th>
                                </tr>

                                <tr>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-dotted border-r-teal-300">
                                        <span>Mobile</span>
                                    </th>




                                    <!-- Auto Billing Enabled -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Auto Billing Enabled</span>
                                    </th>
                                    <!-- Next Attempt Date -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Next Attempt Date</span>
                                    </th>
                                    <!-- Next Attempt Countdown -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Countdown</span>
                                    </th>
                                    <!-- Attempt -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Attempts Before Disabling</span>
                                    </th>
                                    <!-- Overall Attempts -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Overall Attempts</span>
                                    </th>
                                    <!-- Overall Successful Attempts -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Overall Successful Attempts</span>
                                    </th>
                                    <!-- Overall Failed Attempts -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Overall Failed Attempts</span>
                                    </th>
                                    <!-- Created Date -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50 border-r border-dotted border-r-fuchsia-300">
                                        <span>Created Date</span>
                                    </th>




                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Status</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Amount</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Funds Before Deduction</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Funds After Deduction</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Rating Type</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Auto Billing</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Description</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50 border-r border-dotted border-r-violet-300">
                                        <span>Created</span>
                                    </th>


                                    <!-- Reminded 72 Hours Before -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50">
                                        <span>72 Hours Before</span>
                                    </th>
                                    <!-- Reminded 48 Hours Before -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50">
                                        <span>48 Hours Before</span>
                                    </th>
                                    <!-- Reminded 24 Hours Before -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50">
                                        <span>24 Hours Before</span>
                                    </th>
                                    <!-- Reminded 12 Hours Before -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50">
                                        <span>12 Hours Before</span>
                                    </th>
                                    <!-- Reminded 6 Hours Before -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50">
                                        <span>6 Hours Before</span>
                                    </th>
                                    <!-- Reminded 1 Hour Before -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50 border-r border-dotted border-r-teal-300">
                                        <span>1 Hour Before</span>
                                    </th>




                                    <!-- Pricing Plan Name -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Name</span>
                                    </th>
                                    <!-- Pricing Plan Description -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Description</span>
                                    </th>
                                    <!-- Pricing Plan Active -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Active</span>
                                    </th>
                                    <!-- Pricing Plan Duration -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Duration</span>
                                    </th>
                                    <!-- Pricing Plan Price -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Price</span>
                                    </th>
                                    <!-- Pricing Plan Created -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Created</span>
                                    </th>

                                </tr>

                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">

                                <tr v-for="autoBillingSchedule in autoBillingSchedulesPayload.data" :key="autoBillingSchedule.id">

                                    <!-- Mobile -->
                                    <td class="px-6 py-3 whitespace-nowrap border-r border-dotted border-r-teal-300">
                                        <div class="text-sm text-gray-900">{{ autoBillingSchedule.subscriber.msisdn }}</div>
                                    </td>




                                    <!-- Auto Billing Enabled -->
                                    <td class="px-6 py-3 text-sm text-gray-500 text-center bg-teal-50">
                                        <AutoBillingEnabledStatusBadge :autoBillingSchedule="autoBillingSchedule"></AutoBillingEnabledStatusBadge>
                                    </td>
                                    <!-- Next Attempt Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-teal-50">
                                        {{ autoBillingSchedule.next_attempt_date == null ? '...' : moment(autoBillingSchedule.next_attempt_date).format('lll') }}
                                    </td>
                                    <!-- Next Attempt Countdown -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50">
                                        <span v-if="autoBillingSchedule.next_attempt_date_milli_seconds_left == null">...</span>
                                        <Countdown v-else :time="autoBillingSchedule.next_attempt_date_milli_seconds_left"></Countdown>
                                    </td>
                                    <!-- Attempt -->
                                    <td class="px-6 py-3 text-sm text-gray-500 text-center bg-teal-50">
                                        {{ autoBillingSchedule.attempt == null ? '...' : autoBillingSchedule.attempt }}
                                            /
                                        {{ autoBillingSchedule.pricing_plan == null ? '...' : (autoBillingSchedule.pricing_plan.max_auto_billing_attempts == 0 ? 'unlimited' : autoBillingSchedule.pricing_plan.max_auto_billing_attempts) }}
                                    </td>
                                    <!-- Overall Attempts -->
                                    <td class="px-6 py-3 text-sm text-gray-500 text-center bg-teal-50">
                                        {{ autoBillingSchedule.overall_attempts == null ? '...' : autoBillingSchedule.overall_attempts }}
                                    </td>
                                    <!-- Overall Successful Attempts -->
                                    <td class="px-6 py-3 text-sm text-gray-500 text-center bg-teal-50">
                                        {{ autoBillingSchedule.overall_successful_attempts == null ? '...' : autoBillingSchedule.overall_successful_attempts }}
                                    </td>
                                    <!-- Overall Failed Attempts -->
                                    <td class="px-6 py-3 text-sm text-gray-500 text-center bg-teal-50">
                                        {{ autoBillingSchedule.overall_failed_attempts == null ? '...' : autoBillingSchedule.overall_failed_attempts }}
                                    </td>
                                    <!-- Created Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-teal-50 border-r border-dotted border-r-fuchsia-300">
                                        {{ autoBillingSchedule.created_at == null ? '...' : moment(autoBillingSchedule.created_at).format('lll') }}
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-fuchsia-50">
                                        <template v-if="getLatestAutoBillingTransaction(autoBillingSchedule).is_successful">
                                            <BillingTransactionStatusBadge :billingTransaction="getLatestAutoBillingTransaction(autoBillingSchedule)"></BillingTransactionStatusBadge>
                                        </template>
                                        <span v-else>...</span>
                                    </td>
                                    <!-- Amount -->
                                    <td class="px-6 py-3 whitespace-nowrap text-md text-gray-500 text-center font-bold bg-fuchsia-50">
                                        <span v-if="getLatestAutoBillingTransaction(autoBillingSchedule).amount">{{ getLatestAutoBillingTransaction(autoBillingSchedule).amount.amount_with_currency }}</span>
                                        <span v-else>...</span>
                                    </td>
                                    <!-- Funds Before Deduction -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50">
                                        {{ getLatestAutoBillingTransaction(autoBillingSchedule).funds_before_deduction == null ? '...' : getLatestAutoBillingTransaction(autoBillingSchedule).funds_before_deduction.amount_with_currency }}
                                    </td>
                                    <!-- Funds After Deduction -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50">
                                        {{ getLatestAutoBillingTransaction(autoBillingSchedule).funds_after_deduction == null ? '...' : getLatestAutoBillingTransaction(autoBillingSchedule).funds_after_deduction.amount_with_currency }}
                                    </td>
                                    <!-- Rating Type -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50">
                                        <template v-if="getLatestAutoBillingTransaction(autoBillingSchedule).rating_type">
                                            <RatingTypeBadge :billingTransaction="getLatestAutoBillingTransaction(autoBillingSchedule)"></RatingTypeBadge>
                                        </template>
                                        <span v-else>...</span>
                                    </td>
                                    <!-- Created Using Auto Billing -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50">
                                        <template v-if="getLatestAutoBillingTransaction(autoBillingSchedule).created_using_auto_billing">
                                            <CreatedUsingAutoBillingBadge :billingTransaction="getLatestAutoBillingTransaction(autoBillingSchedule)"></CreatedUsingAutoBillingBadge>
                                        </template>
                                        <span v-else>...</span>
                                    </td>
                                    <!-- Description -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50">
                                        <span>{{ getLatestAutoBillingTransaction(autoBillingSchedule).description ?? '...' }}</span>
                                    </td>
                                    <!-- Created Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 bg-fuchsia-50 border-r border-dotted border-r-violet-300">
                                        {{ getLatestAutoBillingTransaction(autoBillingSchedule).created_at == null ? '...' : moment(getLatestAutoBillingTransaction(autoBillingSchedule).created_at).format('lll') }}
                                    </td>




                                    <!-- Reminded 72 Hours Before -->
                                    <td class="px-6 py-3 text-xs text-gray-500 bg-violet-50">
                                        <AutoBillingReminderStatusBadge :hours="72" :autoBillingSchedule="autoBillingSchedule"></AutoBillingReminderStatusBadge>
                                    </td>
                                    <!-- Reminded 48 Hours Before -->
                                    <td class="px-6 py-3 text-xs text-gray-500 bg-violet-50">
                                        <AutoBillingReminderStatusBadge :hours="48" :autoBillingSchedule="autoBillingSchedule"></AutoBillingReminderStatusBadge>
                                    </td>
                                    <!-- Reminded 24 Hours Before -->
                                    <td class="px-6 py-3 text-xs text-gray-500 bg-violet-50">
                                        <AutoBillingReminderStatusBadge :hours="24" :autoBillingSchedule="autoBillingSchedule"></AutoBillingReminderStatusBadge>
                                    </td>
                                    <!-- Reminded 12 Hours Before -->
                                    <td class="px-6 py-3 text-xs text-gray-500 bg-violet-50">
                                        <AutoBillingReminderStatusBadge :hours="12" :autoBillingSchedule="autoBillingSchedule"></AutoBillingReminderStatusBadge>
                                    </td>
                                    <!-- Reminded 6 Hours Before -->
                                    <td class="px-6 py-3 text-xs text-gray-500 bg-violet-50">
                                        <AutoBillingReminderStatusBadge :hours="6" :autoBillingSchedule="autoBillingSchedule"></AutoBillingReminderStatusBadge>
                                    </td>
                                    <!-- Reminded 1 Hour Before -->
                                    <td class="px-6 py-3 text-xs text-gray-500 bg-violet-50 border-r border-dotted border-r-teal-300">
                                        <AutoBillingReminderStatusBadge :hours="1" :autoBillingSchedule="autoBillingSchedule"></AutoBillingReminderStatusBadge>
                                    </td>


                                    <!-- Pricing Plan Name -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-teal-50">
                                        <div class="text-sm text-gray-900">{{ autoBillingSchedule.pricing_plan.name }}</div>
                                    </td>
                                    <!-- Pricing Plan Description -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-teal-50">
                                        {{ autoBillingSchedule.pricing_plan.description == null ? '...' : autoBillingSchedule.pricing_plan.description }}
                                    </td>
                                    <!-- Pricing Plan Active -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50">
                                        <PricingPlanActiveStatusBadge :pricingPlan="autoBillingSchedule.pricing_plan"></PricingPlanActiveStatusBadge>
                                    </td>
                                    <!-- Pricing Plan Duration -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50">
                                        {{ autoBillingSchedule.pricing_plan.duration_in_words == null ? '...' : autoBillingSchedule.pricing_plan.duration_in_words }}
                                    </td>
                                    <!-- Pricing Plan Price -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50">
                                        {{ autoBillingSchedule.pricing_plan.price == null ? '...' : autoBillingSchedule.pricing_plan.price.amount_with_currency }}
                                    </td>
                                    <!-- Pricing Plan Created Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-teal-50">
                                        {{ autoBillingSchedule.pricing_plan.created_at == null ? '...' : moment(autoBillingSchedule.pricing_plan.created_at).format('lll') }}
                                    </td>

                                </tr>

                                <tr v-if="autoBillingSchedulesPayload.data.length == 0">

                                    <!-- Content -->
                                    <td :colspan="10" class="px-6 py-3 whitespace-nowrap">
                                        <div class="text-center text-gray-900 text-sm p-6">No auto billing schedules</div>
                                    </td>

                                    <td :colspan="10" class="px-6 py-3 whitespace-nowrap">
                                        <div class="text-center text-gray-900 text-sm p-6">No auto billing schedules</div>
                                    </td>

                                    <td :colspan="7" class="px-6 py-3 whitespace-nowrap">
                                        <div class="text-center text-gray-900 text-sm p-6">No auto billing schedules</div>
                                    </td>

                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <pagination class="mt-6" :paginationPayload="autoBillingSchedulesPayload" :updateData="['autoBillingSchedulesPayload']" />

        </div>

    </div>

</template>

<script>
    import BillingTransactionStatusBadge from './../../../BillingTransactions/List/Partials/BillingTransactionStatusBadge.vue';
    import CreatedUsingAutoBillingBadge from './../../../BillingTransactions/List/Partials/CreatedUsingAutoBillingBadge.vue';
    import PricingPlanActiveStatusBadge from './../../../PricingPlans/List/Partials/ActiveStatusBadge.vue';
    import RatingTypeBadge from './../../../BillingTransactions/List/Partials/RatingTypeBadge.vue';
    import AutoBillingReminderStatusBadge from './AutoBillingReminderStatusBadge.vue';
    import AutoBillingEnabledStatusBadge from './AutoBillingEnabledStatusBadge.vue';
    import Pagination from '../../../../Partials/Pagination.vue';
    import Countdown from './../../../../Partials/Countdown.vue';
    import { defineComponent } from 'vue';
    import moment from "moment";

    export default defineComponent({
        components: {
            Countdown, PricingPlanActiveStatusBadge, AutoBillingReminderStatusBadge, AutoBillingEnabledStatusBadge, Pagination,
            BillingTransactionStatusBadge, RatingTypeBadge, CreatedUsingAutoBillingBadge
        },
        props: {
            autoBillingSchedulesPayload: Object
        },
        data() {
            return {
                refreshContentInterval: null,
                moment: moment
            }
        },
        methods: {
            getLatestAutoBillingTransaction(autoBillingSchedule)
            {
                return autoBillingSchedule.subscriber.latest_auto_billing_transaction ?? {};
            },
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
