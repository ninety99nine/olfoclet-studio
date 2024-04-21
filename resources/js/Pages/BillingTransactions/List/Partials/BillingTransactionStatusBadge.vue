<template>

    <div class="flex items-center">

        <span v-if="billingTransaction.is_successful" class="bg-green-100 text-green-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-green-200 dark:text-green-900">
            Successful
        </span>

        <template v-else>

            <span v-if="billingTransaction.failure_type == 'Inactive Account' || billingTransaction.failure_type == 'Insufficient Funds'" class="bg-yellow-100 text-yellow-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-yellow-200 dark:text-yellow-900">
                {{  billingTransaction.failure_type }}
            </span>

            <span v-else class="bg-red-100 text-red-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-red-200 dark:text-red-900">
                {{  billingTransaction.failure_type ?? 'Unsuccessful' }}
            </span>

            <el-popover placement="bottom" title="Reason" width="300" trigger="hover"
                :content="billingTransaction.failure_reason">
                <template v-slot:reference>
                    <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                </template>
            </el-popover>
        </template>

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
