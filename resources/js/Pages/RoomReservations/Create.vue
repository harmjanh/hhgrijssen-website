<script setup lang="ts">
import { ref, watch } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import NumberInput from '@/Components/NumberInput.vue';
import axios from 'axios';

interface Room {
    id: number;
    name: string;
    description?: string;
}

interface Props {
    rooms: Room[];
}

const props = defineProps<Props>();

const form = useForm<{
    room_id: string | number;
    subject: string;
    number_of_people: number;
    start_time: string;
    end_time: string;
    coffee_needed: boolean | null;
    has_break: boolean | null;
    beamer_needed: boolean | null;
    guest_speaker: boolean | null;
    broadcast_needed: boolean | null;
    other_remarks: string;
}>({
    room_id: '',
    subject: '',
    number_of_people: 1,
    start_time: '',
    end_time: '',
    coffee_needed: null,
    has_break: null,
    beamer_needed: null,
    guest_speaker: null,
    broadcast_needed: null,
    other_remarks: '',
});

const availableRooms = ref<Room[]>(props.rooms);
const loading = ref(false);

const validateExtraFields = () => {
    form.clearErrors(
        'coffee_needed',
        'has_break',
        'beamer_needed',
        'guest_speaker',
        'broadcast_needed',
        'other_remarks',
    );

    const errors: Record<string, string> = {};

    if (form.coffee_needed === null) {
        errors.coffee_needed = 'Geef aan of er koffie geregeld moet worden.';
    }

    if (form.has_break === null) {
        errors.has_break = 'Geef aan of er een pauze is.';
    }

    if (form.beamer_needed === null) {
        errors.beamer_needed = 'Geef aan of de beamer nodig is.';
    }

    if (form.guest_speaker === null) {
        errors.guest_speaker = 'Geef aan of er een gastspreker is.';
    }

    if (form.broadcast_needed === null) {
        errors.broadcast_needed = 'Geef aan of er een uitzending moet zijn.';
    }

    if (!form.other_remarks.trim()) {
        errors.other_remarks = 'Overige opmerkingen zijn verplicht.';
    }

    if (Object.keys(errors).length > 0) {
        form.setError(errors);
        return false;
    }

    return true;
};

// Watch for changes in start_time and end_time to fetch available rooms
watch([() => form.start_time, () => form.end_time], async ([newStartTime, newEndTime]) => {
    if (newStartTime && newEndTime) {
        loading.value = true;
        try {
            const response = await axios.get(route('room-reservations.available-rooms'), {
                params: {
                    start_time: newStartTime,
                    end_time: newEndTime
                }
            });
            availableRooms.value = response.data.available_rooms;
            // Reset room selection if the selected room is no longer available
            if (form.room_id && !availableRooms.value.find(room => room.id === Number(form.room_id))) {
                form.room_id = '';
            }
        } catch (error) {
            console.error('Error fetching available rooms:', error);
        } finally {
            loading.value = false;
        }
    } else {
        availableRooms.value = props.rooms;
    }
});

const submit = () => {
    if (!validateExtraFields()) {
        return;
    }

    form.post(route('room-reservations.store'), {
        preserveScroll: true,
    });
};

const getMinDateTime = () => {
    const now = new Date();
    now.setMinutes(now.getMinutes() + 1); // Add 1 minute to avoid "after:now" validation issues
    return now.toISOString().slice(0, 16);
};
</script>

