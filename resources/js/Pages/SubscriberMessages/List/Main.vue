<template>
    <app-layout title="Dashboard">
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

                <subscriber-messages-content :subscriberMessagesPayload="subscriberMessagesPayload" />

            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import SubscriberMessagesContent from './Partials/Content.vue'

    export default defineComponent({
        components: {
            AppLayout,
            SubscriberMessagesContent
        },
        props: {
            subscriberMessagesPayload: Object
        }
    })
</script>
