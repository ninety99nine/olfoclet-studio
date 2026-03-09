<template>
    <app-layout title="Topics">
        <div class="min-h-screen bg-slate-50 pb-12">
            <div class="max-w-7xl mx-auto px-6 pt-6 pb-12">
                <TopicsContent
                    :parent-topic="parentTopic"
                    :topics-payload="topicsPayload"
                    :breadcrumbs="breadcrumbs"
                    :project-permissions="projectPermissions"
                />
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import TopicsContent from './Partials/Content.vue';

export default defineComponent({
    components: {
        AppLayout,
        TopicsContent,
    },
    props: {
        parentTopic: { type: Object, default: null },
        topicsPayload: { type: Object, required: true },
        breadcrumbs: { type: Array, default: () => [] },
        projectPermissions: { type: Array, default: () => [] },
    },
});
</script>
