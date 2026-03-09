<template>
    <Tag
        :value="autoBillingSchedule?.auto_billing_enabled ? 'Yes' : 'No'"
        :severity="autoBillingSchedule?.auto_billing_enabled ? 'success' : 'warn'"
        :class="['text-xs', { 'tag-amber': !autoBillingSchedule?.auto_billing_enabled }]"
    />
</template>

<script>
import Tag from 'primevue/tag';

export default {
    components: { Tag },
    props: {
        autoBillingSchedule: Object,
    },
};
</script>
