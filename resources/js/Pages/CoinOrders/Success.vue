<script setup lang="ts">
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Link } from '@inertiajs/vue3';

interface Props {
    order: {
        id: number;
        name: string;
        email: string;
        silver_coins: number;
        gold_coins: number;
        total_amount: number;
        status: string;
        pickup_moment: {
            id: number;
            date: string;
        } | null;
    };
}

defineProps<Props>();

const formatPickupMoment = (pickupMoment: Props['order']['pickup_moment']) => {
    if (!pickupMoment) return '';
    
    const date = new Date(pickupMoment.date).toLocaleDateString('nl-NL', {
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric'
    });
    
    return `${date} om 10:00`;
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <!-- <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Bestelling Bevestigd
            </h2> -->
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-green-500" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">Bedankt voor je bestelling!</h3>
                            <p class="mt-2 text-sm text-gray-500">
                                Je bestelling is succesvol ontvangen en wordt verwerkt.
                            </p>
                        </div>

                        <div class="mt-8">
                            <h4 class="text-lg font-medium text-gray-900">Bestelling Details</h4>
                            <dl class="mt-4 space-y-4">
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Bestelling Nummer</dt>
                                        <dd class="mt-1 text-sm text-gray-900">#{{ order.id }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Status</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ order.status }}</dd>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Naam</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ order.name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">E-mailadres</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ order.email }}</dd>
                                    </div>
                                </div>

                                <div class="border-t border-gray-200 pt-4">
                                    <h5 class="text-sm font-medium text-gray-500">Bestelde Munten</h5>
                                    <dl class="mt-2 space-y-2">
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Zilveren Munten</dt>
                                            <dd class="text-sm text-gray-900">{{ order.silver_coins }} x €0.75</dd>
                                        </div>
                                        <div class="flex justify-between">
                                            <dt class="text-sm text-gray-600">Gouden Munten</dt>
                                            <dd class="text-sm text-gray-900">{{ order.gold_coins }} x €1.25</dd>
                                        </div>
                                        <div class="flex justify-between border-t border-gray-200 pt-2">
                                            <dt class="text-sm font-medium text-gray-900">Totaal</dt>
                                            <dd class="text-sm font-medium text-gray-900">€{{
                                                order.total_amount.toFixed(2) }}
                                            </dd>
                                        </div>
                                    </dl>
                                </div>
                                
                                <div v-if="order.pickup_moment" class="border-t border-gray-200 pt-4">
                                    <h5 class="text-sm font-medium text-gray-500">Afhaalmoment</h5>
                                    <p class="mt-2 text-sm text-gray-900">
                                        {{ formatPickupMoment(order.pickup_moment) }}
                                    </p>
                                </div>
                            </dl>
                        </div>

                        <div class="mt-8 flex justify-center">
                            <Link :href="route('dashboard')"
                                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Terug naar Dashboard
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
