<script setup lang="ts">
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import type { User } from '@/types/User';

defineProps<{
    mustVerifyEmail?: Boolean;
    status?: String;
}>();

const user = usePage().props.auth.user as User;

const form = useForm({
    name: user.name,
    email: user.email,
    date_of_birth: user.date_of_birth || '',
    street: user.street || '',
    number: user.number || '',
    zipcode: user.zipcode || '',
    city: user.city || '',
    phonenumber: user.phonenumber || '',
    bankaccountnumber: user.bankaccountnumber || '',
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Profielinformatie
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Werk uw accountprofielinformatie en e-mailadres bij.
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
            <div>
                <InputLabel for="name" value="Naam" />

                <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus
                    autocomplete="name" />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="E-mail" />

                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required
                    autocomplete="username" />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="date_of_birth" value="Geboortedatum" />

                <TextInput id="date_of_birth" type="date" class="mt-1 block w-full" v-model="form.date_of_birth"
                    autocomplete="bday" />

                <InputError class="mt-2" :message="form.errors.date_of_birth" />
            </div>

            <div>
                <InputLabel for="street" value="Straat" />

                <TextInput id="street" type="text" class="mt-1 block w-full" v-model="form.street"
                    autocomplete="street-address" />

                <InputError class="mt-2" :message="form.errors.street" />
            </div>

            <div>
                <InputLabel for="number" value="Huisnummer" />

                <TextInput id="number" type="text" class="mt-1 block w-full" v-model="form.number"
                    autocomplete="address-line2" />

                <InputError class="mt-2" :message="form.errors.number" />
            </div>

            <div>
                <InputLabel for="zipcode" value="Postcode" />

                <TextInput id="zipcode" type="text" class="mt-1 block w-full" v-model="form.zipcode"
                    autocomplete="postal-code" />

                <InputError class="mt-2" :message="form.errors.zipcode" />
            </div>

            <div>
                <InputLabel for="city" value="Plaats" />

                <TextInput id="city" type="text" class="mt-1 block w-full" v-model="form.city"
                    autocomplete="address-level2" />

                <InputError class="mt-2" :message="form.errors.city" />
            </div>

            <div>
                <InputLabel for="phonenumber" value="Telefoonnummer" />

                <TextInput id="phonenumber" type="tel" class="mt-1 block w-full" v-model="form.phonenumber"
                    autocomplete="tel" />

                <InputError class="mt-2" :message="form.errors.phonenumber" />
            </div>

            <div>
                <InputLabel for="bankaccountnumber" value="IBAN" />

                <TextInput id="bankaccountnumber" type="text" class="mt-1 block w-full" v-model="form.bankaccountnumber"
                    autocomplete="off" />

                <InputError class="mt-2" :message="form.errors.bankaccountnumber" />
            </div>

            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800 dark:text-gray-200">
                    Uw e-mailadres is niet geverifieerd.
                    <Link :href="route('verification.send')" method="post" as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:text-gray-400 dark:hover:text-gray-100 dark:focus:ring-offset-gray-800">
                    Klik hier om de verificatie-e-mail opnieuw te versturen.
                    </Link>
                </p>

                <div v-show="status === 'verification-link-sent'"
                    class="mt-2 text-sm font-medium text-green-600 dark:text-green-400">
                    Er is een nieuwe verificatielink naar uw e-mailadres verzonden.
                </div>
            </div>

            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Opslaan</PrimaryButton>

                <Transition enter-active-class="transition ease-in-out" enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out" leave-to-class="opacity-0">
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600 dark:text-gray-400">
                        Opgeslagen.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>
