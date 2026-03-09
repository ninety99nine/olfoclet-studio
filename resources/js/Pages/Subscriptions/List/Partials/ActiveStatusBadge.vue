<template>
    <Tag
        :value="subscription?.is_active ? 'Active' : 'Inactive'"
        :severity="subscription?.is_active ? 'success' : 'warn'"
        :class="{ 'tag-amber': !subscription?.is_active }"
    />
</template>

<script>
import Tag from 'primevue/tag';

export default {
    name: 'SubscriptionActiveStatusBadge',
    components: { Tag },
    props: {
        subscription: {
            type: Object,
            default: null,
        },
    },
};
</script>
