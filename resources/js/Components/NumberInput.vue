<script setup>
import { ref } from 'vue';

defineProps({
    maxlength: Number,
    placeholder: String,
    modelValue: [Number, String],
});

defineEmits(['update:modelValue']);

const input = ref(null);

</script>

<template>
    <input
        ref="input"
        type="number"
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        :value="modelValue"
        :maxlength="maxlength"
        :placeholder="placeholder"
        @input="$emit('update:modelValue', ($event.target.value ? parseInt($event.target.value) : null))"
    >
</template>
