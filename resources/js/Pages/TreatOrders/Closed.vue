<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import NavBar from '@/Components/NavBar.vue';
import PageFooter from '@/Components/PageFooter.vue';

defineProps<{
    pages: Array<{ id: number; title: string; slug: string }>;
    orderDeadline: string;
    pickupDate: string;
}>();
</script>

<template>
    <Head title="Bestellen gesloten" />

    <NavBar :pages="pages" />

    <div class="mx-auto max-w-2xl px-6 py-12 lg:px-8">
        <div class="text-center">
            <h1 class="text-3xl font-bold tracking-tight text-gray-900">
                Snoep- en stroopwafelactie
            </h1>
            <p class="mt-6 text-lg text-gray-700 leading-relaxed">
                De bestelperiode is gesloten. Bestellen was mogelijk tot en met
                <strong>{{ orderDeadline }}</strong>.
            </p>
            <p class="mt-4 text-gray-700 leading-relaxed">
                Hebt u al besteld? U kunt uw bestelling ophalen op
                <strong>{{ pickupDate }}</strong> tijdens de jeugd- en gemeentedag op
                <strong>Plasdijk 18</strong> in Markelo.
            </p>
        </div>

        <div class="mt-8 flex justify-center">
            <a
                href="/"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition"
            >
                Terug naar homepage
            </a>
        </div>
    </div>

    <PageFooter :pages="pages" />
</template>
