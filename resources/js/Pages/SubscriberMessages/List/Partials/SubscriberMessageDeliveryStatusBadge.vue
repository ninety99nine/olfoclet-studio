<template>
    <div class="flex items-center">
        <template v-if="subscriberMessage.delivery_status_update_is_successful == null">
            <span class="text-slate-400 text-xs font-medium">—</span>
        </template>
        <Tag
            v-else-if="subscriberMessage.delivery_status_update_is_successful"
            :value="subscriberMessage.delivery_status"
            severity="success"
            class="text-xs whitespace-nowrap"
        />
        <Tag
            v-else
            :value="subscriberMessage.delivery_status_update_failure_type || 'Unsuccessful'"
            severity="warn"
            :class="['text-xs whitespace-nowrap', 'tag-amber']"
        />
    </div>
</template>

<script>
import Tag from 'primevue/tag';

export default {
    name: 'SubscriberMessageDeliveryStatusBadge',
    components: { Tag },
    props: {
        subscriberMessage: { type: Object, default: null },
    },
};
</script>
