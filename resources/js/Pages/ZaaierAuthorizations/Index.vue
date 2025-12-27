<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

defineProps<{
    authorizations: {
        data: Array<{
            id: number;
            name: string;
            initials: string;
            street: string;
            zipcode: string;
            city: string;
            submission_date: string;
            created_at: string;
        }>;
        current_page: number;
        last_page: number;
    };
    hasAuthorization: boolean;
}>();
</script>

<template>
    <Head title="Machtigingen De Zaaier" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader 
                title="Machtigingen De Zaaier"
                description="Overzicht van uw ingediende machtigingen voor De Zaaier" 
            />
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div v-if="!hasAuthorization" class="mb-6 flex justify-end">
                    <Link :href="route('zaaier-authorizations.create')">
                        <PrimaryButton>
                            Nieuwe Machtiging
                        </PrimaryButton>
                    </Link>
                </div>
                
                <div v-if="authorizations.data.length === 0" class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100 text-center">
                        <p class="text-gray-500 dark:text-gray-400 mb-4">U heeft nog geen machtigingen ingediend.</p>
                        <Link v-if="!hasAuthorization" :href="route('zaaier-authorizations.create')">
                            <PrimaryButton>
                                Eerste Machtiging Indienen
                            </PrimaryButton>
                        </Link>
                    </div>
                </div>

                <div v-else class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Naam
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Adres
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Datum Machtiging
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Ingediend op
                                        </th>
                                        <th scope="col"
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                            Acties
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    <tr v-for="authorization in authorizations.data" :key="authorization.id">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                                {{ authorization.name }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ authorization.initials }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                                {{ authorization.street }}
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ authorization.zipcode }} {{ authorization.city }}
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ new Date(authorization.submission_date).toLocaleDateString('nl-NL') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ new Date(authorization.created_at).toLocaleDateString('nl-NL') }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <Link :href="route('zaaier-authorizations.show', authorization.id)"
                                                class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                                Details
                                            </Link>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4 flex justify-center" v-if="authorizations.last_page > 1">
                            <Link 
                                v-for="page in authorizations.last_page" 
                                :key="page"
                                :href="route('zaaier-authorizations.index', { page })" 
                                :class="{
                                    'bg-gray-800 dark:bg-gray-200 text-white dark:text-gray-800': page === authorizations.current_page,
                                    'text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700': page !== authorizations.current_page
                                }" 
                                class="px-4 py-2 mx-1 rounded-md">
                                {{ page }}
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

