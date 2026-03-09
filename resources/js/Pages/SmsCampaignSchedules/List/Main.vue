<template>
    <app-layout title="Sms Schedules">
        <div class="min-h-screen bg-slate-50 pb-12">
            <div class="max-w-7xl mx-auto px-6 pt-4 pb-12">
                <SmsSchedulesContent :sms-campaign-schedules-payload="smsCampaignSchedulesPayload" />
            </div>
        </div>
    </app-layout>
</template>

<script>
import { defineComponent } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import SmsSchedulesContent from './Partials/Content.vue';

export default defineComponent({
    components: {
        AppLayout,
        SmsSchedulesContent,
    },
    props: {
        smsCampaignSchedulesPayload: { type: Object, required: true },
    },
});
</script>
