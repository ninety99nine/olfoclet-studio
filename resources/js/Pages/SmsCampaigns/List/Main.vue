<template>
    <app-layout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <manage-sms-campaign-modal :showAddbutton="true" :subscriptionPlans="subscriptionPlans" :contentToSendOptions="contentToSendOptions" :scheduleTypeOptions="scheduleTypeOptions" />

                <sms-campaigns-content :projectPayload="projectPayload" :smsCampaignsPayload="smsCampaignsPayload" :subscriptionPlans="subscriptionPlans" :contentToSendOptions="contentToSendOptions" :scheduleTypeOptions="scheduleTypeOptions" />

            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import SmsCampaignsContent from './Partials/Content.vue'
    import ManageSmsCampaignModal from './Partials/ManageSmsCampaignModal.vue'

    export default defineComponent({
        components: {
            AppLayout,
            SmsCampaignsContent,
            ManageSmsCampaignModal
        },
        props: {
            contentToSendOptions: Array,
            scheduleTypeOptions: Array,
            subscriptionPlans: Array,
            smsCampaignsPayload: Object,
            projectPayload: Object
        }
    })
</script>
