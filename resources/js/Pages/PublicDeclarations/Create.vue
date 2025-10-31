<script setup lang="ts">
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const form = useForm({
    name: '',
    email: '',
    street: '',
    number: '',
    zipcode: '',
    city: '',
    date_of_service: '',
    time_of_service_1: '',
    time_of_service_2: '',
    kilometers: '0',
});

// Pricing constants
const TIMESLOT_PRICE = 130.00;
const KILOMETER_PRICE = 0.35;

// Calculate the number of timeslots (1 if only time_of_service_1, 2 if both are filled)
const numberOfTimeslots = computed(() => {
    return form.time_of_service_1 && form.time_of_service_2 ? 2 : (form.time_of_service_1 ? 1 : 0);
});

// Calculate timeslot total
const timeslotTotal = computed(() => {
    return numberOfTimeslots.value * TIMESLOT_PRICE;
});

// Calculate kilometer total
const kilometerTotal = computed(() => {
    return (parseInt(form.kilometers) || 0) * KILOMETER_PRICE;
});

// Calculate grand total
const grandTotal = computed(() => {
    return timeslotTotal.value + kilometerTotal.value;
});

const submit = () => {
    form.post(route('public-declarations.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AppLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Declaratie Indienen
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-none mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="mb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Declaratie predikant</h3>
                            <p class="text-sm text-gray-600">
                                Vul het onderstaande formulier in om een declaratie in te dienen voor uw preekbeurt.
                                U ontvangt een bevestigingsmail na het indienen.
                            </p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Personal Information -->
                            <div>
                                <h4 class="text-md font-medium text-gray-900 mb-4">Persoonlijke Gegevens</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div>
                                        <InputLabel for="name" value="Naam *" />
                                        <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name"
                                            required />
                                        <InputError :message="form.errors.name" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="email" value="E-mailadres *" />
                                        <TextInput id="email" type="email" class="mt-1 block w-full"
                                            v-model="form.email" required />
                                        <InputError :message="form.errors.email" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="street" value="Straat *" />
                                        <TextInput id="street" type="text" class="mt-1 block w-full"
                                            v-model="form.street" required />
                                        <InputError :message="form.errors.street" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="number" value="Huisnummer *" />
                                        <TextInput id="number" type="text" class="mt-1 block w-full"
                                            v-model="form.number" required />
                                        <InputError :message="form.errors.number" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="zipcode" value="Postcode *" />
                                        <TextInput id="zipcode" type="text" class="mt-1 block w-full"
                                            v-model="form.zipcode" required />
                                        <InputError :message="form.errors.zipcode" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="city" value="Plaats *" />
                                        <TextInput id="city" type="text" class="mt-1 block w-full" v-model="form.city"
                                            required />
                                        <InputError :message="form.errors.city" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Service Information -->
                            <div>
                                <h4 class="text-md font-medium text-gray-900 mb-4">Dienst Informatie</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                                    <div>
                                        <InputLabel for="date_of_service" value="Datum van dienst *" />
                                        <TextInput id="date_of_service" type="date" class="mt-1 block w-full"
                                            v-model="form.date_of_service" required />
                                        <InputError :message="form.errors.date_of_service" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="time_of_service_1" value="Tijd van dienst 1 *" />
                                        <TextInput id="time_of_service_1" type="time" class="mt-1 block w-full"
                                            v-model="form.time_of_service_1" required />
                                        <InputError :message="form.errors.time_of_service_1" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="time_of_service_2" value="Tijd van dienst 2 (optioneel)" />
                                        <TextInput id="time_of_service_2" type="time" class="mt-1 block w-full"
                                            v-model="form.time_of_service_2" />
                                        <InputError :message="form.errors.time_of_service_2" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="kilometers" value="Aantal kilometers *" />
                                        <TextInput id="kilometers" type="number" min="0" class="mt-1 block w-full"
                                            v-model.number="form.kilometers" required />
                                        <InputError :message="form.errors.kilometers" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Calculation Summary -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-md font-medium text-gray-900 mb-4">Declaratie Overzicht</h4>
                                <div class="space-y-2">
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Aantal diensten:</span>
                                        <span class="font-medium">{{ numberOfTimeslots }} x €{{
                                            TIMESLOT_PRICE.toFixed(2)
                                            }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Totaal diensten:</span>
                                        <span class="font-medium">€{{ timeslotTotal.toFixed(2) }}</span>
                                    </div>
                                    <div class="flex justify-between">
                                        <span class="text-gray-600">Kilometers ({{ form.kilometers }} km):</span>
                                        <span class="font-medium">€{{ kilometerTotal.toFixed(2) }}</span>
                                    </div>
                                    <div class="flex justify-between border-t border-gray-200 pt-2">
                                        <span class="text-lg font-medium text-gray-900">Totaal declaratie:</span>
                                        <span class="text-lg font-medium text-gray-900">€{{ grandTotal.toFixed(2)
                                            }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-end">
                                <PrimaryButton class="ml-4" :disabled="form.processing">
                                    Declaratie Indienen
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        </GuestLayout>
</template>
