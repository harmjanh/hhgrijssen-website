<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';

interface DeclarationAttachment {
    id: number;
    filename: string;
    path: string;
    mime_type: string;
    size: number;
}

interface Declaration {
    id: number;
    name: string;
    street: string;
    number: string;
    zipcode: string;
    city: string;
    bankaccountnumber: string;
    amount: number;
    explanation: string;
    status: 'pending' | 'approved' | 'rejected';
    admin_notes: string | null;
    created_at: string;
    attachments: DeclarationAttachment[];
}

interface Props {
    declaration: Declaration;
}

defineProps<Props>();

const formatDate = (date: string) => {
    return new Date(date).toLocaleDateString('nl-NL', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    });
};

const formatAmount = (amount: number) => {
    return new Intl.NumberFormat('nl-NL', {
        style: 'currency',
        currency: 'EUR',
    }).format(amount);
};

const getStatusColor = (status: Declaration['status']) => {
    switch (status) {
        case 'approved':
            return 'bg-green-100 text-green-800';
        case 'rejected':
            return 'bg-red-100 text-red-800';
        default:
            return 'bg-yellow-100 text-yellow-800';
    }
};

const getStatusText = (status: Declaration['status']) => {
    switch (status) {
        case 'approved':
            return 'Goedgekeurd';
        case 'rejected':
            return 'Afgewezen';
        default:
            return 'In behandeling';
    }
};

const formatFileSize = (bytes: number) => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <PageHeader title="Declaratie Details" description="Bekijk de details van uw ingediende declaratie" />
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-end mb-4">
                            <Link :href="route('declarations.index')" class="text-primary-600 hover:text-primary-900">
                                Terug naar overzicht
                            </Link>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Persoonlijke gegevens</h3>
                                <dl class="mt-4 space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Naam</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ declaration.name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Adres</dt>
                                        <dd class="mt-1 text-sm text-gray-900">
                                            {{ declaration.street }} {{ declaration.number }}<br>
                                            {{ declaration.zipcode }} {{ declaration.city }}
                                        </dd>
                                    </div>
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">IBAN</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ declaration.bankaccountnumber }}</dd>
                                    </div>
                                </dl>
                            </div>

                            <div>
                                <h3 class="text-lg font-medium text-gray-900">Declaratie details</h3>
                                <dl class="mt-4 space-y-4">
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Bedrag</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ formatAmount(declaration.amount) }}
                                        </dd>
                                    </div>

                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Datum</dt>
                                        <dd class="mt-1 text-sm text-gray-900">{{ formatDate(declaration.created_at) }}
                                        </dd>
                                    </div>
                                </dl>
                            </div>
                        </div>

                        <div class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900">Toelichting</h3>
                            <p class="mt-4 text-sm text-gray-900 whitespace-pre-line">{{ declaration.explanation }}</p>
                        </div>

                        <div v-if="declaration.admin_notes" class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900">Opmerkingen van de beheerder</h3>
                            <p class="mt-4 text-sm text-gray-900 whitespace-pre-line">{{ declaration.admin_notes }}</p>
                        </div>

                        <div v-if="declaration.attachments.length > 0" class="mt-8">
                            <h3 class="text-lg font-medium text-gray-900">Bijlagen</h3>
                            <ul class="mt-4 divide-y divide-gray-200">
                                <li v-for="attachment in declaration.attachments" :key="attachment.id" class="py-4">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                            <span class="ml-2 text-sm text-gray-900">{{ attachment.filename }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            <span class="text-sm text-gray-500">{{ formatFileSize(attachment.size)
                                                }}</span>
                                            <a :href="'/storage/' + attachment.path" target="_blank"
                                                class="ml-4 text-primary-600 hover:text-primary-900">
                                                Download
                                            </a>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
