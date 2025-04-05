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

const removeFile = (index: number) => {
    form.attachments.splice(index, 1);
    // Reset the file input if all files are removed
    if (form.attachments.length === 0 && fileInput.value) {
        fileInput.value.value = '';
    }
};

const formatFileSize = (bytes: number) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
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

                                <!-- Selected files list -->
                                <div v-if="form.attachments.length > 0" class="mt-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-2">Geselecteerde bestanden:</h4>
                                    <ul class="border border-gray-200 rounded-md divide-y divide-gray-200">
                                        <li v-for="(file, index) in form.attachments" :key="index"
                                            class="flex items-center justify-between py-2 px-3">
                                            <div class="flex items-center">
                                                <svg class="h-5 w-5 text-gray-400 mr-2" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                                </svg>
                                                <span class="text-sm text-gray-900">{{ file.name }}</span>
                                            </div>
                                            <div class="flex items-center">
                                                <span class="text-xs text-gray-500 mr-3">{{ formatFileSize(file.size)
                                                }}</span>
                                                <button type="button" @click="removeFile(index)"
                                                    class="text-red-600 hover:text-red-800">
                                                    <svg class="h-5 w-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
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