<template>

    <Head title="Nieuwe Zaalreservering" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader title="Nieuwe Zaalreservering" description="Reserveer een zaal in de kerk" />
        </template>

        <div class="py-12">
            <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Start Time -->
                            <div>
                                <InputLabel for="start_time" value="Starttijd" />
                                <TextInput id="start_time" v-model="form.start_time" type="datetime-local"
                                    :min="getMinDateTime()" class="mt-1 block w-full" required />
                                <InputError class="mt-2" :message="form.errors.start_time" />
                            </div>

                            <!-- End Time -->
                            <div>
                                <InputLabel for="end_time" value="Eindtijd" />
                                <TextInput id="end_time" v-model="form.end_time" type="datetime-local"
                                    :min="form.start_time || getMinDateTime()" class="mt-1 block w-full" required />
                                <InputError class="mt-2" :message="form.errors.end_time" />
                            </div>

                            <!-- Room Selection -->
                            <div>
                                <InputLabel for="room_id" value="Zaal" />
                                <select id="room_id" v-model="form.room_id"
                                    class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm"
                                    :disabled="loading || availableRooms.length === 0" required>
                                    <option value="">Selecteer een zaal</option>
                                    <option v-for="room in availableRooms" :key="room.id" :value="room.id">
                                        {{ room.name }}
                                    </option>
                                </select>
                                <div v-if="loading" class="mt-1 text-sm text-gray-500">
                                    Beschikbare zalen worden geladen...
                                </div>
                                <div v-else-if="availableRooms.length === 0 && form.start_time && form.end_time"
                                    class="mt-1 text-sm text-red-500">
                                    Geen zalen beschikbaar in de geselecteerde tijdsperiode.
                                </div>
                                <InputError class="mt-2" :message="form.errors.room_id" />
                            </div>

                            <!-- Subject -->
                            <div>
                                <InputLabel for="subject" value="Onderwerp" />
                                <TextInput id="subject" v-model="form.subject" type="text" class="mt-1 block w-full"
                                    required />
                                <InputError class="mt-2" :message="form.errors.subject" />
                            </div>

                            <!-- Number of People -->
                            <div>
                                <InputLabel for="number_of_people" value="Aantal personen" />
                                <NumberInput id="number_of_people" v-model="form.number_of_people"
                                    class="mt-1 block w-full" :min="1" :max="100" required />
                                <InputError class="mt-2" :message="form.errors.number_of_people" />
                            </div>

                            <!-- Coffee -->
                            <div>
                                <InputLabel value="Moet er koffie geregeld worden?" />
                                <div class="mt-2 flex gap-6">
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="form.coffee_needed" :value="true"
                                            class="border-gray-300 text-primary-600 focus:ring-primary-500">
                                        <span class="ml-2 text-sm text-gray-700">Ja</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="form.coffee_needed" :value="false"
                                            class="border-gray-300 text-primary-600 focus:ring-primary-500">
                                        <span class="ml-2 text-sm text-gray-700">Nee</span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.coffee_needed" />
                            </div>

                            <!-- Break -->
                            <div>
                                <InputLabel value="Is er pauze?" />
                                <div class="mt-2 flex gap-6">
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="form.has_break" :value="true"
                                            class="border-gray-300 text-primary-600 focus:ring-primary-500">
                                        <span class="ml-2 text-sm text-gray-700">Ja</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="form.has_break" :value="false"
                                            class="border-gray-300 text-primary-600 focus:ring-primary-500">
                                        <span class="ml-2 text-sm text-gray-700">Nee</span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.has_break" />
                            </div>

                            <!-- Beamer -->
                            <div>
                                <InputLabel value="Is de beamer nodig?" />
                                <div class="mt-2 flex gap-6">
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="form.beamer_needed" :value="true"
                                            class="border-gray-300 text-primary-600 focus:ring-primary-500">
                                        <span class="ml-2 text-sm text-gray-700">Ja</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="form.beamer_needed" :value="false"
                                            class="border-gray-300 text-primary-600 focus:ring-primary-500">
                                        <span class="ml-2 text-sm text-gray-700">Nee</span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.beamer_needed" />
                            </div>

                            <!-- Guest speaker -->
                            <div>
                                <InputLabel value="Is er een gastspreker?" />
                                <div class="mt-2 flex gap-6">
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="form.guest_speaker" :value="true"
                                            class="border-gray-300 text-primary-600 focus:ring-primary-500">
                                        <span class="ml-2 text-sm text-gray-700">Ja</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="form.guest_speaker" :value="false"
                                            class="border-gray-300 text-primary-600 focus:ring-primary-500">
                                        <span class="ml-2 text-sm text-gray-700">Nee</span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.guest_speaker" />
                            </div>

                            <!-- Broadcast -->
                            <div>
                                <InputLabel value="Moet er een uitzending zijn?" />
                                <div class="mt-2 flex gap-6">
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="form.broadcast_needed" :value="true"
                                            class="border-gray-300 text-primary-600 focus:ring-primary-500">
                                        <span class="ml-2 text-sm text-gray-700">Ja</span>
                                    </label>
                                    <label class="inline-flex items-center">
                                        <input type="radio" v-model="form.broadcast_needed" :value="false"
                                            class="border-gray-300 text-primary-600 focus:ring-primary-500">
                                        <span class="ml-2 text-sm text-gray-700">Nee</span>
                                    </label>
                                </div>
                                <InputError class="mt-2" :message="form.errors.broadcast_needed" />
                            </div>

                            <!-- Other remarks -->
                            <div>
                                <InputLabel for="other_remarks" value="Overige opmerkingen" />
                                <textarea id="other_remarks" v-model="form.other_remarks" rows="4"
                                    class="mt-1 block w-full border-gray-300 focus:border-primary-500 focus:ring-primary-500 rounded-md shadow-sm"
                                    required></textarea>
                                <InputError class="mt-2" :message="form.errors.other_remarks" />
                            </div>

                            <!-- Submit Button -->
                            <div class="flex items-center justify-end space-x-4">
                                <Link :href="route('room-reservations.index')"
                                    class="text-gray-600 hover:text-gray-800">
                                Annuleren
                                </Link>
                                <PrimaryButton :class="{ 'opacity-25': form.processing }"
                                    :disabled="form.processing || loading || availableRooms.length === 0">
                                    Reservering Aanmaken
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
