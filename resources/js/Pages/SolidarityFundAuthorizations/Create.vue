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
    initials: '',
    street: props.user?.street ?? '',
    zipcode: props.user?.zipcode ?? '',
    city: props.user?.city ?? '',
    iban: '',
    submission_date: new Date().toISOString().split('T')[0],
    agreed: false,
});

const normalizeIban = (value: string) => {
    return value.toUpperCase().replace(/\s/g, '');
};

const submit = () => {
    // Normalize IBAN before submission
    form.iban = normalizeIban(form.iban);
    
    form.post(route('solidarity-fund-authorizations.store'), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Machtiging Solidariteitsfonds" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader 
                title="Machtiging Solidariteitsfonds"
                description="Vul het formulier in om een machtiging voor automatische incasso van het Solidariteitsfonds af te geven" 
            />
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <!-- Information Section -->
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                            <h3 class="text-lg font-semibold mb-2 text-blue-900 dark:text-blue-100">Wat is het Solidariteitsfonds?</h3>
                            <p class="text-sm text-blue-800 dark:text-blue-200 mb-2">
                                Onze kerk moet jaarlijks een vast bedrag per lid afdragen aan de landelijke kerk t.b.v. de stichting Predikantstraktementen. 
                                De hoogte van deze heffing wordt jaarlijks vastgesteld. Op dit moment is deze heffing €27,50 per persoon voor alle leden van 18 jaar en ouder (belijdende en doopleden).
                            </p>
                            <p class="text-sm text-blue-800 dark:text-blue-200 mb-2">
                                Inning van deze heffing is alleen mogelijk via automatische incasso. Wij vragen u daarvoor een machtiging af te geven. 
                                Inning vindt jaarlijks plaats in november.
                            </p>
                            <p class="text-sm text-blue-800 dark:text-blue-200">
                                <strong>Belangrijk:</strong> Indien het lidmaatschap van de kerkelijke gemeente wordt stopgezet, wordt de incasso ook automatisch stopgezet.
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
                                        <InputLabel for="initials" value="Voorletters *" />
                                        <TextInput 
                                            id="initials" 
                                            type="text" 
                                            class="mt-1 block w-full"
                                            v-model="form.initials" 
                                            required 
                                        />
                                        <InputError :message="form.errors.initials" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Address Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Adresgegevens</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <InputLabel for="street" value="Adres *" />
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

                            <!-- Bank Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Bankgegevens</h3>
                                <div>
                                    <InputLabel for="iban" value="IBAN *" />
                                    <TextInput 
                                        id="iban" 
                                        type="text" 
                                        class="mt-1 block w-full uppercase"
                                        v-model="form.iban"
                                        @input="(e: Event) => form.iban = normalizeIban((e.target as HTMLInputElement).value)"
                                        placeholder="NL91ABNA0417164300"
                                        required 
                                    />
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                        Het IBAN dient op naam van de geadresseerde te staan.
                                    </p>
                                    <InputError :message="form.errors.iban" class="mt-2" />
                                </div>
                            </div>

                            <!-- Date -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
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
                                            Ik geef hierbij tot wederopzegging toestemming aan de Kerkvoogdij van de Hersteld Hervormde Gemeente te Rijssen 
                                            om de bijdrage voor het Solidariteitsfonds jaarlijks via automatische incasso te innen. *
                                        </InputLabel>
                                        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                                            Door dit vinkje aan te vinken, geeft u toestemming voor de automatische incasso van het Solidariteitsfonds.
                                        </p>
                                    </div>
                                </div>
                                <InputError :message="form.errors.agreed" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end mt-6">
                                <PrimaryButton class="ml-4" :disabled="form.processing">
                                    Machtiging Indienen
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

