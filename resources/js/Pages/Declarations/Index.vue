<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

interface Declaration {
    id: number;
    amount: number;
    status: 'pending' | 'approved' | 'rejected';
    created_at: string;
}

interface Props {
    declarations: Declaration[];
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
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <PageHeader title="Mijn declaraties" description="Overzicht van al uw ingediende declaraties" />
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex justify-end mb-4">
                            <Link :href="route('declarations.create')">
                                <PrimaryButton>
                                    Nieuwe declaratie
                                </PrimaryButton>
                            </Link>
                        </div>

                        <div v-if="declarations.length === 0" class="text-center py-8">
                            <p class="text-gray-500">U heeft nog geen declaraties ingediend.</p>
                        </div>
                        <div v-else class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Datum
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Bedrag
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Status
                                        </th>

                                        <th scope="col" class="relative px-6 py-3">
                                            <span class="sr-only">Bekijken</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="declaration in declarations" :key="declaration.id">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ formatDate(declaration.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ formatAmount(declaration.amount) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            {{ getStatusText(declaration.status) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route('declarations.show', declaration.id)"
                                                class="text-primary-600 hover:text-primary-900">
                                                Bekijken
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
