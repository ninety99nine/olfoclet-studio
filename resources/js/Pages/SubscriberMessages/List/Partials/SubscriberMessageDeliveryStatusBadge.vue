<template>

    <div class="flex items-center">

        <span v-if="subscriberMessage.delivery_status_update_is_successful == true" class="bg-green-100 text-green-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-green-200 dark:text-green-900">
            {{ subscriberMessage.delivery_status }}
        </span>

        <template v-else-if="subscriberMessage.delivery_status_update_is_successful == false">
            <span class="bg-yellow-100 text-yellow-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-yellow-200 dark:text-yellow-900">
                Unsuccessful
            </span>

            <el-popover placement="bottom" title="Reason" width="300" trigger="hover"
                :content="subscriberMessage.delivery_status_update_failure_reason">
                <template v-slot:reference>
                    <svg class="w-4 h-4 ml-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                    </svg>
                </template>
            </el-popover>
        </template>

        <span v-else class="bg-gray-100 text-gray-900 text-sm font-medium inline-flex items-center px-2.5 py-0.5 rounded dark:bg-gray-200 dark:text-gray-900">
            <svg class="w-4 h-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
            </svg>
            {{ subscriberMessage.delivery_status }}
        </span>

    </div>

</template>
<script>

    export default {
        props: {
            subscriberMessage: Object
        },
        data() {
            return {
            }
        }
    }
</script>
