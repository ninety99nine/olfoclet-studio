<template>
    <app-layout title="SMS Campaigns">
        <div class="min-h-screen bg-slate-50 pb-12">
            <div class="max-w-7xl mx-auto px-6 pt-6 pb-12">
                <SmsCampaignsContent
                    :project-payload="projectPayload"
                    :sms-campaigns-payload="smsCampaignsPayload"
                    :content-to-send-options="contentToSendOptions"
                    :schedule-type-options="scheduleTypeOptions"
                />
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
            SmsCampaignsContent,
        },
        props: {
            contentToSendOptions: Array,
            scheduleTypeOptions: Array,
            smsCampaignsPayload: Object,
            projectPayload: Object
        }
    })
</script>
