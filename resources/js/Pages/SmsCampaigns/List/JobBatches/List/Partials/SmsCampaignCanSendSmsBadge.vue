<template>

    <div>
        <span v-if="smsCampaign.can_send_messages == true" class="bg-green-100 text-green-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-green-200 dark:text-green-900">
            Yes
        </span>
        <span v-else class="bg-gray-100 text-gray-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-gray-200 dark:text-gray-900">
            No
        </span>
    </div>

</template>
<script>

    export default {
        props: {
            smsCampaign: Object
        },
        data() {
            return {
            }
        }
    }
</script>
