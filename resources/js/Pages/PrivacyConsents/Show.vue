<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';

defineProps<{
    consent: {
        id: number;
        name: string;
        street: string;
        zipcode: string;
        city: string;
        birth_date: string;
        voorbede_eredienst: boolean;
        voorbede_zaaier: boolean;
        verjaardag_zaaier: boolean;
        rsv_gegevens: boolean;
        place: string;
        submission_date: string;
        agreed: boolean;
        created_at: string;
    };
}>();
</script>

<template>
    <Head title="Privacy Toestemmingsformulier Details" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader 
                title="Privacy Toestemmingsformulier Details"
                description="Details van uw ingediende privacy toestemmingsformulier" 
            />
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="mb-6">
                    <Link :href="route('privacy-consents.index')"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:bg-gray-700 dark:focus:bg-white active:bg-gray-900 dark:active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
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
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ consent.name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Geboortedatum</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ new Date(consent.birth_date).toLocaleDateString('nl-NL') }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Address Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Adresgegevens</h3>
                                <dl class="grid grid-cols-1 gap-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Adres</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ consent.street }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Postcode</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ consent.zipcode }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Woonplaats</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ consent.city }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Consent Details -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg md:col-span-2">
                                <h3 class="text-lg font-medium mb-4">Toestemmingen</h3>
                                <dl class="grid grid-cols-1 gap-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Voorbede/dankzegging tijdens eredienst
                                        </dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">
                                            <span v-if="consent.voorbede_eredienst" 
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Wel toestemming
                                            </span>
                                            <span v-else 
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                Geen toestemming
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Voorbede/dankzegging in De Zaaier
                                        </dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">
                                            <span v-if="consent.voorbede_zaaier" 
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Wel toestemming
                                            </span>
                                            <span v-else 
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                Geen toestemming
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Verjaardag 75+ in De Zaaier
                                        </dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">
                                            <span v-if="consent.verjaardag_zaaier" 
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Wel toestemming
                                            </span>
                                            <span v-else 
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                Geen toestemming
                                            </span>
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">
                                            Gegevens aan RSV
                                        </dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">
                                            <span v-if="consent.rsv_gegevens" 
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Wel toestemming
                                            </span>
                                            <span v-else 
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
                                                Geen toestemming
                                            </span>
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Form Details -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Formulierdetails</h3>
                                <dl class="grid grid-cols-1 gap-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Plaats</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">{{ consent.place }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Datum</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">
                                            {{ new Date(consent.submission_date).toLocaleDateString('nl-NL') }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Akkoord</dt>
                                        <dd class="text-sm text-gray-900 dark:text-gray-100">
                                            <span v-if="consent.agreed" 
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                                Ja
                                            </span>
                                            <span v-else 
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200">
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
                                <li>U kunt op ieder moment een weergave van uw gegevens opvragen bij het kerkelijk bureau.</li>
                                <li>Onjuistheden en wijzigingen kunt u doorgeven aan het kerkelijk bureau.</li>
                                <li>Mocht uw lidmaatschap door overlijden of vertrek worden beëindigd, dan blijven de gegevens twee jaar bewaard, tenzij u het kerkelijk bureau verzoekt deze eerder te verwijderen.</li>
                            </ul>
                        </div>

                        <div class="mt-6 text-sm text-gray-500 dark:text-gray-400">
                            Ingediend op: {{ new Date(consent.created_at).toLocaleString('nl-NL') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

