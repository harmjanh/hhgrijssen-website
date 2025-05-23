<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Link } from '@inertiajs/vue3';
import axios from 'axios';

interface Order {
    id: number;
    name: string;
    email: string;
    blue_coins: number;
    red_coins: number;
    total_amount: number;
    status: string;
    created_at: string;
}

interface Props {
    orders: Order[];
    prices: {
        blue_coin: number;
        red_coin: number;
        payment_fee: number;
    };
}

const props = defineProps<Props>();

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('nl-NL', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const getStatusColor = (status: string) => {
    switch (status) {
        case 'paid':
            return 'text-green-700 bg-green-50';
        case 'pending':
            return 'text-yellow-700 bg-yellow-50';
        case 'failed':
            return 'text-red-700 bg-red-50';
        default:
            return 'text-gray-700 bg-gray-50';
    }
};

const getStatusText = (status: string) => {
    switch (status) {
        case 'paid':
            return 'Betaald';
        case 'pending':
            return 'In behandeling';
        case 'failed':
            return 'Mislukt';
        default:
            return status;
    }
};

const downloadPdf = async (orderId: number) => {
    try {
        window.open(route('coin-orders.download', orderId), '_blank');
    } catch (error) {
        console.error('Error downloading PDF:', error);
    }
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <PageHeader title="Mijn bestelde munten" description="Overzicht van al uw munt bestellingen" />
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-end mb-4">
                            <Link :href="route('coin-orders.create')"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Nieuwe Bestelling
                            </Link>
                        </div>

                        <div v-if="orders.length === 0" class="text-center py-8">
                            <p class="text-gray-500">Je hebt nog geen munten besteld.</p>
                            <Link :href="route('coin-orders.create')"
                                class="mt-4 inline-flex items-center text-indigo-600 hover:text-indigo-900">
                            Bestel nu munten
                            <svg class="ml-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5l7 7-7 7" />
                            </svg>
                            </Link>
                        </div>

                        <div v-else class="space-y-6">
                            <div v-for="order in orders" :key="order.id" class="border rounded-lg p-6">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <h3 class="text-lg font-medium text-gray-900">
                                            Bestelling #{{ order.id }}
                                        </h3>
                                        <p class="text-sm text-gray-500">{{ formatDate(order.created_at) }}</p>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button @click="downloadPdf(order.id)"
                                            class="inline-flex items-center px-3 py-1 bg-gray-100 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200 focus:bg-gray-200 active:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                            </svg>
                                            Download PDF
                                        </button>
                                        <span :class="getStatusColor(order.status)"
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium">
                                            {{ getStatusText(order.status) }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mt-4">
                                    <dl class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Blauwe Munten</dt>
                                            <dd class="mt-1 text-sm text-gray-900">
                                                {{ order.blue_coins }} x €{{ prices.blue_coin.toFixed(2) }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Rode Munten</dt>
                                            <dd class="mt-1 text-sm text-gray-900">
                                                {{ order.red_coins }} x €{{ prices.red_coin.toFixed(2) }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-500">Betalingskosten</dt>
                                            <dd class="mt-1 text-sm text-gray-900">
                                                €{{ prices.payment_fee.toFixed(2) }}
                                            </dd>
                                        </div>
                                        <div>
                                            <dt class="text-sm font-medium text-gray-900">Totaal</dt>
                                            <dd class="mt-1 text-sm font-medium text-gray-900">
                                                €{{ Number(order.total_amount).toFixed(2) }}
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
