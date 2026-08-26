<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import NavBar from '@/Components/NavBar.vue';
import PageFooter from '@/Components/PageFooter.vue';

defineProps<{
    pages: Array<{ id: number; title: string; slug: string }>;
    season: string;
    registration: {
        first_name: string;
        last_name: string;
        email: string;
        phone: string;
        group_label: string;
    };
}>();
</script>

<template>
    <Head title="Inschrijving ontvangen" />

    <NavBar :pages="pages" />

    <div class="mx-auto max-w-2xl px-6 py-12 lg:px-8">
        <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900">
                Bedankt voor uw inschrijving
            </h1>
            <p class="mt-4 text-gray-700 text-lg leading-relaxed">
                Uw inschrijving voor catechisatie seizoen {{ season }} is ontvangen.
            </p>
        </div>

        <div class="mt-8 rounded-lg border border-gray-200 bg-gray-50 p-6 text-sm text-gray-700 space-y-2">
            <p>
                <strong>Naam:</strong> {{ registration.first_name }} {{ registration.last_name }}
            </p>
            <p>
                <strong>E-mailadres:</strong> {{ registration.email }}
            </p>
            <p>
                <strong>Telefoonnummer:</strong> {{ registration.phone }}
            </p>
            <p>
                <strong>Groep:</strong> {{ registration.group_label }}
            </p>
        </div>

        <div class="mt-8 flex justify-center gap-4">
            <a
                href="/"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition"
            >
                Terug naar homepage
            </a>
            <a
                :href="route('catechesis-registrations.create')"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition"
            >
                Nog iemand inschrijven
            </a>
        </div>
    </div>

    <PageFooter :pages="pages" />
</template>
