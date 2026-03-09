<template>
    <Tag
        :value="smsCampaign?.can_send_messages === true ? 'Yes' : 'No'"
        :severity="smsCampaign?.can_send_messages === true ? 'success' : 'warn'"
        :class="['text-xs', { 'tag-amber': smsCampaign?.can_send_messages !== true }]"
    />
</template>

<script>
import { defineComponent } from 'vue';
import Tag from 'primevue/tag';

export default defineComponent({
    components: { Tag },
    props: {
        smsCampaign: {
            type: Object,
            default: null,
        },
    },
});
</script>
