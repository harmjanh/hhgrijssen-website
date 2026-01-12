<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import NavBar from '@/Components/NavBar.vue';
import PageFooter from '@/Components/PageFooter.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps<{
    pages: Array<{ id: number; title: string; slug: string }>;
}>();

const form = useForm({
    name: '',
    email: '',
    phone_number: '',
});

const submit = () => {
    form.post(route('church-administration-contact.store'), {
        preserveScroll: false,
        onSuccess: () => {
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Kerkelijke Administratie Contact" />

    <NavBar :pages="pages" />

    <div class="mx-auto max-w-7xl px-3 py-12 md:px-6 lg:px-8">
        <div class="mx-auto max-w-2xl">
            <h1 class="mb-6 text-3xl font-bold tracking-tight text-gray-900 text-center">Kerkelijke Administratie</h1>
            
            <p class="mb-8 text-center text-gray-600">
                Laat hier uw gegevens achter voor de kerkelijke administratie. We nemen zo spoedig mogelijk contact met u op.
            </p>

            <div v-if="$page.props.flash?.success" class="mb-8 rounded-lg bg-green-50 border border-green-200 p-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-lg font-medium text-green-800 mb-2">
                            Bedankt!
                        </h3>
                        <p class="text-sm text-green-700">
                            {{ $page.props.flash?.success }}
                        </p>
                    </div>
                </div>
            </div>

            <div v-show="!$page.props.flash?.success" class="bg-white shadow-sm sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <form @submit.prevent="submit" class="space-y-6">
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
                            <InputLabel for="email" value="E-mailadres *" />
                            <TextInput 
                                id="email" 
                                type="email" 
                                class="mt-1 block w-full"
                                v-model="form.email" 
                                required 
                            />
                            <InputError :message="form.errors.email" class="mt-2" />
                        </div>

                        <div>
                            <InputLabel for="phone_number" value="06-nummer *" />
                            <TextInput 
                                id="phone_number" 
                                type="tel" 
                                class="mt-1 block w-full"
                                v-model="form.phone_number" 
                                placeholder="Bijvoorbeeld: 0612345678"
                                required 
                            />
                            <InputError :message="form.errors.phone_number" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-end">
                            <PrimaryButton :disabled="form.processing">
                                Verzenden
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <PageFooter />
</template>
