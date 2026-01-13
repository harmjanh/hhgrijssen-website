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
    reservation: Reservation;
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

const deleteReservation = () => {
    if (confirm('Weet je zeker dat je deze reservering wilt annuleren?')) {
        router.delete(route('room-reservations.destroy', props.reservation.id), {
            preserveScroll: false,
        });
    }
};
</script>

<template>

    <Head title="Zaalreservering Details" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader title="Zaalreservering Details" description="Bekijk de details van je zaalreservering" />
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
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
                        <div class="space-y-6">
                            <!-- Reservation Details -->
                            <div>
                                <h2 class="text-lg font-medium text-gray-900 mb-4">Reservering Details</h2>
                                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Onderwerp</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ reservation.subject }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Zaal</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ reservation.room.name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Datum</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ formatDate(reservation.start_time) }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Tijd</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{
                                            formatTimeRange(reservation.start_time,
                                                reservation.end_time) }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Aantal personen</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ reservation.number_of_people }} {{ reservation.number_of_people === 1 ?
                                                'persoon'
                                                : 'personen' }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>

                            <!-- Room Description -->
                            <div v-if="reservation.room.description">
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Zaal Informatie</h3>
                                <p class="text-sm text-gray-600">{{ reservation.room.description }}</p>
                            </div>

                            <!-- Action Buttons -->
                            <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                                <div class="flex space-x-4">
                                    <Link :href="route('room-reservations.index')"
                                        class="text-gray-600 hover:text-gray-800">
                                    ← Terug naar overzicht
                                    </Link>
                                </div>
                                <div class="flex space-x-3">
                                    <Link :href="route('room-reservations.edit', reservation.id)">
                                    <PrimaryButton>
                                        Bewerken
                                    </PrimaryButton>
                                    </Link>
                                    <DangerButton @click="deleteReservation">
                                        Annuleren
                                    </DangerButton>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
