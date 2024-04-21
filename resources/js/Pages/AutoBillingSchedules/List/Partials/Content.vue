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
                                        <div class="font-bold text-teal-500">SUBSCRIPTION PLAN</div>
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
                                    <!-- Attempts -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Attempts</span>
                                    </th>
                                    <!-- Total Successful Attempts -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Total Successful Attempts</span>
                                    </th>
                                    <!-- Total Failed Attempts -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Total Failed Attempts</span>
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


                                    <!-- Reminded 1 Hour Before -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50">
                                        <span>1 Hour Before</span>
                                    </th>
                                    <!-- Reminded 6 Hours Before -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50">
                                        <span>6 Hours Before</span>
                                    </th>
                                    <!-- Reminded 12 Hours Before -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50">
                                        <span>12 Hours Before</span>
                                    </th>
                                    <!-- Reminded 24 Hours Before -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50">
                                        <span>24 Hours Before</span>
                                    </th>
                                    <!-- Reminded 48 Hours Before -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50">
                                        <span>48 Hours Before</span>
                                    </th>
                                    <!-- Reminded 72 Hours Before -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-violet-50 border-r border-dotted border-r-teal-300">
                                        <span>72 Hours Before</span>
                                    </th>




                                    <!-- Subscription Plan Name -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Name</span>
                                    </th>
                                    <!-- Subscription Plan Description -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Description</span>
                                    </th>
                                    <!-- Subscription Plan Active -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Active</span>
                                    </th>
                                    <!-- Subscription Plan Duration -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Duration</span>
                                    </th>
                                    <!-- Subscription Plan Price -->
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Price</span>
                                    </th>
                                    <!-- Subscription Plan Created -->
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
                                        <vue-countdown :time="autoBillingSchedule.next_attempt_date_milli_seconds_left" v-slot="{ days, hours, minutes, seconds }">

                                            <span v-if="days > 0">{{ days }} {{ days == 1 ? 'day ' : 'days '}}</span>
                                            <span v-if="hours > 0">{{ hours }} {{ hours == 1 ? 'hr ' : 'hrs '}}</span>
                                            <span v-if="days == 0 && minutes > 0">{{ minutes }} {{ minutes == 1 ? 'min ' : 'mins '}}</span>
                                            <span v-if="hours == 0 && seconds > 0">{{ seconds }} {{ seconds == 1 ? 'sec ' : 'secs '}}</span>

                                            <span v-if="days == 0 && hours == 0 && minutes == 0 && seconds == 0">Ended</span>

                                        </vue-countdown>
                                    </td>
                                    <!-- Attempts -->
                                    <td class="px-6 py-3 text-sm text-gray-500 text-center bg-teal-50">
                                        {{ autoBillingSchedule.attempts == null ? '...' : autoBillingSchedule.attempts }}
                                            /
                                        {{ autoBillingSchedule.subscription_plan == null ? '...' : autoBillingSchedule.subscription_plan.max_auto_billing_attempts }}
                                    </td>
                                    <!-- Total Successful Attempts -->
                                    <td class="px-6 py-3 text-sm text-gray-500 text-center bg-teal-50">
                                        {{ autoBillingSchedule.total_successful_attempts == null ? '...' : autoBillingSchedule.total_successful_attempts }}
                                    </td>
                                    <!-- Total Failed Attempts -->
                                    <td class="px-6 py-3 text-sm text-gray-500 text-center bg-teal-50">
                                        {{ autoBillingSchedule.total_failed_attempts == null ? '...' : autoBillingSchedule.total_failed_attempts }}
                                    </td>
                                    <!-- Created Date -->
                                    <td class="px-6 py-3 text-sm text-gray-500 text-left bg-teal-50 border-r border-dotted border-r-fuchsia-300">
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




                                    <!-- Reminded 1 Hour Before -->
                                    <td class="px-6 py-3 text-sm text-gray-500 bg-violet-50">
                                        <AutoBillingReminderStatusBadge :hours="1" :autoBillingSchedule="autoBillingSchedule"></AutoBillingReminderStatusBadge>
                                    </td>
                                    <!-- Reminded 6 Hours Before -->
                                    <td class="px-6 py-3 text-sm text-gray-500 bg-violet-50">
                                        <AutoBillingReminderStatusBadge :hours="6" :autoBillingSchedule="autoBillingSchedule"></AutoBillingReminderStatusBadge>
                                    </td>
                                    <!-- Reminded 12 Hours Before -->
                                    <td class="px-6 py-3 text-sm text-gray-500 bg-violet-50">
                                        <AutoBillingReminderStatusBadge :hours="12" :autoBillingSchedule="autoBillingSchedule"></AutoBillingReminderStatusBadge>
                                    </td>
                                    <!-- Reminded 24 Hours Before -->
                                    <td class="px-6 py-3 text-sm text-gray-500 bg-violet-50">
                                        <AutoBillingReminderStatusBadge :hours="24" :autoBillingSchedule="autoBillingSchedule"></AutoBillingReminderStatusBadge>
                                    </td>
                                    <!-- Reminded 48 Hours Before -->
                                    <td class="px-6 py-3 text-sm text-gray-500 bg-violet-50">
                                        <AutoBillingReminderStatusBadge :hours="48" :autoBillingSchedule="autoBillingSchedule"></AutoBillingReminderStatusBadge>
                                    </td>
                                    <!-- Reminded 72 Hours Before -->
                                    <td class="px-6 py-3 text-sm text-gray-500 bg-violet-50 border-r border-dotted border-r-teal-300">
                                        <AutoBillingReminderStatusBadge :hours="72" :autoBillingSchedule="autoBillingSchedule"></AutoBillingReminderStatusBadge>
                                    </td>


                                    <!-- Subscription Plan Name -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-teal-50">
                                        <div class="text-sm text-gray-900">{{ autoBillingSchedule.subscription_plan.name }}</div>
                                    </td>
                                    <!-- Subscription Plan Description -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-teal-50">
                                        {{ autoBillingSchedule.subscription_plan.description == null ? '...' : autoBillingSchedule.subscription_plan.description }}
                                    </td>
                                    <!-- Subscription Plan Active -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50">
                                        <SubscriptionPlanActiveStatusBadge :subscriptionPlan="autoBillingSchedule.subscription_plan"></SubscriptionPlanActiveStatusBadge>
                                    </td>
                                    <!-- Subscription Plan Duration -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50">
                                        {{ autoBillingSchedule.subscription_plan.duration_in_words == null ? '...' : autoBillingSchedule.subscription_plan.duration_in_words }}
                                    </td>
                                    <!-- Subscription Plan Price -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50">
                                        {{ autoBillingSchedule.subscription_plan.price == null ? '...' : autoBillingSchedule.subscription_plan.price.amount_with_currency }}
                                    </td>
                                    <!-- Subscription Plan Created Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-teal-50">
                                        {{ autoBillingSchedule.subscription_plan.created_at == null ? '...' : moment(autoBillingSchedule.subscription_plan.created_at).format('lll') }}
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
    import SubscriptionPlanActiveStatusBadge from './../../../SubscriptionPlans/List/Partials/ActiveStatusBadge.vue';
    import RatingTypeBadge from './../../../BillingTransactions/List/Partials/RatingTypeBadge.vue';
    import AutoBillingReminderStatusBadge from './AutoBillingReminderStatusBadge.vue';
    import AutoBillingEnabledStatusBadge from './AutoBillingEnabledStatusBadge.vue';
    import Pagination from '../../../../Partials/Pagination.vue';
    import VueCountdown from '@chenfengyuan/vue-countdown';
    import { defineComponent } from 'vue';
    import moment from "moment";

    export default defineComponent({
        components: {
            VueCountdown, SubscriptionPlanActiveStatusBadge, AutoBillingReminderStatusBadge, AutoBillingEnabledStatusBadge, Pagination,
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
