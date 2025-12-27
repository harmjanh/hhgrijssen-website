<script setup lang="ts">
import { computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import NumberInput from '@/Components/NumberInput.vue';
import Select from '@/Components/Select.vue';

interface Props {
    user: {
        name: string;
        email: string;
    };
    prices: {
        silver_coin: number;
        gold_coin: number;
        payment_fee: number;
    };
    pickupMoments: Array<{
        id: number;
        date: string;
    }>;
}

const props = defineProps<Props>();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    silver_coins: 0,
    gold_coins: 0,
    pickup_moment_id: null as number | null,
});

const pickupMomentOptions = computed(() => {
    return props.pickupMoments.map(moment => {
        const date = new Date(moment.date).toLocaleDateString('nl-NL', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
        return {
            value: moment.id,
            label: `${date} om 10:00`
        };
    });
});

const totalAmount = computed(() => {
    return (form.silver_coins * props.prices.silver_coin) +
        (form.gold_coins * props.prices.gold_coin) +
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
            <PageHeader title="Bestel Munten" description="Bestel zilveren en gouden munten voor gebruik in de kerk" />
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
                                        <InputLabel for="silver_coins" value="Zilveren Munten (€0.75 per stuk)" />
                                        <NumberInput id="silver_coins" class="mt-1 block w-full" v-model="form.silver_coins"
                                            :min="0" required />
                                        <InputError :message="form.errors.silver_coins" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="gold_coins" value="Gouden Munten (€1.25 per stuk)" />
                                        <NumberInput id="gold_coins" class="mt-1 block w-full" v-model="form.gold_coins"
                                            :min="0" required />
                                        <InputError :message="form.errors.gold_coins" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Pickup Moment Selection -->
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Afhaalmoment</h3>
                                <div class="mt-4">
                                    <InputLabel for="pickup_moment_id" value="Selecteer afhaalmoment *" />
                                    <Select 
                                        id="pickup_moment_id" 
                                        class="mt-1 block w-full"
                                        v-model="form.pickup_moment_id"
                                        :options="pickupMomentOptions"
                                        required 
                                    />
                                    <InputError :message="form.errors.pickup_moment_id" class="mt-2" />
                                    <p class="mt-2 text-sm text-gray-500">
                                        Selecteer wanneer u de munten wilt ophalen.
                                    </p>
                                </div>
                            </div>

                            <!-- Order Summary -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h3 class="text-lg font-medium text-gray-900">Bestelling Overzicht</h3>
                                <dl class="mt-4 space-y-2">
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600">Zilveren Munten</dt>
                                        <dd class="text-gray-900">€{{ (form.silver_coins * prices.silver_coin).toFixed(2) }}
                                        </dd>
                                    </div>
                                    <div class="flex justify-between">
                                        <dt class="text-gray-600">Gouden Munten</dt>
                                        <dd class="text-gray-900">€{{ (form.gold_coins * prices.gold_coin).toFixed(2) }}
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
