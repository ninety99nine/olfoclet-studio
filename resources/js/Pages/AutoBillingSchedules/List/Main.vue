<template>
    <app-layout title="Auto Billing Schedules">
        <div class="min-h-screen bg-slate-50 pb-12">
            <div class="max-w-7xl mx-auto px-6 pt-6 pb-12">
                <BillingSchedulesContent :auto-billing-schedules-payload="autoBillingSchedulesPayload" />
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import BillingSchedulesContent from './Partials/Content.vue';

export default defineComponent({
    components: {
        AppLayout,
        BillingSchedulesContent,
    },
    props: {
        autoBillingSchedulesPayload: { type: Object, default: () => ({ data: [], current_page: 1, last_page: 1, total: 0, links: [] }) },
    },
});
</script>
