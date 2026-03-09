<template>
    <app-layout title="Auto Billing Schedules">
        <div class="min-h-screen bg-slate-50 pb-12">
            <div class="max-w-7xl mx-auto px-6 pt-6 pb-12">
                <BillingSchedulesContent
                    :auto-billing-schedules-payload="autoBillingSchedulesPayload"
                    :auto-billing-progress="autoBillingProgress"
                />
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent, onMounted } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import BillingSchedulesContent from './Partials/Content.vue';

export default defineComponent({
    components: {
        AppLayout,
        BillingSchedulesContent,
    },
    props: {
        deferredSchedules: { type: Boolean, default: false },
        autoBillingSchedulesPayload: { type: Object, default: () => ({ data: [], current_page: 1, last_page: 1, total: 0, links: [] }) },
        autoBillingProgress: { type: Object, default: () => ({ total_due: 0, processed: 0, total_in_batches: 0 }) },
    },
    setup(props) {
        onMounted(() => {
            if (props.deferredSchedules) {
                router.reload({
                    only: ['autoBillingSchedulesPayload', 'autoBillingProgress'],
                });
            }
        });
    },
});
</script>
