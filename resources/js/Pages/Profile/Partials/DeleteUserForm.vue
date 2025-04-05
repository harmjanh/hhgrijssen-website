<script setup lang="ts">
import DangerButton from '@/Components/DangerButton.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import Modal from '@/Components/Modal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick, ref } from 'vue';

const confirmingUserDeletion = ref(false);
const passwordInput = ref<HTMLInputElement | null>(null);

const form = useForm({
    password: '',
});

const confirmUserDeletion = () => {
    confirmingUserDeletion.value = true;

    nextTick(() => passwordInput.value?.focus());
};

const deleteUser = () => {
    form.delete(route('profile.destroy'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
        onError: () => passwordInput.value?.focus(),
        onFinish: () => {
            form.reset();
        },
    });
};

const closeModal = () => {
    confirmingUserDeletion.value = false;

    form.clearErrors();
    form.reset();
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                Account Verwijderen
            </h2>

            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                Zodra uw account is verwijderd, worden alle bijbehorende resources en gegevens permanent verwijderd.
                Download voordat u uw account verwijdert alle gegevens of informatie die u wilt behouden.
            </p>
        </header>

        <DangerButton @click="confirmUserDeletion">Account Verwijderen</DangerButton>

        <Modal :show="confirmingUserDeletion" @close="closeModal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                    Weet u zeker dat u uw account wilt verwijderen?
                </h2>

                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                    Zodra uw account is verwijderd, worden alle bijbehorende resources en gegevens permanent verwijderd.
                    Voer uw wachtwoord in om te bevestigen dat u uw account permanent wilt verwijderen.
                </p>

                <div class="mt-6">
                    <InputLabel for="password" value="Wachtwoord" class="sr-only" />

                    <TextInput id="password" ref="passwordInput" v-model="form.password" type="password"
                        class="mt-1 block w-3/4" placeholder="Wachtwoord" @keyup.enter="deleteUser" />

                    <InputError :message="form.errors.password" class="mt-2" />
                </div>

                <div class="mt-6 flex justify-end">
                    <SecondaryButton @click="closeModal">
                        Annuleren
                    </SecondaryButton>

                    <DangerButton class="ms-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
                        @click="deleteUser">
                        Account Verwijderen
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </section>
</template>
