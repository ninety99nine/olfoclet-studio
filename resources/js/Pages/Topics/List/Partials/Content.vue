<template>

    <div>

        <manage-topic-modal
            v-model="isShowingModal" :action="modalAction"
            :topic="topic" :parentTopic="parentTopic"
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
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span>Title</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span>Content</span>
                                </th>
                                <th scope="col" class="px-6 py-3 whitespace-nowrap text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <span>Actions</span>
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="topic in topicsPayload.data" :key="topic.id">
                                    <!-- Title -->
                                    <td class="px-6 py-3">
                                        <div class="text-sm text-gray-900">{{ topic.title }}</div>
                                    </td>
                                    <!-- Content -->
                                    <td class="px-6 py-3">
                                        <div class="text-sm text-gray-900">{{ topic.content }}</div>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-right text-sm font-medium">
                                        <a v-if="$inertia.page.props.projectPermissions.includes('View topics')" href="#" @click.prevent="$inertia.get(route('show.topic', { project: route().params.project, topic: topic.id }))" class="text-indigo-600 hover:text-indigo-900 mr-3">View</a>
                                        <a v-if="$inertia.page.props.projectPermissions.includes('Manage topics')" href="#" @click.prevent="showModal(topic, 'update')" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                        <a v-if="$inertia.page.props.projectPermissions.includes('Manage topics')" href="#" @click.prevent="showModal(topic, 'delete')" class="text-red-600 hover:text-red-900">Delete</a>
                                    </td>
                                </tr>

                                <tr v-if="topicsPayload.data.length == 0">
                                    <!-- Content -->
                                    <td :colspan="3" class="px-6 py-3 whitespace-nowrap">
                                        <div class="text-center text-gray-900 text-sm p-6">No topics</div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination Links -->
            <pagination class="mt-6" :paginationPayload="topicsPayload" :updateData="['topicsPayload']" />

        </div>

    </div>

</template>
<script>

    import Pagination from '../../../../Partials/Pagination.vue'
    import ManageTopicModal from './ManageTopicModal.vue'
    import { defineComponent } from 'vue'

    export default defineComponent({
        components: {
            ManageTopicModal, Pagination
        },
        props: {
            parentTopic: Object,
            topicsPayload: Object,
            breadcrumbs: Array
        },
        data() {
            return {
                isShowingModal: false,
                modalAction: null,
                topic: null
            }
        },
        methods: {
            onDeleted() {
                this.topic = null;
            },
            showModal(topic, action){
                this.topic = topic;
                this.modalAction = action;
                this.isShowingModal = true
            }
        }
    })
</script>
