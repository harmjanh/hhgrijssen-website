<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import NavBar from '@/Components/NavBar.vue';
import PageFooter from '@/Components/PageFooter.vue';

defineProps<{
    pages: Array<{ id: number; title: string; slug: string }>;
}>();
</script>

<template>
    <Head title="Declaratie Bevestigd" />

    <NavBar :pages="pages" />

    <div class="mx-auto w-3/5 py-12">
        <h1 class="mb-6 text-3xl font-bold tracking-tight text-gray-900 text-left">Declaratie Bevestigd</h1>
        
        <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M5 13l4 4L19 7" />
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">Bedankt voor uw declaratie!</h3>
            <p class="mt-2 text-gray-700 text-lg/8">
                Uw declaratie is succesvol ontvangen en wordt verwerkt.
            </p>
            <p class="mt-2 text-gray-700 text-lg/8">
                U ontvangt een bevestigingsmail met de details van uw declaratie.
            </p>
        </div>

        <div class="mt-8">
            <h4 class="text-lg font-medium text-gray-900">Wat gebeurt er nu?</h4>
            <ul class="mt-4 space-y-2 text-gray-700 text-lg/8">
                <li class="flex items-center">
                    <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Uw declaratie is ontvangen en wordt beoordeeld
                </li>
                <li class="flex items-center">
                    <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    De penningmeester ontvangt een notificatie
                </li>
                <li class="flex items-center">
                    <svg class="h-5 w-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    U ontvangt een bevestigingsmail
                </li>
                <li class="flex items-center">
                    <svg class="h-5 w-5 text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    U wordt op de hoogte gehouden van de status
                </li>
            </ul>
        </div>

        <div class="mt-8 flex justify-center">
            <a href="/" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Terug naar Homepage
            </a>
        </div>
    </div>

    <PageFooter :pages="pages" />
</template>

