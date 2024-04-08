<template>

    <div>

        <div class="grid grid-cols-12 gap-8">

            <div class="col-span-6">
                <div class="bg-gray-50 border-b px-6 py-4 rounded-t text-gray-500 text-sm mb-4">
                    <div class="text-2xl font-semibold leading-6 text-gray-500">Billing Reports</div>
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
                                    <th scope="col" colspan="6" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50 border-r border-dotted border-r-fuchsia-300">
                                        <div class="font-bold text-teal-500">BREAKDOWN</div>
                                    </th>
                                    <th scope="col" colspan="3" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50 border-r border-dotted border-r-fuchsia-300">
                                        <div class="font-bold text-fuchsia-500">DOCUMENTS</div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-dotted border-r-teal-300">

                                    </th>
                                </tr>

                                <tr>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-dotted border-r-teal-300">
                                        <span>Name</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Gross Revenue</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Costs (31.5%)</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Sharable Revenue</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Our Share (60%)</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Their Share (40%)</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50 border-r border-dotted border-r-fuchsia-300">
                                        <span>Total Transactions</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Overview</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Transactions</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50 border-r border-dotted border-r-fuchsia-300">
                                        <span>Invoice</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <span>Created</span>
                                    </th>
                                </tr>

                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">

                                <tr v-for="billingReport in billingReportsPayload.data" :key="billingReport.id">

                                    <!-- Name -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 border-r border-dotted border-r-teal-300">

                                        <div class="flex space-x-2">
                                            <div class="text-sm text-gray-900">{{ billingReport.name }}</div>

                                            <el-popover :width="400">
                                                <template #reference>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                                        <path strokeLinecap="round" strokeLinejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                                    </svg>
                                                </template>
                                                <template #default>
                                                    <p>
                                                        This report outlines the performance of the <strong>{{ projectPayload.name }}</strong> service for <strong>{{ billingReport.name }}</strong>.
                                                        It indicates a total of <strong>{{ billingReport.total_transactions }} {{ billingReport.total_transactions == 1 ? 'transaction' : 'transactions'}}</strong>,
                                                        generating a gross revenue of <strong>{{ billingReport.gross_revenue.amount_with_currency }}</strong>. After deducting costs amounting to <strong>{{ billingReport.costs.amount_with_currency }}</strong>,
                                                        the sharable revenue is calculated to be <strong>{{ billingReport.sharable_revenue.amount_with_currency }}</strong>. Their share, at <strong>40%</strong>, is <strong>{{ billingReport.their_share.amount_with_currency }}</strong>,
                                                        while our share, at <strong>60%</strong>, is <strong>{{ billingReport.our_share.amount_with_currency }}</strong>. The cost breakdown reveals that expenses include USAF, BOCRA, VAT, and dealer commission, totaling <strong>{{ billingReport.costs.amount_with_currency }}</strong>.
                                                    </p>
                                                </template>
                                            </el-popover>
                                        </div>
                                    </td>



                                    <!-- Gross Revenue -->
                                    <td class="px-6 py-3 whitespace-nowrap text-md text-gray-500 text-left bg-teal-50">
                                        <span>{{ billingReport.gross_revenue.amount_with_currency }}</span>
                                    </td>

                                    <!-- Costs -->
                                    <td class="px-6 py-3 whitespace-nowrap text-md text-gray-500 text-left bg-teal-50">

                                        <div class="flex space-x-2">
                                            <span>{{ billingReport.costs.amount_with_currency }}</span>

                                            <el-popover :width="400">
                                                <template #reference>
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2" fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
                                                        <path strokeLinecap="round" strokeLinejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                                                    </svg>
                                                </template>
                                                <template #default>
                                                    <p class="text-lg font-bold mb-4 pl-2">Cost Breakdown</p>
                                                    <table class="w-full">
                                                        <thead>
                                                            <tr class="bg-gray-100">
                                                                <th class="py-2 pl-2">Cost</th>
                                                                <th class="py-2">Amount</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr v-for="(costAmount, costName) in billingReport.cost_breakdown" :key="costName">
                                                                <td class="pl-2">{{ costName }}</td>
                                                                <td>{{ costAmount.amount_with_currency }}</td>
                                                            </tr>
                                                            <tr class="bg-gray-100">
                                                                <td class="py-2 pl-2"><strong>Total</strong></td>
                                                                <td class="py-2"><strong>{{ billingReport.costs.amount_with_currency }}</strong></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </template>
                                            </el-popover>
                                        </div>
                                    </td>

                                    <!-- Sharable Revenue -->
                                    <td class="px-6 py-3 whitespace-nowrap text-md text-gray-500 text-left bg-teal-50">
                                        <span>{{ billingReport.sharable_revenue.amount_with_currency }}</span>
                                    </td>

                                    <!-- Our Share -->
                                    <td class="px-6 py-3 whitespace-nowrap text-md text-gray-500 text-left font-bold bg-teal-50">
                                        <span>{{ billingReport.our_share.amount_with_currency }}</span>
                                    </td>

                                    <!-- Their Share -->
                                    <td class="px-6 py-3 whitespace-nowrap text-md text-gray-500 text-left bg-teal-50">
                                        <span>{{ billingReport.their_share.amount_with_currency }}</span>
                                    </td>

                                    <!-- Total Transactions -->
                                    <td class="px-6 py-3 whitespace-nowrap text-md text-gray-500 text-center bg-teal-50 border-r border-dotted border-r-fuchsia-300">
                                        <span>{{ billingReport.total_transactions }}</span>
                                    </td>

                                    <!-- Overview -->
                                    <td class="px-6 py-3 whitespace-nowrap text-md text-gray-500 text-center bg-fuchsia-50">
                                        <span v-if="billingReport.overview_pdf_path == null">...</span>
                                        <a v-else :href="billingReport.overview_pdf_path" class="mx-auto" download>
                                            <img src="/images/pdf.png" class="w-8 mx-auto"/>
                                        </a>
                                    </td>

                                    <!-- Transactions -->
                                    <td class="px-6 py-3 whitespace-nowrap text-md text-gray-500 text-center bg-fuchsia-50">
                                        <span v-if="billingReport.successful_transactions_csv_path == null">...</span>
                                        <a v-else :href="billingReport.successful_transactions_csv_path" class="mx-auto" download>
                                            <img src="/images/csv.png" class="w-8 mx-auto"/>
                                        </a>
                                    </td>

                                    <!-- Invoice -->
                                    <td class="px-6 py-3 whitespace-nowrap text-md text-gray-500 text-center bg-fuchsia-50 border-r border-dotted border-r-fuchsia-300">
                                        <span v-if="billingReport.invoice_pdf_path == null">...</span>
                                        <a v-else :href="billingReport.invoice_pdf_path" class="mx-auto" download>
                                            <img src="/images/pdf.png" class="w-8 mx-auto"/>
                                        </a>
                                    </td>

                                    <!-- Created Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ billingReport.created_at == null ? '...' : moment(billingReport.created_at).format('lll') }}
                                    </td>

                                </tr>

                                <tr v-if="billingReportsPayload.data.length == 0">

                                    <!-- Content -->
                                    <td :colspan="11" class="px-6 py-3 whitespace-nowrap">
                                        <div class="text-center text-gray-900 text-sm p-6">No billing reports</div>
                                    </td>

                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <pagination class="mt-6" :paginationPayload="billingReportsPayload" :updateData="['billingReportsPayload']" />

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
            projectPayload: Object,
            billingReportsPayload: Object
        },
        data() {
            return {
                moment: moment,
                refreshContentInterval: null,
                form: this.$inertia.form({
                    search: ''
                }),
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
