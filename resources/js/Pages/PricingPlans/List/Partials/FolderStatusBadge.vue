<template>
    <span
        :class="[
            'inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider',
            pricingPlan?.is_folder
                ? 'bg-slate-100 text-slate-600 border border-slate-200'
                : 'bg-indigo-50 text-indigo-700 border border-indigo-200',
        ]"
    >
        {{ pricingPlan?.is_folder ? 'Folder' : 'Plan' }}
    </span>
</template>

<script>
export default {
    name: 'FolderStatusBadge',
    props: {
        pricingPlan: {
            type: Object,
            default: null,
        },
    },
};
</script>
