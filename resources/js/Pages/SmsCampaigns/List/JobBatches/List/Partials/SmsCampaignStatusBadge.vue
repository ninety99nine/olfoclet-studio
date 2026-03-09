<template>
    <span
        v-if="statusLabel === 'Pending'"
        class="inline-flex items-center px-2.5 py-0.5 rounded text-xs font-medium bg-slate-100 text-slate-800 whitespace-nowrap"
    >Pending</span>
    <Tag
        v-else
        :value="statusLabel"
        :severity="isSuccessful ? 'success' : 'warn'"
        :class="['text-xs', { 'tag-amber': !isSuccessful }]"
    />
</template>

<script>
import { defineComponent, computed } from 'vue';
import Tag from 'primevue/tag';

export default defineComponent({
    components: { Tag },
    props: {
        smsCampaignBatchJob: {
            type: Object,
            default: () => ({}),
        },
    },
    computed: {
        batchProgress() {
            const p = this.smsCampaignBatchJob?.progress;
            return p == null || p === '' ? 0 : Number(p);
        },
        isSuccessful() {
            return this.batchProgress === 100 && (this.smsCampaignBatchJob?.failed_jobs ?? 0) === 0;
        },
        statusLabel() {
            if (this.isSuccessful) return 'Successful';
            const failed = this.smsCampaignBatchJob?.failed_jobs ?? 0;
            if (failed > 0) return `${failed} Failed`;
            if (this.batchProgress > 0 && this.batchProgress < 100) return 'Running';
            return 'Pending';
        },
    },
});
</script>
