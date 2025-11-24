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
}>({
    room_id: '',
    subject: '',
    number_of_people: 1,
    start_time: '',
    end_time: '',
});

const availableRooms = ref<Room[]>(props.rooms);
const loading = ref(false);

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
                                    class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
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
