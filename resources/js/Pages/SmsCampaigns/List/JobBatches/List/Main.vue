<template>
    <app-layout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <sms-campaigns-content :smsCampaignBatchJobsPayload="smsCampaignBatchJobsPayload" :smsCampaign="smsCampaign" />

            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import SmsCampaignsContent from './Partials/Content.vue'

    export default defineComponent({
        components: {
            AppLayout,
            SmsCampaignsContent
        },
        props: {
            smsCampaign: Object,
            smsCampaignBatchJobsPayload: Object
        }
    })
</script>
