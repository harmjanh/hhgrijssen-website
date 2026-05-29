<script setup lang="ts">
import { computed } from 'vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import type { PageProps } from '@/types';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import NavBar from '@/Components/NavBar.vue';
import PageFooter from '@/Components/PageFooter.vue';

const props = defineProps<{
    pages: Array<{ id: number; title: string; slug: string }>;
    prices: {
        snoeprollen: number;
        stroopwafels: number;
        payment_fee: number;
    };
    units: {
        snoeprollen_per_order: number;
        stroopwafels_packages_per_order: number;
    };
}>();

const form = useForm({
    name: '',
    email: '',
    phone: '',
    snoeprollen_quantity: 0,
    stroopwafels_quantity: 0,
    remarks: '',
    website: '',
});

const page = usePage<PageProps>();

const snoeprollenTotal = computed(() => form.snoeprollen_quantity * props.prices.snoeprollen);
const stroopwafelsTotal = computed(() => form.stroopwafels_quantity * props.prices.stroopwafels);
const productTotal = computed(() => snoeprollenTotal.value + stroopwafelsTotal.value);
const grandTotal = computed(() => productTotal.value + props.prices.payment_fee);
const canSubmit = computed(() => productTotal.value > 0);

const formatPrice = (amount: number) =>
    new Intl.NumberFormat('nl-NL', { style: 'currency', currency: 'EUR' }).format(amount);

const submit = () => {
    form.post(route('treat-orders.store'), {
        preserveScroll: true,
    });
};
</script>

<template>

    <Head title="Snoep- en stroopwafelactie" />

    <NavBar :pages="pages" />

    <div class="mx-auto max-w-3xl px-6 py-12 lg:px-8">
        <h1 class="mb-6 text-3xl font-bold tracking-tight text-gray-900 text-center">
            Snoep- en stroopwafelactie
        </h1>

        <div class="mb-10 space-y-6 text-gray-700 leading-relaxed">
            <p>
                Iets lekkers voor bij de koffie, voor in de auto of om uit te delen — én meteen een goede
                aanleiding om elkaar weer te ontmoeten op de jeugd- en gemeentedag. Bestellen kan tot en met
                <strong>6 juli</strong>, ophalen op <strong>29 augustus</strong> in Markelo.
            </p>

            <div>
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Dit kun je bestellen</h2>
                <div class="grid gap-4 sm:grid-cols-2">
                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <h3 class="font-semibold text-gray-900">
                            {{ units.snoeprollen_per_order }} snoeprollen — {{ formatPrice(prices.snoeprollen) }}
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Een hele zak vol. Lekker thuis, op verjaardagen of om iemand mee te verrassen.
                        </p>
                    </div>
                    <div class="rounded-lg border border-gray-200 bg-white p-5 shadow-sm">
                        <h3 class="font-semibold text-gray-900">
                            {{ units.stroopwafels_packages_per_order }} pakjes Markus stroopwafel — {{
                                formatPrice(prices.stroopwafels) }}
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Vers van Markus uit Gouda — bekende kwaliteit voor bij de koffie of thee.
                        </p>
                    </div>
                </div>
            </div>

            <div class="rounded-xl bg-amber-50 border border-amber-200 p-6">
                <h2 class="text-xl font-semibold text-amber-900 mb-2">Wanneer haal je het op?</h2>
                <p>
                    Op zaterdag <strong>29 augustus</strong> tijdens de jeugd- en gemeentedag,
                    <strong>Plasdijk 18</strong> in Markelo. Een gezellige ochtend voor de hele gemeente,
                    kom meteen even langs voor een kop koffie of een lekkere pannenkoek!
                </p>
            </div>

            <p class="text-sm text-gray-600">
                Na het plaatsen van uw bestelling betaalt u veilig online via iDEAL of een andere betaalmethode.
            </p>
        </div>

        <div v-if="page.props.errors?.error" class="mb-6 rounded-lg bg-red-50 border border-red-200 p-4">
            <p class="text-sm text-red-700">{{ page.props.errors.error }}</p>
        </div>

        <form @submit.prevent="submit" class="space-y-8">
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Uw gegevens</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <InputLabel for="name" value="Naam *" />
                        <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="email" value="E-mailadres *" />
                        <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>
                    <div class="sm:col-span-2">
                        <InputLabel for="phone" value="Telefoonnummer *" />
                        <TextInput id="phone" type="tel" class="mt-1 block w-full" v-model="form.phone" required />
                        <InputError :message="form.errors.phone" class="mt-2" />
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Uw bestelling</h2>
                <p class="mb-4 text-sm text-gray-600">
                    Geef per product aan hoeveel bestellingen u wilt (bijv. 2 = twee keer het pakket).
                </p>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <InputLabel for="snoeprollen_quantity"
                            :value="`Snoeprollen (${units.snoeprollen_per_order} stuks, ${formatPrice(prices.snoeprollen)} per bestelling)`" />
                        <TextInput id="snoeprollen_quantity" type="number" min="0" max="99" class="mt-1 block w-full"
                            v-model.number="form.snoeprollen_quantity" required />
                        <InputError :message="form.errors.snoeprollen_quantity" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="stroopwafels_quantity"
                            :value="`Markus stroopwafel (${units.stroopwafels_packages_per_order} pakjes, ${formatPrice(prices.stroopwafels)} per bestelling)`" />
                        <TextInput id="stroopwafels_quantity" type="number" min="0" max="99" class="mt-1 block w-full"
                            v-model.number="form.stroopwafels_quantity" required />
                        <InputError :message="form.errors.stroopwafels_quantity" class="mt-2" />
                    </div>
                </div>
            </div>

            <div>
                <InputLabel for="remarks" value="Opmerkingen (optioneel)" />
                <TextArea id="remarks" class="mt-1 block w-full" v-model="form.remarks" rows="3" />
                <InputError :message="form.errors.remarks" class="mt-2" />
            </div>

            <!-- Honeypot -->
            <div class="hidden" aria-hidden="true">
                <InputLabel for="website" value="Website" />
                <TextInput id="website" type="text" v-model="form.website" tabindex="-1" autocomplete="off" />
            </div>

            <div class="rounded-lg bg-gray-50 border border-gray-200 p-4">
                <div class="flex justify-between text-sm text-gray-600" v-if="form.snoeprollen_quantity > 0">
                    <span>Snoeprollen ({{ form.snoeprollen_quantity }}×)</span>
                    <span>{{ formatPrice(snoeprollenTotal) }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600 mt-1" v-if="form.stroopwafels_quantity > 0">
                    <span>Markus stroopwafel ({{ form.stroopwafels_quantity }}×)</span>
                    <span>{{ formatPrice(stroopwafelsTotal) }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600 mt-1">
                    <span>Betaalkosten</span>
                    <span>{{ formatPrice(prices.payment_fee) }}</span>
                </div>
                <div
                    class="flex justify-between text-lg font-semibold text-gray-900 mt-3 pt-3 border-t border-gray-200">
                    <span>Totaal</span>
                    <span>{{ formatPrice(grandTotal) }}</span>
                </div>
            </div>

            <div class="flex justify-end">
                <PrimaryButton :disabled="form.processing || !canSubmit">
                    Naar betaling
                </PrimaryButton>
            </div>
        </form>
    </div>

    <PageFooter :pages="pages" />
</template>
