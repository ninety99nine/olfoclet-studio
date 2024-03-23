<template>
    <app-layout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <manage-subscription-plan-modal :parentSubscriptionPlan="parentSubscriptionPlan" :subscriptionPlansPayload="subscriptionPlansPayload" :autoBillingReminders="autoBillingReminders" :breadcrumbs="breadcrumbs" :showHeader="true" />

                <subscription-plans-content :parentSubscriptionPlan="parentSubscriptionPlan" :subscriptionPlansPayload="subscriptionPlansPayload" :totalSubscriptions="totalSubscriptions" :autoBillingReminders="autoBillingReminders" :breadcrumbs="breadcrumbs" />

            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import SubscriptionPlansContent from './Partials/Content.vue'
    import ManageSubscriptionPlanModal from './Partials/ManageSubscriptionPlanModal.vue'

    export default defineComponent({
        components: {
            AppLayout,
            SubscriptionPlansContent,
            ManageSubscriptionPlanModal
        },
        props: {
            breadcrumbs: Array,
            totalSubscriptions: Number,
            autoBillingReminders: Array,
            parentSubscriptionPlan: Object,
            subscriptionPlansPayload: Object,
        }
    })
</script>
