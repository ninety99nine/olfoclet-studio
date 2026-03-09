<template>
    <app-layout title="Users">
        <div class="min-h-screen bg-slate-50 pb-12">
            <div class="max-w-7xl mx-auto px-6 pt-6 pb-12">
                <UsersContent
                    :project="project"
                    :available-permissions="availablePermissions"
                    :users-payload="usersPayload"
                />
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import UsersContent from './Partials/Content.vue';

export default defineComponent({
    components: {
        AppLayout,
        UsersContent,
    },
    props: {
        availablePermissions: { type: Array, default: () => [] },
        project: { type: Object, default: null },
        usersPayload: { type: Object, required: true },
    },
});
</script>
