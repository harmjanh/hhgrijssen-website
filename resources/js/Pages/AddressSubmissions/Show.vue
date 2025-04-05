<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

defineProps<{
    submission: {
        id: number;
        user: {
            name: string;
            email: string;
            phonenumber: string;
            date_of_birth: string;
        };
        new_street: string;
        new_number: string;
        new_zipcode: string;
        new_city: string;
        old_street: string;
        old_number: string;
        old_zipcode: string;
        old_city: string;
        other_people: string | null;
        note: string | null;
        created_at: string;
    };
}>();
</script>

<template>

    <Head title="Adreswijziging Details" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Adreswijziging Details
                </h2>
                <Link :href="route('address-submissions.index')"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                Terug naar Overzicht
                </Link>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- User Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Gebruikersinformatie</h3>
                                <dl class="grid grid-cols-1 gap-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Naam</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ submission.user.name }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Email</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ submission.user.email }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Telefoonnummer
                                        </dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{
                                            submission.user.phonenumber }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Geboortedatum
                                        </dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ new Date(submission.user.date_of_birth).toLocaleDateString() }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Address Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Adresinformatie</h3>
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Oud Adres</h4>
                                    <p class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ submission.old_street }} {{ submission.old_number }}<br>
                                        {{ submission.old_zipcode }} {{ submission.old_city }}
                                    </p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Nieuw Adres
                                    </h4>
                                    <p class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ submission.new_street }} {{ submission.new_number }}<br>
                                        {{ submission.new_zipcode }} {{ submission.new_city }}
                                    </p>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="md:col-span-2 bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Aanvullende Informatie</h3>
                                <div class="mb-4">
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Andere
                                        Personen</h4>
                                    <p class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ submission.other_people || 'Geen andere personen opgegeven' }}
                                    </p>
                                </div>
                                <div>
                                    <h4 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Opmerking</h4>
                                    <p class="text-sm text-gray-900 dark:text-gray-100">
                                        {{ submission.note || 'Geen opmerkingen' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 text-sm text-gray-500 dark:text-gray-400">
                            Aangemaakt op: {{ new Date(submission.created_at).toLocaleString() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
