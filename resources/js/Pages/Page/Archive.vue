<script setup>
import { Head, router } from '@inertiajs/vue3';
import NavBar from '@/Components/NavBar.vue';
import PageFooter from '@/Components/PageFooter.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    page: {
        type: Object,
        required: true
    },
    pages: {
        type: Array,
        default: () => [],
    },
    services: {
        type: Array,
        default: () => [],
    },
    pastors: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({
            pastor: null,
            date: null,
        }),
    },
});

const selectedPastor = ref(props.filters.pastor || '');
const selectedDate = ref(props.filters.date || '');

const applyFilters = () => {
    const params = {};
    
    if (selectedPastor.value) {
        params.pastor = selectedPastor.value;
    }
    
    if (selectedDate.value) {
        params.date = selectedDate.value;
    }
    
    router.get(window.location.pathname, params, {
        preserveState: true,
        preserveScroll: true,
    });
};

const clearFilters = () => {
    selectedPastor.value = '';
    selectedDate.value = '';
    router.get(window.location.pathname, {}, {
        preserveState: true,
        preserveScroll: true,
    });
};
</script>

<template>
    <Head :title="page.title" />

    <NavBar :pages="pages" />

    <div class="relative isolate overflow-hidden pt-14">
        <img v-if="page.header_image" :src="'/storage/' + page.header_image" :alt="page.title"
            class="absolute inset-0 -z-10 size-full object-cover" />
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl py-32 sm:py-48 lg:py-56">
            </div>
        </div>
    </div>

    <div class="mx-auto max-w-7xl px-6 py-12 lg:px-8">
        <h1 class="mb-8 text-4xl font-bold tracking-tight text-gray-900 text-center">{{ page.title }}</h1>

        <!-- Filters Section -->
        <div class="mb-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="mb-4 text-xl font-semibold text-gray-900">Filter diensten</h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-3 md:gap-6">
                <!-- Pastor Filter -->
                <div>
                    <label for="pastor-filter" class="block text-sm font-medium text-gray-700 mb-2">
                        Predikant
                    </label>
                    <select
                        id="pastor-filter"
                        v-model="selectedPastor"
                        @change="applyFilters"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    >
                        <option value="">Alle predikanten</option>
                        <option v-for="pastor in pastors" :key="pastor" :value="pastor">
                            {{ pastor }}
                        </option>
                    </select>
                </div>

                <!-- Date Filter -->
                <div>
                    <label for="date-filter" class="block text-sm font-medium text-gray-700 mb-2">
                        Datum
                    </label>
                    <input
                        id="date-filter"
                        v-model="selectedDate"
                        type="date"
                        @change="applyFilters"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                    />
                </div>

                <!-- Clear Filters Button -->
                <div class="flex items-end">
                    <button
                        @click="clearFilters"
                        class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors"
                    >
                        Filters wissen
                    </button>
                </div>
            </div>
        </div>

        <!-- Services List -->
        <div v-if="services.length > 0" class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Predikant
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Datum
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Tijd
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="service in services" :key="service.id" class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ service.pastor || '-' }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ service.date }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ service.time }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- No Services Message -->
        <div v-else class="bg-white rounded-lg shadow-md p-8 text-center">
            <p class="text-gray-500 text-lg">Geen diensten gevonden met de geselecteerde filters.</p>
        </div>

        <!-- Page Content -->
        <div v-if="page.content" class="mt-12 bg-gray-50 rounded-xl p-8">
            <div class="prose max-w-none text-left text-gray-700 text-xl/8" v-html="page.content"></div>
        </div>
    </div>

    <PageFooter :pages="pages" />
</template>

