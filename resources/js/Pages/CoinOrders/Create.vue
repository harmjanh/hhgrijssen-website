<script setup lang="ts">
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import NumberInput from '@/Components/NumberInput.vue';

interface Props {
    user: {
        name: string;
        email: string;
    };
    prices: {
        blue_coin: number;
        red_coin: number;
        payment_fee: number;
    };
}

const props = defineProps<Props>();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    blue_coins: 0,
    red_coins: 0,
});

const totalAmount = computed(() => {
    return (form.blue_coins * props.prices.blue_coin) +
        (form.red_coins * props.prices.red_coin) +
        props.prices.payment_fee;
});

const submit = () => {
    form.post(route('coin-orders.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Bestel Munten
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Personal Information -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Persoonlijke Informatie</h3>
                                <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    <div>
                                        <InputLabel for="name" value="Naam" />
                                        <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name"
                                            required />
                                        <InputError :message="form.errors.name" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="email" value="E-mailadres" />
                                        <TextInput id="email" type="email" class="mt-1 block w-full"
                                            v-model="form.email" required />
                                        <InputError :message="form.errors.email" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Coin Selection -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Selecteer Munten</h3>
                                <div class="mt-4 grid grid-cols-1 gap-6 sm:grid-cols-2">
                                    <div>
                                        <InputLabel for="blue_coins" value="Blauwe Munten (€0.60 per stuk)" />
                                        <NumberInput id="blue_coins" class="mt-1 block w-full" v-model="form.blue_coins"
                                            min="0" required />
                                        <InputError :message="form.errors.blue_coins" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="red_coins" value="Rode Munten (€0.90 per stuk)" />
                                        <NumberInput id="red_coins" class="mt-1 block w-full" v-model="form.red_coins"
                                            min="0" required />
                                        <InputError :message="form.errors.red_coins" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Order Summary -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900">Bestelling Overzicht</h3>
                                <dl class="mt-4 space-y-2">
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600">Blauwe Munten</dt>
                                        <dd class="text-gray-900">€{{ (form.blue_coins * prices.blue_coin).toFixed(2) }}
                                        </dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600">Rode Munten</dt>
                                        <dd class="text-gray-900">€{{ (form.red_coins * prices.red_coin).toFixed(2) }}
                                        </dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600">Betalingskosten</dt>
                                        <dd class="text-gray-900">€{{ prices.payment_fee.toFixed(2) }}</dd>
                                    </div>
                                    <div class="flex justify-between border-t border-gray-200 pt-2">
                                        <dt class="text-lg font-medium text-gray-900">Totaal</dt>
                                        <dd class="text-lg font-medium text-gray-900">€{{ totalAmount.toFixed(2) }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div class="flex items-center justify-end">
                                <PrimaryButton :disabled="form.processing">
                                    Bestelling Plaatsen
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
