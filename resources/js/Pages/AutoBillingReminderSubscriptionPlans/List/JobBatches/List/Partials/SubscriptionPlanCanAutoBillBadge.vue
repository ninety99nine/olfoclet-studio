<template>

    <div>
        <span v-if="subscriptionPlan.can_auto_bill == true" class="bg-green-100 text-green-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-green-200 dark:text-green-900">
            Enabled
        </span>
        <span v-else class="bg-gray-100 text-gray-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-gray-200 dark:text-gray-900">
            Disabled
        </span>
    </div>

</template>
<script>

    export default {
        props: {
            subscriptionPlan: Object
        },
        data() {
            return {
            }
        }
    }
</script>
