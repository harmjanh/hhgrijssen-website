<script setup lang="ts">
defineProps<{
    title: string;
    description?: string;
}>();
</script>

<template>
    <div class="bg-white dark:bg-gray-800 shadow w-full">
        <div class="px-4 py-5 sm:px-6">
            <h1 class="text-2xl font-bold leading-7 text-gray-900 dark:text-gray-100 sm:text-3xl sm:truncate">
                {{ title }}
            </h1>
            <p v-if="description" class="mt-1 max-w-5xl text-sm text-gray-500 dark:text-gray-400">
                {{ description }}
            </p>
        </div>
    </div>
</template>
