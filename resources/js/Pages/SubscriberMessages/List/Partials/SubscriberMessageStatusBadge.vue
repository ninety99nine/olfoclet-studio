<template>
    <div class="flex items-center">
        <template v-if="subscriberMessage.is_successful == null">
            <span class="text-slate-400 text-xs font-medium">—</span>
        </template>
        <Tag
            v-else
            :value="subscriberMessage.is_successful ? 'Successful' : 'Unsuccessful'"
            :severity="subscriberMessage.is_successful ? 'success' : 'warn'"
            :class="['text-xs whitespace-nowrap', { 'tag-amber': !subscriberMessage.is_successful }]"
        />
    </div>
</template>

<script>
import Tag from 'primevue/tag';

export default {
    name: 'SubscriberMessageStatusBadge',
    components: { Tag },
    props: {
        subscriberMessage: { type: Object, default: null },
    },
};
</script>
