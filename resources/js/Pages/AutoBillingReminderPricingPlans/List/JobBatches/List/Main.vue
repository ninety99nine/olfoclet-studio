<template>
    <app-layout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <pricing-plans-content :pricingPlanAutoBillingReminderJobBatchesPayload="pricingPlanAutoBillingReminderJobBatchesPayload" :pricingPlan="pricingPlan" />

            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import PricingPlansContent from './Partials/Content.vue'

    export default defineComponent({
        components: {
            AppLayout,
            PricingPlansContent
        },
        props: {
            pricingPlan: Object,
            pricingPlanAutoBillingReminderJobBatchesPayload: Object
        }
    })
</script>
