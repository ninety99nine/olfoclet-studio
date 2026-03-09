<template>
    <app-layout title="Pricing Plans">
        <div class="min-h-screen bg-slate-50/50 pb-12">
            <div class="max-w-[1600px] mx-auto px-4 lg:px-8 pt-6 pb-12">
                <PricingPlansContent
                    :parent-pricing-plan="parentPricingPlan"
                    :pricing-plans-payload="pricingPlansPayload"
                    :total-subscriptions="totalSubscriptions"
                    :auto-billing-reminders="autoBillingReminders"
                    :breadcrumbs="breadcrumbs"
                />
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import PricingPlansContent from './Partials/Content.vue';

export default defineComponent({
    components: {
        AppLayout,
        PricingPlansContent,
    },
    props: {
        breadcrumbs: { type: Array, default: () => [] },
        totalSubscriptions: { type: Number, default: 0 },
        autoBillingReminders: { type: Array, default: () => [] },
        parentPricingPlan: { type: Object, default: null },
        pricingPlansPayload: { type: Object, default: () => ({ data: [], total: 0, links: [] }) },
    },
});
</script>
