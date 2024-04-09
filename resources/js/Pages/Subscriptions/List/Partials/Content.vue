<template>

    <div>

        <ManageSubscriptionModal
            v-model="isShowingModal" :action="modalAction" :subscription="subscription"
            :subscriptionPlans="subscriptionPlans"
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
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-dotted border-r-teal-300">

                                    </th>
                                    <th scope="col" colspan="5" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50 border-r border-dotted border-r-fuchsia-300">
                                        <div class="font-bold text-teal-500">SUBSCRIPTION</div>
                                    </th>
                                    <th scope="col" colspan="6" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <div class="font-bold text-fuchsia-500">SUBSCRIPTION PLAN</div>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-dotted border-r-teal-300">

                                    </th>
                                </tr>

                                <tr>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider border-r border-dotted border-r-teal-300">
                                        <span>Mobile</span>
                                    </th>


                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Start</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>End</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Cancelled</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50">
                                        <span>Active</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-50 border-r border-dotted border-r-fuchsia-300">
                                        <span>Created</span>
                                    </th>

                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Name</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Description</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Active</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Duration</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Price</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-fuchsia-50">
                                        <span>Created</span>
                                    </th>

                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-xs font-medium text-gray-500 uppercase tracking-wider text-right">
                                        <span>Actions</span>
                                    </th>

                                </tr>

                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">

                                <tr v-for="subscription in subscriptionsPayload.data" :key="subscription.id">

                                    <!-- Mobile -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 border-r border-dotted border-r-teal-300">
                                        <div class="text-sm text-gray-900">{{ subscription.subscriber.msisdn }}</div>
                                    </td>




                                    <!-- Subscription Start Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-teal-50">
                                        {{ moment(subscription.start_at).format('lll') }}
                                    </td>
                                    <!-- Subscription End Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-teal-50">
                                        {{ moment(subscription.end_at).format('lll') }}
                                    </td>
                                    <!-- Subscription Cancelled Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-teal-50">
                                        {{ subscription.cancelled_at == null ? '...' : moment(subscription.cancelled_at).format('lll') }}
                                    </td>
                                    <!-- Subscription Status -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-teal-50">
                                        <SubscriptionActiveStatusBadge :subscription="subscription"></SubscriptionActiveStatusBadge>
                                    </td>
                                    <!-- Subscription Created Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-teal-50 border-r border-dotted border-r-fuchsia-300">
                                        {{ moment(subscription.created_at).format('lll') }}
                                    </td>




                                    <!-- Subscription Plan Name -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-fuchsia-50">
                                        <div class="text-sm text-gray-900">{{ subscription.subscription_plan.name }}</div>
                                    </td>
                                    <!-- Subscription Plan Description -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-fuchsia-50">
                                        {{ subscription.subscription_plan.description == null ? '...' : subscription.subscription_plan.description }}
                                    </td>
                                    <!-- Subscription Plan Active -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50">
                                        <SubscriptionPlanActiveStatusBadge :subscriptionPlan="subscription.subscription_plan"></SubscriptionPlanActiveStatusBadge>
                                    </td>
                                    <!-- Subscription Plan Duration -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50">
                                        {{ subscription.subscription_plan.duration_in_words == null ? '...' : subscription.subscription_plan.duration_in_words }}
                                    </td>
                                    <!-- Subscription Plan Price -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center bg-fuchsia-50">
                                        {{ subscription.subscription_plan.price == null ? '...' : subscription.subscription_plan.price.amount_with_currency }}
                                    </td>
                                    <!-- Subscription Plan Created Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-left bg-fuchsia-50">
                                        {{ subscription.subscription_plan.created_at == null ? '...' : moment(subscription.subscription_plan.created_at).format('lll') }}
                                    </td>




                                    <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <a v-if="$inertia.page.props.projectPermissions.includes('Manage subscriptions')" href="#" @click.prevent="showModal(subscription, 'update')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <a v-if="$inertia.page.props.projectPermissions.includes('Manage subscriptions')" href="#" @click.prevent="showModal(subscription, 'delete')" class="text-red-600 hover:text-red-900">Delete</a>
                                    </td>

                                </tr>

                                <tr v-if="subscriptionsPayload.data.length == 0">

                                    <!-- Content -->
                                    <td :colspan="13" class="px-6 py-3 whitespace-nowrap">
                                        <div class="text-center text-gray-900 text-sm p-6">No subscriptions</div>
                                    </td>

                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <pagination class="mt-6" :paginationPayload="subscriptionsPayload" :updateData="['subscriptionsPayload']" />

        </div>

    </div>

</template>
<script>

    import SubscriptionPlanActiveStatusBadge from './../../../SubscriptionPlans/List/Partials/ActiveStatusBadge.vue';
    import SubscriptionActiveStatusBadge from './ActiveStatusBadge.vue';
    import ManageSubscriptionModal from './ManageSubscriptionModal.vue';
    import Pagination from '../../../../Partials/Pagination.vue';
    import { defineComponent } from 'vue';
    import moment from "moment";

    export default defineComponent({
        components: {
            ManageSubscriptionModal, SubscriptionActiveStatusBadge, SubscriptionPlanActiveStatusBadge, Pagination
        },
        props: {
            totalSubscribers: Number,
            subscriptionPlans: Array,
            subscriptionsPayload: Object
        },
        data() {
            return {
                refreshContentInterval: null,
                isShowingModal: false,
                modalAction: null,
                subscription: null,
                moment: moment
            }
        },
        methods: {
            onDeleted() {
                this.subscription = null;
            },
            refreshContent()
            {
                this.$inertia.reload();
            },
            showModal(subscription, action){
                this.subscription = subscription;
                this.modalAction = action;
                this.isShowingModal = true
            },
            getPercentageOfCoverage(subscribersCount){
                if( this.totalSubscribers > 0 ){
                    return Math.round((subscribersCount / this.totalSubscribers) * 100)
                }

                return 0;
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
