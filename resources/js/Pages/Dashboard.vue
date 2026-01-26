<script setup lang="ts">
import { onMounted, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Head } from '@inertiajs/vue3';

interface Props {
    stats: {
        total_declarations: number;
    };
    emailVerified?: boolean;
}

const props = defineProps<Props>();

const showVerificationMessage = ref(false);

onMounted(() => {
    if (props.emailVerified) {
        showVerificationMessage.value = true;
        // Remove the query parameter from URL without reloading
        if (window.history.replaceState) {
            const url = new URL(window.location.href);
            url.searchParams.delete('verified');
            window.history.replaceState({}, '', url.toString());
        }
    }
});
</script>

<template>

    <Head title="Gemeente portal HHG Rijssen" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader title="Gemeente portal HHG Rijssen"
                description="Bestellen munten, adressen wijzigen en declaraties indienen" />
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <!-- Email Verification Success Message -->
                <div v-if="showVerificationMessage" 
                     class="mb-4 rounded-lg bg-green-50 p-4 text-green-800 dark:bg-green-900/20 dark:text-green-400">
                    <div class="flex items-center">
                        <svg class="mr-3 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <p class="font-medium">Uw emailadres is succesvol bevestigd!</p>
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                            <!-- Stats Card -->
                            <div class="p-6 bg-white rounded-lg shadow dark:bg-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Aantal declaraties
                                </h3>
                                <p class="mt-2 text-3xl font-bold text-primary-600 dark:text-primary-400">
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
