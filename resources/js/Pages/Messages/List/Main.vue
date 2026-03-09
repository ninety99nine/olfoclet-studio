<template>
    <app-layout title="Messages">
        <div class="min-h-screen bg-slate-50 pb-12">
            <div class="max-w-7xl mx-auto px-6 pt-6 pb-12">
                <ManageMessageModal
                    v-model="isShowingModal"
                    :action="modalAction"
                    :message="selectedMessage"
                    :parent-message="parentMessage"
                    :breadcrumbs="breadcrumbs"
                    :show-header="false"
                    @onDeleted="onModalDeleted"
                />
                <MessagesContent
                    :parent-message="parentMessage"
                    :messages-payload="messagesPayload"
                    :breadcrumbs="breadcrumbs"
                    @open-modal="openModal"
                />
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import MessagesContent from './Partials/Content.vue';
import ManageMessageModal from './Partials/ManageMessageModal.vue';

export default defineComponent({
    components: {
        AppLayout,
        MessagesContent,
        ManageMessageModal,
    },
    props: {
        parentMessage: { type: Object, default: null },
        messagesPayload: { type: Object, required: true },
        breadcrumbs: { type: Array, default: () => [] },
    },
    data() {
        return {
            isShowingModal: false,
            modalAction: null,
            selectedMessage: null,
        };
    },
    methods: {
        openModal(payload) {
            this.modalAction = payload.action;
            this.selectedMessage = payload.message ?? null;
            this.isShowingModal = true;
        },
        onModalDeleted() {
            this.selectedMessage = null;
            this.isShowingModal = false;
        },
    },
});
</script>
