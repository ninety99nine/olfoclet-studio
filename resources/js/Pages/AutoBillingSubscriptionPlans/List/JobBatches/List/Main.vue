<template>
    <app-layout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <subscription-plans-content :autoBillingSubscriptionPlanJobBatchesPayload="autoBillingSubscriptionPlanJobBatchesPayload" :subscriptionPlan="subscriptionPlan" />

            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import SubscriptionPlansContent from './Partials/Content.vue'

    export default defineComponent({
        components: {
            AppLayout,
            SubscriptionPlansContent
        },
        props: {
            subscriptionPlan: Object,
            autoBillingSubscriptionPlanJobBatchesPayload: Object
        }
    })
</script>
