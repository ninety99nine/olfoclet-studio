<template>
    <app-layout title="Subscriptions">
        <div class="min-h-screen bg-slate-50 pb-12">
            <div class="max-w-7xl mx-auto px-6 pt-6 pb-12">
                <SubscriptionsContent
                    :total-subscribers="totalSubscribers"
                    :pricing-plans="pricingPlans"
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
import SubscriptionsContent from './Partials/Content.vue';

export default defineComponent({
    components: {
        AppLayout,
        SubscriptionsContent,
    },
    props: {
        totalSubscribers: { type: Number, default: 0 },
        pricingPlans: { type: Array, default: () => [] },
        projectPermissions: { type: Array, default: () => [] },
    },
    data() {
        return { refetchKey: 0 };
    },
});
</script>
