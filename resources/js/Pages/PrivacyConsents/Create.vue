<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import Checkbox from '@/Components/Checkbox.vue';

const props = defineProps<{
    user?: {
        name: string;
        street: string;
        number: string;
        zipcode: string;
        city: string;
    };
}>();

const form = useForm({
    name: props.user?.name ?? '',
    street: props.user?.street ?? '',
    zipcode: props.user?.zipcode ?? '',
    city: props.user?.city ?? '',
    birth_date: '',
    voorbede_eredienst: false,
    voorbede_zaaier: false,
    verjaardag_zaaier: false,
    rsv_gegevens: false,
    place: '',
    submission_date: new Date().toISOString().split('T')[0],
    agreed: false,
});

const submit = () => {
    form.post(route('privacy-consents.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Privacy Toestemmingsformulier" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader 
                title="Privacy Toestemmingsformulier"
                description="Vul het formulier in om uw toestemmingen voor privacy te geven" 
            />
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Information Section -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                            <h3 class="text-lg font-semibold mb-2 text-blue-900 dark:text-blue-100">Uw privacy</h3>
                            <p class="text-sm text-blue-800 dark:text-blue-200 mb-2">
                                Als lid van onze gemeente worden gegevens van u vastgelegd in de kerkelijke administratie. 
                                Deze gegevens worden opgeslagen conform de regels van de Algemene Verordening Gegevensbescherming (AVG).
                            </p>
                            <p class="text-sm text-blue-800 dark:text-blue-200 mb-2">
                                U kunt op ieder moment een weergave van uw gegevens (de zogeheten persoonskaart) opvragen bij het kerkelijk bureau. 
                                Onjuistheden en wijzigingen, zoals een adreswijziging, kunt u doorgeven aan het kerkelijk bureau.
                            </p>
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                <strong>Belangrijk:</strong> Wij zijn verplicht er standaard vanuit te gaan dat u geen toestemming geeft, 
                                tenzij u een of meerdere vragen met 'wel toestemming' hebt bevestigd.
                            </p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Personal Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Persoonlijke Informatie</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <InputLabel for="name" value="Naam *" />
                                        <TextInput 
                                            id="name" 
                                            type="text" 
                                            class="mt-1 block w-full" 
                                            v-model="form.name"
                                            required 
                                        />
                                        <InputError :message="form.errors.name" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="birth_date" value="Geboortedatum *" />
                                        <TextInput 
                                            id="birth_date" 
                                            type="date" 
                                            class="mt-1 block w-full"
                                            v-model="form.birth_date" 
                                            required 
                                        />
                                        <InputError :message="form.errors.birth_date" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Address Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Adresgegevens</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <InputLabel for="street" value="Straat/huisnummer *" />
                                        <TextInput 
                                            id="street" 
                                            type="text" 
                                            class="mt-1 block w-full"
                                            v-model="form.street" 
                                            required 
                                        />
                                        <InputError :message="form.errors.street" class="mt-2" />
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <InputLabel for="zipcode" value="Postcode *" />
                                            <TextInput 
                                                id="zipcode" 
                                                type="text" 
                                                class="mt-1 block w-full"
                                                v-model="form.zipcode" 
                                                required 
                                            />
                                            <InputError :message="form.errors.zipcode" class="mt-2" />
                                        </div>

                                        <div>
                                            <InputLabel for="city" value="Woonplaats *" />
                                            <TextInput 
                                                id="city" 
                                                type="text" 
                                                class="mt-1 block w-full"
                                                v-model="form.city" 
                                                required 
                                            />
                                            <InputError :message="form.errors.city" class="mt-2" />
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Consent Options -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Toestemmingen</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                                    Geef aan voor welke doelen we uw gegevens in de toekomst mogen gebruiken.
                                </p>
                                
                                <div class="space-y-4">
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <Checkbox 
                                                id="voorbede_eredienst" 
                                                :checked="form.voorbede_eredienst"
                                                @update:checked="form.voorbede_eredienst = $event"
                                                class="mt-1"
                                            />
                                        </div>
                                        <div class="ml-3">
                                            <InputLabel for="voorbede_eredienst" class="font-medium">
                                                Bij aanvraag van voorbede en/of dankzegging (in geval van geboorte, jubileum, ziekte enz.) 
                                                vermelding van naam en adres tijdens de eredienst.
                                            </InputLabel>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <Checkbox 
                                                id="voorbede_zaaier" 
                                                :checked="form.voorbede_zaaier"
                                                @update:checked="form.voorbede_zaaier = $event"
                                                class="mt-1"
                                            />
                                        </div>
                                        <div class="ml-3">
                                            <InputLabel for="voorbede_zaaier" class="font-medium">
                                                Bij aanvraag van voorbede en/of dankzegging (in geval van geboorte, jubileum, ziekte enz.) 
                                                publicatie van naam en adres in De Zaaier.
                                            </InputLabel>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <Checkbox 
                                                id="verjaardag_zaaier" 
                                                :checked="form.verjaardag_zaaier"
                                                @update:checked="form.verjaardag_zaaier = $event"
                                                class="mt-1"
                                            />
                                        </div>
                                        <div class="ml-3">
                                            <InputLabel for="verjaardag_zaaier" class="font-medium">
                                                Bij verjaardag van leden die 75 jaar of ouder zijn, publicatie van naam en adres in De Zaaier.
                                            </InputLabel>
                                        </div>
                                    </div>

                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <Checkbox 
                                                id="rsv_gegevens" 
                                                :checked="form.rsv_gegevens"
                                                @update:checked="form.rsv_gegevens = $event"
                                                class="mt-1"
                                            />
                                        </div>
                                        <div class="ml-3">
                                            <InputLabel for="rsv_gegevens" class="font-medium">
                                                De Reformatorische School Vereniging voor de opgave van kinderen die de leerplichtige leeftijd bereiken.
                                            </InputLabel>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Place and Date -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <InputLabel for="place" value="Plaats *" />
                                        <TextInput 
                                            id="place" 
                                            type="text" 
                                            class="mt-1 block w-full"
                                            v-model="form.place" 
                                            required 
                                        />
                                        <InputError :message="form.errors.place" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="submission_date" value="Datum *" />
                                        <TextInput 
                                            id="submission_date" 
                                            type="date" 
                                            class="mt-1 block w-full"
                                            v-model="form.submission_date" 
                                            required 
                                        />
                                        <InputError :message="form.errors.submission_date" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Agreement Checkbox -->
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-4">
                                <div class="flex items-start">
                                    <div class="flex items-center h-5">
                                        <Checkbox 
                                            id="agreed" 
                                            :checked="form.agreed"
                                            @update:checked="form.agreed = $event"
                                            class="mt-1"
                                        />
                                    </div>
                                    <div class="ml-3">
                                        <InputLabel for="agreed" class="font-medium">
                                            Ik verklaar dat de bovenstaande gegevens correct zijn en geef toestemming voor de aangegeven doelen. *
                                        </InputLabel>
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                            Door dit vinkje aan te vinken, bevestigt u dat u akkoord gaat met de voorwaarden.
                                        </p>
                                    </div>
                                </div>
                                <InputError :message="form.errors.agreed" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <PrimaryButton class="ml-4" :disabled="form.processing">
                                    Formulier Indienen
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>


