<template>

    <div>
        <span v-if="isActive" class="bg-green-100 text-green-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-green-200 dark:text-green-900">
            Active
        </span>
        <span v-else class="bg-yellow-100 text-yellow-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-yellow-200 dark:text-yellow-900">
            Inactive
        </span>
    </div>

</template>
<script>

    export default {
        props: {
            isActive: Boolean
        }
    }
</script>
