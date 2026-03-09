<template>
    <app-layout title="Billing Reports">
        <div class="min-h-screen bg-slate-50 pb-12">
            <div class="max-w-7xl mx-auto px-6 pt-6 pb-12">
                <BillingReportsContent
                    :project-payload="projectPayload"
                    :refetch-trigger="refetchKey"
                />
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import BillingReportsContent from './Partials/Content.vue';

export default defineComponent({
    components: {
        AppLayout,
        BillingReportsContent,
    },
    props: {
        projectPayload: { type: Object, default: null },
    },
    data() {
        return { refetchKey: 0 };
    },
});
</script>
