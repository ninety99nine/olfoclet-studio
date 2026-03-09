<template>
    <Tag
        :value="pricingPlan?.active ? 'Active' : 'Inactive'"
        :severity="pricingPlan?.active ? 'success' : 'warn'"
        :class="['text-xs', { 'tag-amber': !pricingPlan?.active }]"
    />
</template>

<script>
import Tag from 'primevue/tag';

export default {
    name: 'ActiveStatusBadge',
    components: { Tag },
    props: {
        pricingPlan: {
            type: Object,
            default: null,
        },
    },
};
</script>
