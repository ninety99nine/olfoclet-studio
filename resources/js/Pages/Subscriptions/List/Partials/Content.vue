<template>

    <div>

        <create-subscription-modal v-model="isShowingModal" :action="modalAction" :subscription="subscription" :subscriptionPlans="subscriptionPlans" />

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
                                <span>Mobile</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Start</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>End</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Cancelled</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Plan</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <span>Status</span>
                            </th>
                            <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
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
                                <td class="px-6 py-3 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ subscription.subscriber ? subscription.subscriber.msisdn : '...' }}</div>
                                </td>
                                <!-- Start Date -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ subscription.created_at == null ? '...' : moment(subscription.start_at).format('lll') }}
                                </td>
                                <!-- End Date -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ subscription.created_at == null ? '...' : moment(subscription.end_at).format('lll') }}
                                </td>
                                <!-- Cancelled Date -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ subscription.cancelled_at == null ? '...' : moment(subscription.cancelled_at).format('lll') }}
                                </td>
                                <!-- Plan -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ subscription.subscription_plan == null ? '...' : subscription.subscription_plan.name }}
                                </td>
                                <!-- Status -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                                    <span :class="subscription.status == 'Active' ? 'text-green-600' : 'text-gray-600'">{{ subscription.status }}</span>
                                </td>
                                <!-- Created Date -->
                                <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500">
                                    {{ subscription.created_at == null ? '...' : moment(subscription.created_at).fromNow() }}
                                </td>
                                <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                    <a v-if="$inertia.page.props.projectPermissions.includes('Manage subscriptions')" href="#" @click.prevent="showModal(subscription, 'update')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                    <a v-if="$inertia.page.props.projectPermissions.includes('Manage subscriptions')" href="#" @click.prevent="showModal(subscription, 'delete')" class="text-red-600 hover:text-red-900">Delete</a>
                                </td>
                            </tr>

                            <tr v-if="subscriptionsPayload.data.length == 0">
                                <!-- Content -->
                                <td :colspan="8" class="px-6 py-3 whitespace-nowrap">
                                    <div class="text-center text-gray-900 text-sm p-6">No subscriptions</div>
                                </td>
                            </tr>


                        </tbody>
                    </table>
                    </div>
                </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <pagination class="mt-6" :paginationPayload="subscriptionsPayload" :updateData="['subscriptionsPayload']" />

        </div>

    </div>

</template>
<script>

    import CreateSubscriptionModal from './ManageSubscriptionModal.vue'
    import Pagination from '../../../../Partials/Pagination.vue'
    import { defineComponent } from 'vue'
    import moment from "moment";

    export default defineComponent({
        components: {
            CreateSubscriptionModal, Pagination
        },
        props: {
            subscriptionPlans: Array,
            totalSubscribers: Number,
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
