<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

interface Props {
    stats: {
        total_declarations: number;
    };
}

defineProps<Props>();
</script>

<template>

    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <!-- Stats Card -->
                            <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Aantal declaraties
                                </h3>
                                <p class="mt-2 text-3xl font-bold text-indigo-600 dark:text-indigo-400">
                                    {{ stats.total_declarations }}
                                </p>
                            </div>
                            <!-- Add more stat cards here -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
