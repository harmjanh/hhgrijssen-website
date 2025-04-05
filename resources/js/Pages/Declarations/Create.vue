<script setup lang="ts">
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import TextArea from '@/Components/TextArea.vue';

interface Props {
    user: {
        name: string;
        street: string;
        number: string;
        zipcode: string;
        city: string;
        bankaccountnumber: string;
    };
}

const props = defineProps<Props>();

const form = useForm({
    name: props.user.name,
    street: props.user.street,
    number: props.user.number,
    zipcode: props.user.zipcode,
    city: props.user.city,
    bankaccountnumber: props.user.bankaccountnumber,
    amount: '',
    explanation: '',
    attachments: [] as File[],
});

const fileInput = ref<HTMLInputElement | null>(null);

const handleFileChange = (event: Event) => {
    const input = event.target as HTMLInputElement;
    if (input.files) {
        form.attachments = Array.from(input.files);
    }
};

const submit = () => {
    form.post(route('declarations.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            if (fileInput.value) {
                fileInput.value.value = '';
            }
        },
    });
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Nieuwe declaratie
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <InputLabel for="name" value="Naam" />
                                    <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name"
                                        required />
                                    <InputError :message="form.errors.name" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="amount" value="Bedrag" />
                                    <TextInput id="amount" type="number" step="0.01" class="mt-1 block w-full"
                                        v-model="form.amount" required />
                                    <InputError :message="form.errors.amount" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="street" value="Straat" />
                                    <TextInput id="street" type="text" class="mt-1 block w-full" v-model="form.street"
                                        required />
                                    <InputError :message="form.errors.street" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="number" value="Huisnummer" />
                                    <TextInput id="number" type="text" class="mt-1 block w-full" v-model="form.number"
                                        required />
                                    <InputError :message="form.errors.number" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="zipcode" value="Postcode" />
                                    <TextInput id="zipcode" type="text" class="mt-1 block w-full" v-model="form.zipcode"
                                        required />
                                    <InputError :message="form.errors.zipcode" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="city" value="Plaats" />
                                    <TextInput id="city" type="text" class="mt-1 block w-full" v-model="form.city"
                                        required />
                                    <InputError :message="form.errors.city" class="mt-2" />
                                </div>

                                <div>
                                    <InputLabel for="bankaccountnumber" value="IBAN" />
                                    <TextInput id="bankaccountnumber" type="text" class="mt-1 block w-full"
                                        v-model="form.bankaccountnumber" required />
                                    <InputError :message="form.errors.bankaccountnumber" class="mt-2" />
                                </div>
                            </div>

                            <div>
                                <InputLabel for="explanation" value="Toelichting" />
                                <TextArea id="explanation" class="mt-1 block w-full" v-model="form.explanation" required
                                    rows="4" />
                                <InputError :message="form.errors.explanation" class="mt-2" />
                            </div>

                            <div>
                                <InputLabel for="attachments" value="Bijlagen" />
                                <input ref="fileInput" type="file" id="attachments" class="mt-1 block w-full" multiple
                                    @change="handleFileChange" accept="image/*,.pdf,.doc,.docx" />
                                <InputError :message="form.errors.attachments" class="mt-2" />
                                <p class="mt-1 text-sm text-gray-500">
                                    Maximaal 10MB per bestand. Toegestane bestandstypen: afbeeldingen, PDF, Word.
                                </p>
                            </div>

                            <div class="flex items-center justify-end">
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
