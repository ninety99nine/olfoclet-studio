<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
});
</script>

<template>
    <button :type="type" :class="['text-white bg-blue-600 hover:bg-blue-700 active:bg-blue-800 cursor-pointer active:shadow-lg focus:outline-none font-medium rounded-md text-center inline-flex items-center text-xs px-4 py-2']">
        <slot />
    </button>
</template>
