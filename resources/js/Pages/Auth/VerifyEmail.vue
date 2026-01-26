<script setup lang="ts">
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps<{
    status?: string;
}>();

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>

        <Head title="Email Verificatie" />

        <div v-if="status && status !== 'verification-link-sent'" class="mb-4 text-sm font-medium text-amber-600 dark:text-amber-400">
            {{ status }}
        </div>

        <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
            Bedankt voor uw registratie! Voordat u kunt beginnen, moet u uw
            emailadres bevestigen door op de link te klikken die we u zojuist hebben gemaild. 
            Als u de email niet heeft ontvangen, kunnen we u graag een nieuwe sturen.
        </div>

        <div class="mb-4 text-sm font-medium text-green-600 dark:text-green-400" v-if="verificationLinkSent">
            Een nieuwe verificatielink is verzonden naar het emailadres dat u
            heeft opgegeven tijdens de registratie.
        </div>

        <form @submit.prevent="submit">
            <InputError class="mb-4" :message="form.errors.email" />
            
            <div class="mt-4 flex items-center justify-between">
                <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                    Verificatie-email opnieuw verzenden
                </PrimaryButton>

                <Link :href="route('logout')" method="post" as="button"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800">
                    Uitloggen</Link>
            </div>
        </form>
    </GuestLayout>
</template>
