<template>
    <div class="flex items-center">
        <span v-if="billingTransaction.is_successful" class="bg-green-100 text-green-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded-md dark:bg-green-200 dark:text-green-900">
            Successful
        </span>
        <span v-else class="failure-type-badge">
            {{ billingTransaction.failure_type ?? 'Unsuccessful' }}
        </span>
    </div>
</template>
<script>
    export default {
        props: {
            billingTransaction: Object
        },
        data() {
            return {}
        }
    }
</script>

<style scoped>
.failure-type-badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 10px;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 600;
    background: #fef3c7;
    color: #92400e;
}
</style>
