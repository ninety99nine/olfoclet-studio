<template>

    <div>
        <span v-if="subscriptionPlan.active" class="bg-green-100 text-green-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded mr-2 dark:bg-green-200 dark:text-green-900">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>

            Yes
        </span>
        <span v-else class="bg-gray-100 text-gray-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded mr-2 dark:bg-gray-200 dark:text-gray-900">
            No
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
