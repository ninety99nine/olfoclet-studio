<template>
    <app-layout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <SmsCampaignSchedulesContent :smsCampaignSchedulesPayload="smsCampaignSchedulesPayload" />

            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import SmsCampaignSchedulesContent from './Partials/Content.vue'

    export default defineComponent({
        components: {
            AppLayout,
            SmsCampaignSchedulesContent
        },
        props: {
            smsCampaignSchedulesPayload: Object
        }
    })
</script>
