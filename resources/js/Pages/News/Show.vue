<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import NavBar from "@/Components/NavBar.vue";
import PageFooter from "@/Components/PageFooter.vue";
import InputError from "@/Components/InputError.vue";
import InputLabel from "@/Components/InputLabel.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import TextInput from "@/Components/TextInput.vue";
import TextArea from "@/Components/TextArea.vue";

const props = defineProps<{
    pages: { id: number; title: string; slug: string }[];
    newsItem: {
        id: number;
        slug: string;
        title: string;
        content: string;
        description: string;
        image: string;
        created_at: string;
        updated_at: string;
        visible_from: string;
        visible_until: string;
        contact_form_enabled: boolean;
    };
}>();

const page = usePage();

const form = useForm({
    name: '',
    email: '',
    phone: '',
    remarks: '',
    website: '', // honeypot
});

const submit = () => {
    form.post(route('news.contact.store', props.newsItem.slug), {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};
</script>

<template>

    <Head :title="newsItem.title" />

    <NavBar :pages="pages" />

    <div class="relative isolate overflow-hidden pt-14" v-if="newsItem.image">
        <img :src="'/storage/' + newsItem.image" :alt="newsItem.title"
            class="absolute inset-0 -z-10 size-full object-cover" />
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl py-32 sm:py-48 lg:py-56">
            </div>
        </div>
    </div>

    <div class="mx-auto w-full px-3 py-12 md:w-3/5 md:px-0">
        <h1 class="mb-6 text-3xl font-bold tracking-tight text-gray-900 text-left">{{ newsItem.title }}</h1>
        <div class="prose max-w-none text-left text-gray-700 text-xl/8" v-html="newsItem.content"></div>

        <!-- Contactformulier -->
        <div v-if="newsItem.contact_form_enabled" class="mt-12 pt-10 border-t border-gray-200">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Neem contact op</h2>
            <p class="text-gray-600 mb-6">Heeft u een vraag of opmerking over dit bericht? Vul het formulier in en wij nemen contact met u op.</p>

            <div v-if="page.props.flash?.success" class="mb-6 rounded-lg bg-green-50 border border-green-200 p-5 flex gap-3">
                <svg class="h-5 w-5 text-green-400 shrink-0 mt-0.5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd" />
                </svg>
                <p class="text-sm text-green-700">{{ page.props.flash.success }}</p>
            </div>

            <form v-show="!page.props.flash?.success" @submit.prevent="submit" class="space-y-5">
                <!-- Honeypot -->
                <div class="absolute -left-[9999px] top-0 opacity-0 pointer-events-none" aria-hidden="true">
                    <label for="cf_website">Website</label>
                    <input id="cf_website" type="text" name="website" v-model="form.website" tabindex="-1" autocomplete="off" />
                </div>

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <InputLabel for="cf_name" value="Naam *" />
                        <TextInput id="cf_name" type="text" class="mt-1 block w-full" v-model="form.name" required />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div>
                        <InputLabel for="cf_email" value="E-mailadres *" />
                        <TextInput id="cf_email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                        <InputError :message="form.errors.email" class="mt-2" />
                    </div>
                </div>

                <div>
                    <InputLabel for="cf_phone" value="Telefoonnummer" />
                    <TextInput id="cf_phone" type="tel" class="mt-1 block w-full" v-model="form.phone" />
                    <InputError :message="form.errors.phone" class="mt-2" />
                </div>

                <div>
                    <InputLabel for="cf_remarks" value="Opmerkingen *" />
                    <TextArea id="cf_remarks" class="mt-1 block w-full" v-model="form.remarks" :rows="5" required />
                    <InputError :message="form.errors.remarks" class="mt-2" />
                </div>

                <div class="flex justify-end">
                    <PrimaryButton :disabled="form.processing">
                        Versturen
                    </PrimaryButton>
                </div>
            </form>
        </div>

        <!-- Back to news overview button -->
        <div class="mt-8 pt-8 border-t border-gray-200">
            <Link :href="route('news.index')"
                class="inline-flex items-center px-4 py-2 bg-primary-500 text-gray-900 text-sm font-medium rounded-lg hover:bg-primary-600 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Terug naar nieuws overzicht
            </Link>
        </div>
    </div>

    <PageFooter :pages="pages" />
</template>
