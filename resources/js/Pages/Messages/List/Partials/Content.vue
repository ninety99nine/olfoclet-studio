<template>

    <div>

        <manage-message-modal
            v-model="isShowingModal" :action="modalAction" :message="message"
            :parentMessage="parentMessage" :breadcrumbs="breadcrumbs"
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
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span>Content</span>
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span>Actions</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="message in messagesPayload.data" :key="message.id">
                                    <!-- Content -->
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">{{ message.content }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a v-if="$inertia.page.props.projectPermissions.includes('View messages')" href="#" @click.prevent="$inertia.get(route('show.message', { project: route().params.project, message: message.id }))" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                        <a v-if="$inertia.page.props.projectPermissions.includes('Manage messages')" href="#" @click.prevent="showModal(message, 'update')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <a v-if="$inertia.page.props.projectPermissions.includes('Manage messages')" href="#" @click.prevent="showModal(message, 'delete')" class="text-red-600 hover:text-red-900">Delete</a>
                                    </td>
                                </tr>

                                <tr v-if="messagesPayload.data.length == 0">
                                    <!-- Content -->
                                    <td :colspan="7" class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-center text-gray-900 text-sm p-6">No messages</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <pagination class="mt-6" :paginationPayload="messagesPayload" :updateData="['messagesPayload']" />

        </div>

    </div>

</template>
<script>

    import Pagination from '../../../../Partials/Pagination.vue'
    import ManageMessageModal from './ManageMessageModal.vue'
    import { defineComponent } from 'vue'

    export default defineComponent({
        components: {
            ManageMessageModal, Pagination
        },
        props: {
            parentMessage: Object,
            messagesPayload: Object,
            breadcrumbs: Array
        },
        data() {
            return {
                isShowingModal: false,
                modalAction: null,
                message: null
            }
        },
        methods: {
            onDeleted() {
                this.message = null;
            },
            showModal(message, action){
                this.message = message;
                this.modalAction = action;
                this.isShowingModal = true
            }
        }
    })
</script>
