<script setup lang="ts">
import { onMounted, ref } from 'vue';

interface Props {
    modelValue: string;
    rows?: number;
}

const props = withDefaults(defineProps<Props>(), {
    rows: 3,
});

const emit = defineEmits<{
    (e: 'update:modelValue', value: string): void;
}>();

const input = ref<HTMLTextAreaElement | null>(null);

onMounted(() => {
    if (input.value?.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value?.focus() });
</script>

<template>
    <textarea ref="input" class="border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm"
        :value="modelValue" @input="$event => emit('update:modelValue', ($event.target as HTMLTextAreaElement).value)"
        :rows="rows"></textarea>
</template>
