<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';

defineProps<{
    authorization: {
        id: number;
        name: string;
        initials: string;
        street: string;
        zipcode: string;
        city: string;
        iban: string;
        submission_date: string;
        agreed: boolean;
        created_at: string;
    };
}>();
</script>

<template>
    <Head title="Machtiging De Zaaier Details" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader 
                title="Machtiging De Zaaier Details"
                description="Details van uw ingediende machtiging" 
            />
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="route('zaaier-authorizations.index')"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                        Terug naar Overzicht
                    </Link>
                </div>
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Personal Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Persoonlijke Informatie</h3>
                                <dl class="grid grid-cols-1 gap-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Naam</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ authorization.name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Voorletters</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ authorization.initials }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Address Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Adresgegevens</h3>
                                <dl class="grid grid-cols-1 gap-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Adres</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ authorization.street }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Postcode</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ authorization.zipcode }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Woonplaats</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ authorization.city }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Bank Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Bankgegevens</h3>
                                <dl class="grid grid-cols-1 gap-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">IBAN</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100 font-mono">{{ authorization.iban }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Authorization Details -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Machtigingsdetails</h3>
                                <dl class="grid grid-cols-1 gap-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Datum Machtiging</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ new Date(authorization.submission_date).toLocaleDateString('nl-NL') }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Akkoord</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">
                                            <span v-if="authorization.agreed" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Ja
                                            </span>
                                            <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                Nee
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <!-- Information Box -->
                        <div class="mt-6 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                            <h3 class="text-sm font-semibold mb-2 text-blue-900 dark:text-blue-100">Belangrijke Informatie</h3>
                            <ul class="text-sm text-blue-800 dark:text-blue-200 space-y-1 list-disc list-inside">
                                <li>De incasso vindt jaarlijks plaats in april.</li>
                                <li>Het abonnementsgeld bedraagt momenteel €20 per jaar.</li>
                                <li>De Zaaier verschijnt tweewekelijks en wordt bij u aan huis geleverd.</li>
                                <li>Indien uw lidmaatschap van de kerkelijke gemeente wordt stopgezet, wordt de incasso automatisch stopgezet.</li>
                            </ul>
                        </div>

                        <div class="mt-6 text-sm text-gray-500 dark:text-gray-400">
                            Ingediend op: {{ new Date(authorization.created_at).toLocaleString('nl-NL') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


