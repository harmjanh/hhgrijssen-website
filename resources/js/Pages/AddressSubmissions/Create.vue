<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';
import { computed } from 'vue';

const props = defineProps<{
    user?: {
        name: string;
        email: string;
        phonenumber: string;
        date_of_birth: string;
        street: string;
        number: string;
        zipcode: string;
        city: string;
    };
}>();

const form = useForm({
    name: props.user?.name ?? '',
    email: props.user?.email ?? '',
    date_of_birth: props.user?.date_of_birth ?? '',
    phone_number: props.user?.phonenumber ?? '',
    // Old address
    old_street: props.user?.street ?? '',
    old_number: props.user?.number ?? '',
    old_zipcode: props.user?.zipcode ?? '',
    old_city: props.user?.city ?? '',
    // New address
    new_street: '',
    new_number: '',
    new_zipcode: '',
    new_city: '',
    other_people: '',
    notes: '',
});

const submit = () => {
    form.post(route('address-submissions.store'), {
        preserveScroll: true,
    });
};
</script>

<template>

    <Head title="Nieuwe Adreswijziging" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader title="Nieuwe Adreswijziging"
                description="Vul het formulier in om een adreswijziging door te geven" />
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <form @submit.prevent="submit" class="space-y-6">
                            <!-- Personal Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Persoonlijke Informatie</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <InputLabel for="name" value="Naam" />
                                        <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name"
                                            required />
                                        <InputError :message="form.errors.name" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="email" value="Email" />
                                        <TextInput id="email" type="email" class="mt-1 block w-full"
                                            v-model="form.email" required />
                                        <InputError :message="form.errors.email" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="date_of_birth" value="Geboortedatum" />
                                        <TextInput id="date_of_birth" type="date" class="mt-1 block w-full"
                                            v-model="form.date_of_birth" required />
                                        <InputError :message="form.errors.date_of_birth" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="phone_number" value="Telefoonnummer" />
                                        <TextInput id="phone_number" type="text" class="mt-1 block w-full"
                                            v-model="form.phone_number" required />
                                        <InputError :message="form.errors.phone_number" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Old Address -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Huidig Adres</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <InputLabel for="old_street" value="Straat" />
                                        <TextInput id="old_street" type="text" class="mt-1 block w-full"
                                            v-model="form.old_street" required />
                                        <InputError :message="form.errors.old_street" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="old_number" value="Huisnummer" />
                                        <TextInput id="old_number" type="text" class="mt-1 block w-full"
                                            v-model="form.old_number" required />
                                        <InputError :message="form.errors.old_number" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="old_zipcode" value="Postcode" />
                                        <TextInput id="old_zipcode" type="text" class="mt-1 block w-full"
                                            v-model="form.old_zipcode" required />
                                        <InputError :message="form.errors.old_zipcode" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="old_city" value="Plaats" />
                                        <TextInput id="old_city" type="text" class="mt-1 block w-full"
                                            v-model="form.old_city" required />
                                        <InputError :message="form.errors.old_city" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- New Address -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Nieuw Adres</h3>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <InputLabel for="new_street" value="Straat" />
                                        <TextInput id="new_street" type="text" class="mt-1 block w-full"
                                            v-model="form.new_street" required />
                                        <InputError :message="form.errors.new_street" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="new_number" value="Huisnummer" />
                                        <TextInput id="new_number" type="text" class="mt-1 block w-full"
                                            v-model="form.new_number" required />
                                        <InputError :message="form.errors.new_number" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="new_zipcode" value="Postcode" />
                                        <TextInput id="new_zipcode" type="text" class="mt-1 block w-full"
                                            v-model="form.new_zipcode" required />
                                        <InputError :message="form.errors.new_zipcode" class="mt-2" />
                                    </div>

                                    <div>
                                        <InputLabel for="new_city" value="Plaats" />
                                        <TextInput id="new_city" type="text" class="mt-1 block w-full"
                                            v-model="form.new_city" required />
                                        <InputError :message="form.errors.new_city" class="mt-2" />
                                    </div>
                                </div>
                            </div>

                            <!-- Additional Information -->
                            <div class="bg-gray-50 dark:bg-gray-700 p-4 rounded-lg">
                                <h3 class="text-lg font-medium mb-4">Aanvullende Informatie</h3>
                                <div>
                                    <InputLabel for="other_people" value="Andere Personen die Verhuizen" />
                                    <TextArea id="other_people" class="mt-1 block w-full" v-model="form.other_people"
                                        :rows="3" />
                                    <InputError :message="form.errors.other_people" class="mt-2" />
                                </div>

                                <div class="mt-4">
                                    <InputLabel for="notes" value="Opmerking" />
                                    <TextArea id="notes" class="mt-1 block w-full" v-model="form.notes" :rows="3" />
                                    <InputError :message="form.errors.notes" class="mt-2" />
                                </div>
                            </div>

                            <div class="flex items-center justify-end mt-4">
                                <PrimaryButton class="ml-4" :disabled="form.processing">
                                    Indienen
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
