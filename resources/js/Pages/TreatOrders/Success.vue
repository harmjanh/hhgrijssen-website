<script setup lang="ts">
import { computed } from 'vue';
import { Head } from '@inertiajs/vue3';
import NavBar from '@/Components/NavBar.vue';
import PageFooter from '@/Components/PageFooter.vue';

const props = defineProps<{
    pages: Array<{ id: number; title: string; slug: string }>;
    order: {
        id: number;
        name: string;
        email: string;
        phone: string;
        snoeprollen_quantity: number;
        stroopwafels_quantity: number;
        total_amount: number;
        status: string;
    };
}>();

const formatPrice = (amount: number) =>
    new Intl.NumberFormat('nl-NL', { style: 'currency', currency: 'EUR' }).format(amount);

const statusMessage = computed(() => {
    switch (props.order.status) {
        case 'paid':
            return 'Uw betaling is succesvol ontvangen. Bedankt voor uw bestelling!';
        case 'pending':
            return 'Uw bestelling is ontvangen. De betaling wordt nog verwerkt — u ontvangt zo een bevestiging zodra de betaling is voltooid.';
        case 'failed':
            return 'De betaling is niet gelukt. Neem contact op met de kerk of plaats een nieuwe bestelling.';
        default:
            return 'Uw bestelling is ontvangen.';
    }
});

const isPaid = computed(() => props.order.status === 'paid');
</script>

<template>
    <Head title="Bestelling ontvangen" />

    <NavBar :pages="pages" />

    <div class="mx-auto max-w-2xl px-6 py-12 lg:px-8">
        <div class="text-center">
            <svg
                class="mx-auto h-12 w-12"
                :class="isPaid ? 'text-green-500' : order.status === 'failed' ? 'text-red-500' : 'text-amber-500'"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    v-if="isPaid"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M5 13l4 4L19 7"
                />
                <path
                    v-else
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
                />
            </svg>
            <h1 class="mt-4 text-3xl font-bold tracking-tight text-gray-900">Bedankt voor uw bestelling!</h1>
            <p class="mt-4 text-gray-700 text-lg leading-relaxed">
                {{ statusMessage }}
            </p>
        </div>

        <div class="mt-8 rounded-lg border border-gray-200 bg-gray-50 p-6 text-sm text-gray-700 space-y-2">
            <p><strong>Bestelnummer:</strong> #{{ order.id }}</p>
            <p><strong>Naam:</strong> {{ order.name }}</p>
            <p><strong>Telefoon:</strong> {{ order.phone }}</p>
            <p v-if="order.snoeprollen_quantity > 0">
                <strong>Snoeprollen:</strong> {{ order.snoeprollen_quantity }}× (10 stuks)
            </p>
            <p v-if="order.stroopwafels_quantity > 0">
                <strong>Stroopwafels:</strong> {{ order.stroopwafels_quantity }}× (3 pakjes)
            </p>
            <p><strong>Totaalbedrag:</strong> {{ formatPrice(order.total_amount) }}</p>
        </div>

        <div class="mt-8 flex justify-center gap-4">
            <a
                href="/"
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 transition"
            >
                Terug naar homepage
            </a>
            <a
                :href="route('treat-orders.create')"
                class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-50 transition"
            >
                Nog een bestelling
            </a>
        </div>
    </div>

    <PageFooter :pages="pages" />
</template>
