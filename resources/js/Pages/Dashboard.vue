<script setup lang="ts">
import { onMounted, ref } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Head, Link } from '@inertiajs/vue3';

interface Props {
    stats: {
        total_declarations: number;
    };
    emailVerified?: boolean;
    missingInScipio?: boolean;
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

                <!-- Missing Contact Information Warning -->
                <div v-if="props.missingInScipio" 
                     class="mb-6 rounded-lg bg-amber-50 border border-amber-200 p-4 dark:bg-amber-900/20 dark:border-amber-800">
                    <div class="flex items-start">
                        <svg class="mr-3 h-5 w-5 text-amber-600 dark:text-amber-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495zM10 5a.75.75 0 01.75.75v3.5a.75.75 0 01-1.5 0v-3.5A.75.75 0 0110 5zm0 9a1 1 0 100-2 1 1 0 000 2z" clip-rule="evenodd" />
                        </svg>
                        <div class="flex-1">
                            <h3 class="text-sm font-semibold text-amber-800 dark:text-amber-200 mb-1">
                                Uw contactgegevens ontbreken
                            </h3>
                            <p class="text-sm text-amber-700 dark:text-amber-300 mb-3">
                                Uw e-mailadres of telefoonnummer komt niet voor in onze administratie. 
                                Gelieve uw gegevens door te geven aan het kerkelijk bureau.
                            </p>
                            <Link :href="route('contact-information.create')"
                                class="inline-flex items-center text-sm font-medium text-amber-800 hover:text-amber-900 dark:text-amber-200 dark:hover:text-amber-100 underline">
                                Gegevens doorgeven
                                <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </Link>
                        </div>
                    </div>
                </div>

                <!-- Voluntary Contribution CTA Card -->
                <div class="mb-6 overflow-hidden bg-white border border-gray-200 shadow-sm sm:rounded-lg dark:bg-gray-800 dark:border-gray-700">
                    <div class="p-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Vrijwillige Bijdrage 2026</h3>
                                <p class="text-gray-600 dark:text-gray-300 mb-4">
                                    Ondersteun onze kerkelijke gemeente in 2026 met een vrijwillige bijdrage. 
                                    U kunt uw bijdrage eenvoudig online instellen.
                                </p>
                                <Link :href="route('voluntary-contributions.index')"
                                    class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300">
                                    Meer informatie
                                    <svg class="ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
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
