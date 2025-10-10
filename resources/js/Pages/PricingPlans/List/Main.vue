<template>
    <app-layout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <manage-pricing-plan-modal :parentPricingPlan="parentPricingPlan" :pricingPlansPayload="pricingPlansPayload" :autoBillingReminders="autoBillingReminders" :breadcrumbs="breadcrumbs" :showHeader="true" />

                <pricing-plans-content :parentPricingPlan="parentPricingPlan" :pricingPlansPayload="pricingPlansPayload" :totalSubscriptions="totalSubscriptions" :autoBillingReminders="autoBillingReminders" :breadcrumbs="breadcrumbs" />

            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import PricingPlansContent from './Partials/Content.vue'
    import ManagePricingPlanModal from './Partials/ManagePricingPlanModal.vue'

    export default defineComponent({
        components: {
            AppLayout,
            PricingPlansContent,
            ManagePricingPlanModal
        },
        props: {
            breadcrumbs: Array,
            totalSubscriptions: Number,
            autoBillingReminders: Array,
            parentPricingPlan: Object,
            pricingPlansPayload: Object,
        }
    })
</script>
