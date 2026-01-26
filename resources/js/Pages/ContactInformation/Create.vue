<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

interface Props {
    user: {
        name: string;
        email: string;
        phonenumber: string | null;
    };
}

const props = defineProps<Props>();

const form = useForm({
    name: props.user.name,
    email: props.user.email,
    phonenumber: props.user.phonenumber || '',
});

const submit = () => {
    form.post(route('contact-information.store'), {
        preserveScroll: true,
    });
};
</script>

<template>

    <Head title="Contactgegevens Doorgeven" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader title="Contactgegevens Doorgeven" />
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="mb-6">
                            <p class="text-gray-700 dark:text-gray-300">
                                Uw e-mailadres of telefoonnummer komt niet voor in onze administratie.
                                Vul hieronder uw gegevens in zodat het kerkelijk bureau deze kan verwerken.
                            </p>
                        </div>

                        <!-- Success message -->
                        <div v-if="$page.props.flash?.success"
                            class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-lg">
                            <p class="text-green-800 dark:text-green-200">
                                {{ $page.props.flash.success }}
                            </p>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <InputLabel for="name" value="Naam *" />
                                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required
                                    autofocus />
                                <InputError :message="form.errors.name" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="email" value="E-mailadres *" />
                                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email"
                                    required />
                                <InputError :message="form.errors.email" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="phonenumber" value="Telefoonnummer *" />
                                <TextInput id="phonenumber" type="tel" class="mt-1 block w-full"
                                    v-model="form.phonenumber" placeholder="Bijvoorbeeld: 0612345678" required />
                                <InputError :message="form.errors.phonenumber" class="mt-2" />
                            </div>

                            <div class="flex items-center justify-end space-x-4">
                                <Link :href="route('dashboard')"
                                    class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                                    Annuleren
                                </Link>
                                <PrimaryButton :disabled="form.processing">
                                    Verzenden
                                </PrimaryButton>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
