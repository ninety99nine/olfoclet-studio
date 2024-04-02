<template>

    <div>
        <span v-if="autoBillingSubscriptionPlanJobBatch.progress == 100 && autoBillingSubscriptionPlanJobBatch.failed_jobs == 0" class="bg-green-100 text-green-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded mr-2 dark:bg-green-200 dark:text-green-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
            </svg>
            Successful
        </span>
        <span v-else-if="autoBillingSubscriptionPlanJobBatch.failed_jobs > 0" class="bg-red-100 text-red-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-red-200 dark:text-red-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1 animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ autoBillingSubscriptionPlanJobBatch.failed_jobs }} Failed
        </span>
        <span v-else-if="autoBillingSubscriptionPlanJobBatch.progress > 0 && autoBillingSubscriptionPlanJobBatch.progress < 100" class="bg-blue-100 text-blue-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded mr-2 dark:bg-blue-200 dark:text-blue-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1 animate-spin" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
            </svg>
            Running
        </span>
        <span v-else class="bg-gray-100 text-gray-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded mr-2 dark:bg-gray-200 dark:text-gray-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Waiting
        </span>
    </div>

</template>
<script>

    export default {
        props: {
            autoBillingSubscriptionPlanJobBatch: Object
        },
        data() {
            return {
            }
        }
    }
</script>
