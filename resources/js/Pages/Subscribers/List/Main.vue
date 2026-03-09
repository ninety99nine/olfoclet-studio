<template>
    <app-layout title="Subscribers">
        <div class="min-h-screen bg-slate-50 pb-12">
            <div class="max-w-7xl mx-auto px-6 pt-4 pb-12">
                <SubscribersContent
                    :total-messages="totalMessages"
                    :project-permissions="projectPermissions"
                    :refetch-trigger="refetchKey"
                />
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import SubscribersContent from './Partials/Content.vue';

export default defineComponent({
    components: {
        AppLayout,
        SubscribersContent,
    },
    props: {
        totalMessages: { type: Number, required: true },
        projectPermissions: { type: Array, default: () => [] },
    },
    data() {
        return { refetchKey: 0 };
    },
});
</script>
