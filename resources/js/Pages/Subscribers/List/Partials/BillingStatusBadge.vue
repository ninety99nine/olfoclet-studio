<template>
    <Tag
        :value="billingTransaction.is_successful ? 'Successful' : 'Unsuccessful'"
        :severity="billingTransaction.is_successful ? 'success' : 'warn'"
        :class="{ 'tag-amber': !billingTransaction.is_successful }"
    />
</template>

<script>
import Tag from 'primevue/tag';

export default {
    name: 'BillingStatusBadge',
    components: { Tag },
    props: {
        billingTransaction: {
            type: Object,
            required: true,
        },
    },
};
</script>
