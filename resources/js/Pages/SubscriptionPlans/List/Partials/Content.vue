<template>

    <div>

        <manage-subscription-plan-modal v-model="isShowingModal" :action="modalAction" :subscriptionPlan="subscriptionPlan" :parentSubscriptionPlan="parentSubscriptionPlan" :autoBillingReminders="autoBillingReminders" :breadcrumbs="breadcrumbs" />

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
                                <tr v-for="subscriptionPlan in subscriptionPlansPayload.data" :key="subscriptionPlan.id">
                                    <!-- Name -->
                                    <td class="px-6 py-3 whitespace-nowrap text-left">
                                        <div class="text-sm text-gray-900">{{ subscriptionPlan.name }}</div>
                                    </td>
                                    <!-- Description -->
                                    <td class="px-6 py-3 whitespace-nowrap text-left">
                                        {{ subscriptionPlan.description == null ? '...' : subscriptionPlan.description }}
                                    </td>
                                    <!-- Folder -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                        <FolderStatusBadge :subscriptionPlan="subscriptionPlan"></FolderStatusBadge>
                                    </td>
                                    <!-- Active -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                        <ActiveStatusBadge :subscriptionPlan="subscriptionPlan"></ActiveStatusBadge>
                                    </td>
                                    <!-- Duration -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                        {{ subscriptionPlan.duration_in_words == null ? '...' : subscriptionPlan.duration_in_words }}
                                    </td>
                                    <!-- Price -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                        {{ subscriptionPlan.price == null ? '...' : subscriptionPlan.price.amount_with_currency }}
                                    </td>
                                    <!-- Total Subscriptions -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                        {{ subscriptionPlan.subscriptions_count == null ? '...' : subscriptionPlan.subscriptions_count }}
                                    </td>
                                    <!-- Popularity -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center">
                                        <span class="text-lg text-green-600">{{ getPercentageOfCoverage(subscriptionPlan.subscriptions_count) }}%</span>
                                    </td>
                                    <!-- Created Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ subscriptionPlan.created_at == null ? '...' : moment(subscriptionPlan.created_at).fromNow() }}
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <a v-if="$inertia.page.props.projectPermissions.includes('View subscription plans') && subscriptionPlan.is_folder" href="#" @click.prevent="$inertia.get(route('show.subscription.plan', { project: route().params.project, subscription_plan: subscriptionPlan.id }))" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                        <a v-if="$inertia.page.props.projectPermissions.includes('Manage subscription plans')" href="#" @click.prevent="showModal(subscriptionPlan, 'update')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <a v-if="$inertia.page.props.projectPermissions.includes('Manage subscription plans')" href="#" @click.prevent="showModal(subscriptionPlan, 'delete')" class="text-red-600 hover:text-red-900">Delete</a>
                                    </td>
                                </tr>

                                <tr v-if="subscriptionPlansPayload.data.length == 0">
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

            <!-- Pagination Links -->
            <pagination class="mt-6" :paginationPayload="subscriptionPlansPayload" :updateData="['subscriptionPlansPayload']" />

        </div>

    </div>

</template>
<script>

    import moment from "moment";
    import { defineComponent } from 'vue';
    import ActiveStatusBadge from './ActiveStatusBadge.vue';
    import FolderStatusBadge from './FolderStatusBadge.vue';
    import Pagination from '../../../../Partials/Pagination.vue';
    import ManageSubscriptionPlanModal from './ManageSubscriptionPlanModal.vue';

    export default defineComponent({
        components: {
            ManageSubscriptionPlanModal, Pagination, ActiveStatusBadge, FolderStatusBadge
        },
        props: {
            breadcrumbs: Array,
            totalSubscriptions: Number,
            autoBillingReminders: Array,
            parentSubscriptionPlan: Object,
            subscriptionPlansPayload: Object,
        },
        data() {
            return {
                moment: moment,
                modalAction: null,
                isShowingModal: false,
                subscriptionPlan: null
            }
        },
        methods: {
            getPercentageOfCoverage(subscriptionsCount){
                if( this.totalSubscriptions > 0 ){
                    return Math.round((subscriptionsCount / this.totalSubscriptions) * 100)
                }

                return 0;
            },
            showModal(subscriptionPlan, action){
                this.subscriptionPlan = subscriptionPlan;
                this.modalAction = action;
                this.isShowingModal = true;
            }
        }
    })
</script>
