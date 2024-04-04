<template>

    <div class="flex items-center">

        <span v-if="billingTransaction.rating_type == 'Prepaid'" class="bg-green-100 text-green-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded mr-2 dark:bg-green-200 dark:text-green-900">
            {{ billingTransaction.rating_type }}
        </span>

        <span v-else-if="billingTransaction.rating_type == 'Postpaid'" class="bg-blue-100 text-blue-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded mr-2 dark:bg-blue-200 dark:text-blue-900">
            {{ billingTransaction.rating_type }}
        </span>

        <span v-else class="bg-gray-100 text-gray-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded mr-2 dark:bg-gray-200 dark:text-gray-900">
            {{ billingTransaction.rating_type }}
        </span>

    </div>

</template>
<script>

    export default {
        props: {
            billingTransaction: Object
        },
        data() {
            return {
            }
        }
    }
</script>
