<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    maxlength: Number,
    modelValue: String,
    placeholder: String
});

defineEmits(['update:modelValue']);

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <div class="flex">
        <div class="py-2 px-2 rounded-tl-lg rounded-bl-lg bg-gray-200">@</div>
        <div>
            <input
                type="text"
                ref="input"
                class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full block rounded-tl-none rounded-bl-none rounded-tr-lg rounded-br-lg"
                :value="modelValue"
                :maxlength="maxlength"
                :placeholder="placeholder"
                @input="$emit('update:modelValue', $event.target.value)"
            >
        </div>
    </div>
</template>
