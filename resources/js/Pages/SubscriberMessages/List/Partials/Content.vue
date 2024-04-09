<template>

    <div>

        <div class="grid grid-cols-12 gap-4">

            <div class="col-span-6">
                <div class="bg-gray-50 border-b px-6 py-4 rounded-t text-gray-500 text-sm mb-4">
                    <div class="text-2xl font-semibold leading-6 text-gray-500">Subscriber Messages</div>
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
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <span>Mobile</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-100">
                                        <span>Content</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-100">
                                        <span>Type</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-100">
                                        <span>Status</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider bg-teal-100">
                                        <span>Delivery Status</span>
                                    </th>
                                    <th scope="col" class="px-6 py-3 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <span>Created</span>
                                    </th>
                                </tr>

                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">

                                <tr v-for="subscriberMessage in subscriberMessagesPayload.data" :key="subscriberMessage.id">

                                    <!-- Mobile -->
                                    <td class="px-6 py-3 whitespace-nowrap align-top">
                                        <div class="text-sm text-gray-900">{{ subscriberMessage.subscriber.msisdn }}</div>
                                    </td>

                                    <!-- Content -->
                                    <td class="px-6 py-3 text-sm text-gray-500 text-justify align-top bg-teal-50">
                                        <div class="w-96">{{ subscriberMessage.content }}</div>
                                    </td>

                                    <!-- Type -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center align-top bg-teal-50">
                                        <span>{{ subscriberMessage.type }}</span>
                                    </td>

                                    <!-- Status -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center align-top bg-teal-50">
                                        <SubscriberMessageStatusBadge :subscriberMessage="subscriberMessage"></SubscriberMessageStatusBadge>
                                    </td>

                                    <!-- Delivery Status -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 text-center align-top bg-teal-50">
                                        <span v-if="subscriberMessage.delivery_status == null">...</span>
                                        <SubscriberMessageDeliveryStatusBadge v-else :subscriberMessage="subscriberMessage"></SubscriberMessageDeliveryStatusBadge>
                                    </td>

                                    <!-- Created Date -->
                                    <td class="px-6 py-3 whitespace-nowrap text-sm text-gray-500 align-top">
                                        {{ subscriberMessage.created_at == null ? '...' : moment(subscriberMessage.created_at).format('lll') }}
                                    </td>

                                </tr>

                                <tr v-if="subscriberMessagesPayload.data.length == 0">

                                    <!-- Content -->
                                    <td :colspan="8" class="px-6 py-3 whitespace-nowrap">
                                        <div class="text-center text-gray-900 text-sm p-6">No subscriber messages</div>
                                    </td>

                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <pagination class="mt-6" :paginationPayload="subscriberMessagesPayload" :updateData="['subscriberMessagesPayload']" />

        </div>

    </div>

</template>
<script>

    import SubscriberMessageDeliveryStatusBadge from './SubscriberMessageDeliveryStatusBadge.vue';
    import SubscriberMessageStatusBadge from './SubscriberMessageStatusBadge.vue';
    import Pagination from '../../../../Partials/Pagination.vue';
    import { defineComponent } from 'vue';
    import moment from "moment";

    export default defineComponent({
        components: {
            Pagination, SubscriberMessageDeliveryStatusBadge, SubscriberMessageStatusBadge
        },
        props: {
            subscriberMessagesPayload: Object
        },
        data() {
            return {
                refreshContentInterval: null,
                moment: moment
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
