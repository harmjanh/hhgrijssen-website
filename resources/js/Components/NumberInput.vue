<script setup lang="ts">
import { onMounted, ref } from 'vue';

interface Props {
    modelValue: number;
    min?: number;
    max?: number;
    step?: number;
}

const props = withDefaults(defineProps<Props>(), {
    min: 0,
    max: undefined,
    step: 1,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: number): void;
}>();

const input = ref<HTMLInputElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

const handleInput = (event: Event) => {
    const target = event.target as HTMLInputElement;
    emit('update:modelValue', Number(target.value));
};

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <input ref="input" type="number"
        class="border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm" :value="modelValue"
        @input="handleInput" :min="min" :max="max" :step="step">
</template>
