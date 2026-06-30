<script setup>
import { DocumentArrowDownIcon } from '@heroicons/vue/24/outline';

defineProps({
    files: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <div v-if="files.length" class="mt-10 border-t border-gray-200 pt-8">
        <h2 class="mb-4 text-xl font-semibold text-gray-900">Documenten</h2>
        <ul class="space-y-3">
            <li v-for="file in files" :key="file.id">
                <a
                    :href="'/storage/' + file.file_path"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center gap-2 text-lg text-amber-700 hover:text-amber-800 hover:underline"
                >
                    <DocumentArrowDownIcon class="size-5 shrink-0" />
                    {{ file.name }}
                </a>
            </li>
        </ul>
    </div>
</template>
