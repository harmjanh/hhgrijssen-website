<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

interface Room {
    id: number;
    name: string;
    description?: string;
}

interface Reservation {
    id: number;
    subject: string;
    number_of_people: number;
    start_time: string;
    end_time: string;
    room: Room;
}

interface Props {
    reservations: Reservation[];
}

const props = defineProps<Props>();

const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleString('nl-NL', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
    });
};

const formatTimeRange = (startTime: string, endTime: string) => {
    const start = new Date(startTime).toLocaleString('nl-NL', {
        hour: '2-digit',
        minute: '2-digit',
    });
    const end = new Date(endTime).toLocaleString('nl-NL', {
        hour: '2-digit',
        minute: '2-digit',
    });
    return `${start} - ${end}`;
};

const deleteReservation = (reservationId: number) => {
    if (confirm('Weet je zeker dat je deze reservering wilt annuleren?')) {
        router.delete(route('room-reservations.destroy', reservationId), {
            preserveScroll: false,
        });
    }
};
</script>

<template>

    <Head title="Zaalreserveringen" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader title="Mijn Zaalreserveringen" description="Beheer je zaalreserveringen" />
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="$page.props.flash?.success" class="mb-6 rounded-lg bg-green-50 border border-green-200 p-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-green-700">
                                {{ $page.props.flash?.success }}
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-semibold">Komende Zaalreserveringen</h2>
                            <div class="flex space-x-3">
                                <Link :href="route('room-reservations.history')"
                                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:bg-gray-100">
                                    Historische Reserveringen
                                </Link>
                                <Link :href="route('room-reservations.create')">
                                    <PrimaryButton>
                                        Nieuwe Reservering
                                    </PrimaryButton>
                                </Link>
                            </div>
                        </div>

                        <div v-if="reservations.length === 0" class="text-center py-8">
                            <p class="text-gray-500 mb-4">Je hebt geen komende zaalreserveringen.</p>
                            <div class="flex justify-center space-x-3">
                                <Link :href="route('room-reservations.history')"
                                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:bg-gray-100">
                                    Bekijk Historische Reserveringen
                                </Link>
                                <Link :href="route('room-reservations.create')">
                                    <PrimaryButton>
                                        Maak je eerste reservering
                                    </PrimaryButton>
                                </Link>
                            </div>
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="reservation in reservations" :key="reservation.id"
                                class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50">
                                <div class="flex justify-between items-start">
                                    <div class="flex-1">
                                        <h3 class="text-lg font-medium text-gray-900">
                                            {{ reservation.subject }}
                                        </h3>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ reservation.room.name }}
                                        </p>
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ formatDate(reservation.start_time) }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ formatTimeRange(reservation.start_time, reservation.end_time) }}
                                        </p>
                                        <p class="text-sm text-gray-500">
                                            {{ reservation.number_of_people }} {{ reservation.number_of_people === 1 ?
                                                'persoon'
                                                : 'personen' }}
                                        </p>
                                    </div>
                                    <div class="flex space-x-2 ml-4 items-center">
                                        <Link :href="route('room-reservations.show', reservation.id)"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                        Bekijken
                                        </Link>
                                        <Link :href="route('room-reservations.edit', reservation.id)"
                                            class="text-green-600 hover:text-green-800 text-sm font-medium">
                                        Bewerken
                                        </Link>
                                        <button
                                            @click="deleteReservation(reservation.id)"
                                            class="text-red-600 hover:text-red-800 text-sm font-medium">
                                        Annuleren
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
