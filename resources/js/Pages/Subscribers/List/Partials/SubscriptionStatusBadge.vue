<template>
    <Tag :value="isActive ? 'Active' : 'Inactive'" :severity="isActive ? 'success' : 'warn'" :class="{ 'tag-amber': !isActive }" />
</template>

<script>
import Tag from 'primevue/tag';

export default {
    name: 'SubscriptionStatusBadge',
    components: { Tag },
    props: {
        isActive: Boolean,
    },
};
</script>
