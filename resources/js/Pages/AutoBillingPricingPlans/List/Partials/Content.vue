<template>

    <div>

        <div class="bg-gray-50 border-b px-6 py-4 rounded-t text-gray-500 text-sm mb-4">

            <div class="text-2xl font-semibold leading-6 text-gray-500 border-b pb-4 mb-4">Auto Billing</div>

            <div class="flex items-center">

                <!-- Note -->
                <span v-if="projectPayload.can_auto_bill" class="text-gray-400"><span class="text-green-500 font-bold">Auto Billing is enabled</span> — You can turn off "Auto Billing" in settings (This affects all pricing plans)</span>
                <span v-else class="text-gray-400"><span class="text-red-500 font-bold">Auto Billing is disabled</span> — You can turn on "Auto Billing" in settings</span>

                <el-popover :width="400">
                    <template #reference>
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                            <path strokeLinecap="round" strokeLinejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                        </svg>
                    </template>
                    <template #default>
                        <span v-if="projectPayload.can_auto_bill" class="break-normal">
                            Turning off "Auto Billing" from the project settings means that every pricing plan won't be able to auto bill even if the pricing plan "Auto Billing" option is turned on
                            <hr class="my-4">
                            After turning off "Auto Billing" from the project settings, any running pricing plans will complete their last sprint before completely stopping to auto bill.
                        </span>
                        <span v-else class="break-normal">
                            Turning on "Auto Billing" from the project settings means that every pricing plan will be able to auto bill as long as the pricing plan "Auto Billing" option is turned on
                        </span>
                    </template>
                </el-popover>

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
                                <tr v-for="pricingPlan in autoBillingPricingPlansPayload.data" :key="pricingPlan.id">
                                    <!-- Name -->
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ pricingPlan.name }}</div>
                                    </td>
                                    <!-- Send SMS -->
                                    <td class="px-6 py-3">
                                        <PricingPlanCanAutoBillBadge :pricingPlan="pricingPlan"></PricingPlanCanAutoBillBadge>
                                    </td>
                                    <!-- Status -->
                                    <td class="px-6 py-3">
                                        <AutoBillingStatusBadge :autoBillingPricingPlanJobBatch="getLatestPricingPlanBatchJob(pricingPlan)"></AutoBillingStatusBadge>
                                    </td>
                                    <!-- Sprints -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                        <div class="text-sm text-gray-900">{{ pricingPlan.auto_billing_job_batches_count }}</div>
                                    </td>
                                    <!-- Total -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50">
                                        <div class="text-sm text-gray-900">{{ getLatestPricingPlanBatchJob(pricingPlan).total_jobs }}</div>
                                    </td>
                                    <!-- Pending -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50">
                                        <div class="text-sm text-gray-900">{{ getLatestPricingPlanBatchJob(pricingPlan).pending_jobs }}</div>
                                    </td>
                                    <!-- Processed -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50">
                                        <div class="text-sm text-gray-900">{{ getLatestPricingPlanBatchJob(pricingPlan).processed_jobs }}</div>
                                    </td>
                                    <!-- Progress -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-indigo-50">
                                        <span class="text-lg text-green-600">{{ getLatestPricingPlanBatchJob(pricingPlan).progress }} {{ getLatestPricingPlanBatchJob(pricingPlan).progress ? '%' : '' }}</span>
                                    </td>
                                    <!-- Last Sprint Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ getLatestPricingPlanBatchJob(pricingPlan).created_at == null ? '...' : moment(getLatestPricingPlanBatchJob(pricingPlan).created_at).format('lll') }}
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <a v-if="$inertia.page.props.projectPermissions.includes('View pricing plans')" href="#" @click.prevent="$inertia.get(route('show.auto.billing.pricing.plan.job.batches', { project: route().params.project, pricing_plan: pricingPlan.id }))" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                    </td>
                                </tr>

                                <tr v-if="autoBillingPricingPlansPayload.data.length == 0">
                                    <!-- Content -->
                                    <td :colspan="10" class="px-6 py-3 whitespace-nowrap">
                                        <div class="text-center text-gray-900 text-sm p-6">No pricing plans</div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <pagination class="mt-6" :paginationPayload="autoBillingPricingPlansPayload" :updateData="['autoBillingPricingPlansPayload']" />

        </div>

    </div>

</template>
<script>
    import PricingPlanCanAutoBillBadge from '../JobBatches/List/Partials/PricingPlanCanAutoBillBadge.vue'
    import AutoBillingStatusBadge from '../JobBatches/List/Partials/AutoBillingStatusBadge.vue'
    import Pagination from '../../../../Partials/Pagination.vue'
    import { defineComponent } from 'vue'
    import moment from "moment";

    export default defineComponent({
        components: {
            Pagination, PricingPlanCanAutoBillBadge, AutoBillingStatusBadge
        },
        props: {
            projectPayload: Object,
            autoBillingPricingPlansPayload: Object,
        },
        data() {
            return {
                refreshContentInterval: null,
                pricingPlan: null,
                moment: moment
            }
        },
        methods: {
            refreshContent()
            {
                this.$inertia.reload();
            },
            getLatestPricingPlanBatchJob(pricingPlan)
            {
                if( pricingPlan.latest_auto_billing_job_batch.length ) {
                    return pricingPlan.latest_auto_billing_job_batch[0];
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
