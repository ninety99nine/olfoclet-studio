<template>

    <div>

        <div class="bg-gray-50 border-b pl-6 py-3 rounded-t text-gray-500 text-sm mb-4 flex items-center">

            <!-- Note -->
            <span v-if="projectPayload.can_auto_bill" class="text-gray-400"><span class="text-green-500 font-bold">Auto Billing is enabled</span> — You can turn off "Auto Billing" in settings (This affects all subscription plans)</span>
            <span v-else class="text-gray-400"><span class="text-red-500 font-bold">Auto Billing is disabled</span> — You can turn on "Auto Billing" in settings</span>

            <el-popover :width="400">
                <template #reference>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                </template>
                <template #default>
                    <span v-if="projectPayload.can_auto_bill" class="break-normal">
                        Turning off "Auto Billing" from the project settings means that every subscription plan won't be able to auto bill even if the subscription plan "Auto Billing" option is turned on
                        <hr class="my-4">
                        After turning off "Auto Billing" from the project settings, any running subscription plans will complete their last sprint before completely stopping to auto bill.
                    </span>
                    <span v-else class="break-normal">
                        Turning on "Auto Billing" from the project settings means that every subscription plan will be able to auto bill as long as the subscription plan "Auto Billing" option is turned on
                    </span>
                </template>
            </el-popover>

        </div>

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
                                <span>Name</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Auto Bill</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Status</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Sprints</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-100">
                                <span>Total</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-100">
                                <span>Pending</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-100">
                                <span>Processed</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-indigo-100">
                                <span>Progress</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Last Sprint Date</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Actions</span>
                            </th>
                        </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="subscriptionPlan in autoBillingSubscriptionPlansPayload.data" :key="subscriptionPlan.id">
                                <!-- Name -->
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ subscriptionPlan.name }}</div>
                                </td>
                                <!-- Send SMS -->
                                <td class="px-6 py-3">
                                    <SubscriptionPlanCanAutoBillBadge :subscriptionPlan="subscriptionPlan"></SubscriptionPlanCanAutoBillBadge>
                                </td>
                                <!-- Status -->
                                <td class="px-6 py-3">
                                    <AutoBillingStatusBadge :autoBillingSubscriptionPlanJobBatch="getLatestSubscriptionPlanBatchJob(subscriptionPlan)"></AutoBillingStatusBadge>
                                </td>
                                <!-- Sprints -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                    <div class="text-sm text-gray-900">{{ subscriptionPlan.auto_billing_job_batches_count }}</div>
                                </td>
                                <!-- Total -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50">
                                    <div class="text-sm text-gray-900">{{ getLatestSubscriptionPlanBatchJob(subscriptionPlan).total_jobs }}</div>
                                </td>
                                <!-- Pending -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50">
                                    <div class="text-sm text-gray-900">{{ getLatestSubscriptionPlanBatchJob(subscriptionPlan).pending_jobs }}</div>
                                </td>
                                <!-- Processed -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50">
                                    <div class="text-sm text-gray-900">{{ getLatestSubscriptionPlanBatchJob(subscriptionPlan).processed_jobs }}</div>
                                </td>
                                <!-- Progress -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50">
                                    <span class="text-lg text-green-600">{{ getLatestSubscriptionPlanBatchJob(subscriptionPlan).progress }} {{ getLatestSubscriptionPlanBatchJob(subscriptionPlan).progress ? '%' : '' }}</span>
                                </td>
                                <!-- Last Sprint Date -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ getLatestSubscriptionPlanBatchJob(subscriptionPlan).created_at == null ? '...' : moment(getLatestSubscriptionPlanBatchJob(subscriptionPlan).created_at).format('lll') }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <a v-if="$inertia.page.props.projectPermissions.includes('View subscription plans')" href="#" @click.prevent="$inertia.get(route('show.auto.billing.subscription.plan.job.batches', { project: route().params.project, subscription_plan: subscriptionPlan.id }))" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                </td>
                            </tr>

                            <tr v-if="autoBillingSubscriptionPlansPayload.data.length == 0">
                                <!-- Content -->
                                <td :colspan="10" class="px-6 py-3 whitespace-nowrap">
                                    <div class="text-center text-gray-900 text-sm p-6">No subscription plans</div>
                                </td>
                            </tr>

                        </tbody>
                    </table>
                    </div>
                </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <pagination class="mt-6" :paginationPayload="autoBillingSubscriptionPlansPayload" :updateData="['autoBillingSubscriptionPlansPayload']" />

        </div>

    </div>

</template>
<script>
    import SubscriptionPlanCanAutoBillBadge from '../JobBatches/List/Partials/SubscriptionPlanCanAutoBillBadge.vue'
    import AutoBillingStatusBadge from '../JobBatches/List/Partials/AutoBillingStatusBadge.vue'
    import Pagination from '../../../../Partials/Pagination.vue'
    import { defineComponent } from 'vue'
    import moment from "moment";

    export default defineComponent({
        components: {
            Pagination, SubscriptionPlanCanAutoBillBadge, AutoBillingStatusBadge
        },
        props: {
            projectPayload: Object,
            autoBillingSubscriptionPlansPayload: Object,
        },
        data() {
            return {
                refreshContentInterval: null,
                subscriptionPlan: null,
                moment: moment
            }
        },
        methods: {
            refreshContent()
            {
                this.$inertia.reload();
            },
            getLatestSubscriptionPlanBatchJob(subscriptionPlan)
            {
                console.log('subscriptionPlan');
                console.log(subscriptionPlan);
                console.log('subscriptionPlan.latest_auto_billing_job_batch');
                console.log(subscriptionPlan.latest_auto_billing_job_batch);
                console.log('subscriptionPlan.latest_auto_billing_job_batch[0]');
                console.log(subscriptionPlan.latest_auto_billing_job_batch[0]);

                if( subscriptionPlan.latest_auto_billing_job_batch.length ) {
                    return subscriptionPlan.latest_auto_billing_job_batch[0];
                }
                return {};
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
