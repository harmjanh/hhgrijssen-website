<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

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
</script>

<template>
    <Head title="Historische Zaalreserveringen" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader title="Historische Zaalreserveringen" description="Overzicht van je oude zaalreserveringen" />
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-xl font-semibold">Historische Zaalreserveringen</h2>
                            <Link :href="route('room-reservations.index')"
                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-xs font-semibold uppercase tracking-widest text-gray-700 transition duration-150 ease-in-out hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:bg-gray-100">
                                Terug naar Komende Reserveringen
                            </Link>
                        </div>

                        <div v-if="reservations.length === 0" class="text-center py-8">
                            <p class="text-gray-500 mb-4">Je hebt geen historische zaalreserveringen.</p>
                            <Link :href="route('room-reservations.index')"
                                class="inline-flex items-center rounded-md border border-transparent bg-gray-800 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white transition duration-150 ease-in-out hover:bg-gray-700 focus:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 active:bg-gray-900">
                                Terug naar Komende Reserveringen
                            </Link>
                        </div>

                        <div v-else class="space-y-4">
                            <div v-for="reservation in reservations" :key="reservation.id"
                                class="border border-gray-200 rounded-lg p-4 hover:bg-gray-50 opacity-75">
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
                                    <div class="flex space-x-2 ml-4">
                                        <Link :href="route('room-reservations.show', reservation.id)"
                                            class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                                            Bekijken
                                        </Link>
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
