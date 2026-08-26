<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import NavBar from '@/Components/NavBar.vue';
import PageFooter from '@/Components/PageFooter.vue';

type GroupOption = {
    value: string;
    time: string;
    age: string;
};

type GroupDay = {
    day: string;
    options: GroupOption[];
};

defineProps<{
    pages: Array<{ id: number; title: string; slug: string }>;
    season: string;
    groups: GroupDay[];
}>();

const form = useForm({
    first_name: '',
    last_name: '',
    email: '',
    phone: '',
    group: '',
    website: '',
});

const submit = () => {
    form.post(route('catechesis-registrations.store'), {
        preserveScroll: true,
    });
};
</script>

<template>

    <Head title="Inschrijven catechisatie" />

    <NavBar :pages="pages" />

    <div class="mx-auto max-w-3xl px-6 py-12 lg:px-8">
        <h1 class="mb-2 text-3xl font-bold tracking-tight text-gray-900 text-center">
            Inschrijven catechisatie
        </h1>
        <p class="mb-10 text-center text-gray-600">
            Seizoen {{ season }}
        </p>

        <div class="mb-10 space-y-4 text-gray-700 leading-relaxed">
            <p>
                Schrijf je in voor catechisatie door onderstaand formulier in te vullen.
                Kies de groep die bij je leeftijd past.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-8">
            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-4">Gegevens</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <InputLabel for="first_name" value="Voornaam *" />
                        <TextInput id="first_name" type="text" class="mt-1 block w-full" v-model="form.first_name"
                            autocomplete="given-name" required />
                        <InputError :message="form.errors.first_name" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="last_name" value="Achternaam *" />
                        <TextInput id="last_name" type="text" class="mt-1 block w-full" v-model="form.last_name"
                            autocomplete="family-name" required />
                        <InputError :message="form.errors.last_name" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="email" value="E-mailadres *" />
                        <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email"
                            autocomplete="email" required />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>
                    <div>
                        <InputLabel for="phone" value="Telefoonnummer *" />
                        <TextInput id="phone" type="tel" class="mt-1 block w-full" v-model="form.phone"
                            autocomplete="tel" required />
                        <InputError :message="form.errors.phone" class="mt-2" />
                    </div>
                </div>
            </div>

            <div>
                <h2 class="text-lg font-medium text-gray-900 mb-2">Groep *</h2>
                <p class="mb-4 text-sm text-gray-600">
                    Kies één groep. Per persoon is één inschrijving nodig.
                </p>

                <div class="space-y-8">
                    <fieldset v-for="dayGroup in groups" :key="dayGroup.day" class="space-y-3">
                        <legend class="text-base font-semibold text-gray-900">
                            {{ dayGroup.day }}
                        </legend>
                        <label v-for="option in dayGroup.options" :key="option.value"
                            class="flex cursor-pointer items-start gap-3 rounded-lg border p-4 transition" :class="form.group === option.value
                                ? 'border-gray-800 bg-gray-50 ring-1 ring-gray-800'
                                : 'border-gray-200 bg-white hover:border-gray-400'">
                            <input type="radio" name="group"
                                class="mt-1 h-4 w-4 border-gray-300 text-gray-800 focus:ring-gray-800"
                                :value="option.value" v-model="form.group" required />
                            <span>
                                <span class="block font-medium text-gray-900">
                                    {{ option.time }}
                                </span>
                                <span class="block text-sm text-gray-600">
                                    {{ option.age }}
                                </span>
                            </span>
                        </label>
                    </fieldset>
                </div>
                <InputError :message="form.errors.group" class="mt-3" />
            </div>

            <div class="hidden" aria-hidden="true">
                <InputLabel for="website" value="Website" />
                <TextInput id="website" type="text" v-model="form.website" tabindex="-1" autocomplete="off" />
            </div>

            <div class="flex justify-end">
                <PrimaryButton :disabled="form.processing">
                    Inschrijven
                </PrimaryButton>
            </div>
        </form>
    </div>

    <PageFooter :pages="pages" />
</template>
