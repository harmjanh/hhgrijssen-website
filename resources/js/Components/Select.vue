<script setup lang="ts">
import { onMounted, ref } from 'vue';

interface Props {
    modelValue: string | number | null;
    options: Array<{ value: string | number; label: string }>;
}

const props = defineProps<Props>();

const emit = defineEmits<{
    (e: 'update:modelValue', value: string | number | null): void;
}>();

const select = ref<HTMLSelectElement | null>(null);

onMounted(() => {
    if (select.value?.hasAttribute('autofocus')) {
        select.value.focus();
    }
});

const handleChange = (event: Event) => {
    const target = event.target as HTMLSelectElement;
    const value = target.value === '' ? null : (isNaN(Number(target.value)) ? target.value : Number(target.value));
    emit('update:modelValue', value);
};

defineExpose({ focus: () => select.value?.focus() });
</script>

<template>
    <select
        ref="select"
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 dark:focus:border-indigo-600 dark:focus:ring-indigo-600"
        :value="modelValue"
        @change="handleChange"
    >
        <option value="">-- Selecteer --</option>
        <option v-for="option in options" :key="option.value" :value="option.value">
            {{ option.label }}
        </option>
    </select>
</template>


