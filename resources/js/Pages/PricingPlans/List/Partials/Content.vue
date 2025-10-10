<template>

    <div>

        <manage-pricing-plan-modal
            v-model="isShowingModal"
            :action="modalAction" :pricingPlan="pricingPlan"
            :parentPricingPlan="parentPricingPlan"
            :autoBillingReminders="autoBillingReminders"
            :breadcrumbs="breadcrumbs"
            @onDeleted="onDeleted"
        />

        <div class="bg-white shadow-xl sm:rounded-lg">

            <!-- Table -->
            <div class="flex flex-col overflow-y-auto">
                <div class="align-middle inline-block min-w-full">
                    <div class="shadow border-b border-gray-200">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-left">
                                    <span>Name</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-left">
                                    <span>Description</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                    <span>Type</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                    <span>Active</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                    <span>Tags</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                    <span>Duration</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                    <span>Price</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                    <span>Subscriptions</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-center">
                                    <span>Popularity</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span>Created</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-right">
                                    <span>Actions</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="pricingPlan in pricingPlansPayload.data" :key="pricingPlan.id">
                                    <!-- Name -->
                                    <td class="px-6 py-3 whitespace-nowrap text-left">
                                        <div class="text-sm text-gray-900">{{ pricingPlan.name }}</div>
                                    </td>
                                    <!-- Description -->
                                    <td class="px-6 py-3 whitespace-nowrap text-left">
                                        {{ pricingPlan.description == null ? '...' : pricingPlan.description }}
                                    </td>
                                    <!-- Folder -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                        <FolderStatusBadge :pricingPlan="pricingPlan"></FolderStatusBadge>
                                    </td>
                                    <!-- Active -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                        <ActiveStatusBadge :pricingPlan="pricingPlan"></ActiveStatusBadge>
                                    </td>
                                    <!-- Tags -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                        <div v-if="pricingPlan.tags.length == 0">...</div>
                                        <div class="flex space-x-2">
                                            <div v-for="(tag, index) in pricingPlan.tags" :key="index" class="bg-blue-50 text-blue-500 border border-blue-300 py-1 px-2 text-xs rounded">
                                                {{ tag }}
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Duration -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                        {{ pricingPlan.duration_in_words == null ? '...' : pricingPlan.duration_in_words }}
                                    </td>
                                    <!-- Price -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                        {{ pricingPlan.price == null ? '...' : pricingPlan.price.amount_with_currency }}
                                    </td>
                                    <!-- Total Subscriptions -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                        {{ pricingPlan.subscriptions_count == null ? '...' : pricingPlan.subscriptions_count }}
                                    </td>
                                    <!-- Popularity -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                        <span class="text-lg text-green-600">{{ getPercentageOfCoverage(pricingPlan.subscriptions_count) }}%</span>
                                    </td>
                                    <!-- Created Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ pricingPlan.created_at == null ? '...' : moment(pricingPlan.created_at).fromNow() }}
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <a v-if="$inertia.page.props.projectPermissions.includes('View pricing plans') && pricingPlan.is_folder" href="#" @click.prevent="$inertia.get(route('show.pricing.plan', { project: route().params.project, pricing_plan: pricingPlan.id }))" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                        <a v-if="$inertia.page.props.projectPermissions.includes('Manage pricing plans')" href="#" @click.prevent="showModal(pricingPlan, 'update')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <a v-if="$inertia.page.props.projectPermissions.includes('Manage pricing plans')" href="#" @click.prevent="showModal(pricingPlan, 'delete')" class="text-red-600 hover:text-red-900">Delete</a>
                                    </td>
                                </tr>

                                <tr v-if="pricingPlansPayload.data.length == 0">
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
            <pagination class="mt-6" :paginationPayload="pricingPlansPayload" :updateData="['pricingPlansPayload']" />

        </div>

    </div>

</template>
<script>

    import moment from "moment";
    import { defineComponent } from 'vue';
    import ActiveStatusBadge from './ActiveStatusBadge.vue';
    import FolderStatusBadge from './FolderStatusBadge.vue';
    import Pagination from '../../../../Partials/Pagination.vue';
    import ManagePricingPlanModal from './ManagePricingPlanModal.vue';

    export default defineComponent({
        components: {
            ManagePricingPlanModal, Pagination, ActiveStatusBadge, FolderStatusBadge
        },
        props: {
            breadcrumbs: Array,
            totalSubscriptions: Number,
            autoBillingReminders: Array,
            parentPricingPlan: Object,
            pricingPlansPayload: Object,
        },
        data() {
            return {
                moment: moment,
                modalAction: null,
                isShowingModal: false,
                pricingPlan: null
            }
        },
        methods: {
            onDeleted() {
                this.pricingPlan = null;
            },
            getPercentageOfCoverage(subscriptionsCount) {
                if( this.totalSubscriptions > 0 ){
                    return Math.round((subscriptionsCount / this.totalSubscriptions) * 100)
                }

                return 0;
            },
            showModal(pricingPlan, action){
                this.pricingPlan = pricingPlan;
                this.modalAction = action;
                this.isShowingModal = true;
            }
        }
    })
</script>
