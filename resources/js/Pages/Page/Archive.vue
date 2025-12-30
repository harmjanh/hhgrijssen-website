<script setup>
import { Head, router } from '@inertiajs/vue3';
import NavBar from '@/Components/NavBar.vue';
import PageFooter from '@/Components/PageFooter.vue';
import { ref, computed } from 'vue';
import axios from 'axios';

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
    availableYears: {
        type: Array,
        default: () => [],
    },
    filters: {
        type: Object,
        default: () => ({
            pastor: null,
            date: null,
            year: null,
        }),
    },
    isAdmin: {
        type: Boolean,
        default: false,
    },
});

const selectedPastor = ref(props.filters.pastor || '');
const selectedDate = ref(props.filters.date || '');
const selectedYear = ref(props.filters.year || '');
const downloadingServiceId = ref(null);

const applyFilters = () => {
    const params = {};

    if (selectedYear.value) {
        params.year = selectedYear.value;
    }

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
    selectedYear.value = '';
    router.get(window.location.pathname, {}, {
        preserveState: true,
        preserveScroll: true,
    });
};

const downloadVideo = async (service) => {
    if (!service.youtube_video) {
        return;
    }

    downloadingServiceId.value = service.id;

    try {
        await axios.post(`/services/${service.id}/download-video`);

        // Show success message
        alert('Download gestart. Het audio bestand zal binnenkort beschikbaar zijn.');

        // Reload the page after a short delay to update the status
        setTimeout(() => {
            router.reload({ only: ['services'] });
        }, 2000);
    } catch (error) {
        console.error('Download error:', error);
        alert(error.response?.data?.message || 'Er is een fout opgetreden bij het starten van de download.');
    } finally {
        downloadingServiceId.value = null;
    }
};

// Group services by year
const servicesByYear = computed(() => {
    const grouped = {};
    props.services.forEach(service => {
        const year = service.year;
        if (!grouped[year]) {
            grouped[year] = [];
        }
        grouped[year].push(service);
    });

    // Sort years in descending order
    const sortedYears = Object.keys(grouped).sort((a, b) => b - a);
    const result = {};
    sortedYears.forEach(year => {
        result[year] = grouped[year];
    });
    return result;
});
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

    <div class="mx-auto max-w-7xl px-3 py-12 md:px-6 lg:px-8">
        <h1 class="mb-8 text-4xl font-bold tracking-tight text-gray-900 text-center">{{ page.title }}</h1>

        <!-- Disclaimer -->
        <div class="mb-8 bg-blue-50 border-l-4 border-blue-400 p-4 rounded-r-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        <strong class="font-medium">Let op:</strong>
                        <!--Dit prekenarchief wordt continu bijgewerkt en kan
                        onvolledig zijn. Voor nu is 2021 t/m 2025 zo veel mogelijk als audio beschikbaar. We werken er
                        aan dat er meer beschikbaar komt.
                        Als u ontdekt dat er zaken niet kloppen of ontbreken,
                        <a href="mailto:webmaster@hhgrijssen.nl" class="font-medium underline hover:text-blue-800">neem
                            dan contact op via webmaster@hhgrijssen.nl</a>.

                        -->
                        Het prekenarchief is nog onder hande. De historische agenda is al wel inzichtelijk. De audio
                        bestanden volgen binnenkort.
                    </p>
                </div>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="mb-8 bg-white rounded-lg shadow-md p-6">
            <h2 class="mb-4 text-xl font-semibold text-gray-900">Filter diensten</h2>
            <div class="grid grid-cols-1 gap-4 md:grid-cols-4 md:gap-6">
                <!-- Year Filter -->
                <div>
                    <label for="year-filter" class="block text-sm font-medium text-gray-700 mb-2">
                        Jaar
                    </label>
                    <select id="year-filter" v-model="selectedYear" @change="applyFilters"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">Alle jaren</option>
                        <option v-for="year in availableYears" :key="year" :value="year">
                            {{ year }}
                        </option>
                    </select>
                </div>

                <!-- Pastor Filter -->
                <div>
                    <label for="pastor-filter" class="block text-sm font-medium text-gray-700 mb-2">
                        Predikant
                    </label>
                    <select id="pastor-filter" v-model="selectedPastor" @change="applyFilters"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
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
                    <input id="date-filter" v-model="selectedDate" type="date" @change="applyFilters"
                        class="w-full rounded-md border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500" />
                </div>

                <!-- Clear Filters Button -->
                <div class="flex items-end">
                    <button @click="clearFilters"
                        class="w-full px-4 py-2 bg-gray-200 text-gray-700 rounded-md hover:bg-gray-300 transition-colors">
                        Filters wissen
                    </button>
                </div>
            </div>
        </div>

        <!-- Services List -->
        <div v-if="services.length > 0">
            <div v-for="(yearServices, year) in servicesByYear" :key="year"
                class="mb-8 bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-100 px-6 py-3 border-b border-gray-200">
                    <h2 class="text-xl font-semibold text-gray-900">{{ year }}</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Predikant
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Datum
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Tijd
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Acties
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <tr v-for="service in yearServices" :key="service.id" class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ service.pastor || '-' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ service.date }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ service.time }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <div class="flex items-center gap-2">
                                        <!-- YouTube icon (only for admin users) -->
                                        <a v-if="isAdmin && service.youtube_video" :href="service.youtube_video.url"
                                            target="_blank" rel="noopener noreferrer"
                                            class="text-red-600 hover:text-red-800 transition-colors"
                                            title="Bekijk op YouTube">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                                            </svg>
                                        </a>
                                        <!-- Download button (only for admin users) -->
                                        <button
                                            v-if="isAdmin && service.youtube_video && service.youtube_video.download_status !== 'completed'"
                                            @click="downloadVideo(service)"
                                            :disabled="downloadingServiceId === service.id"
                                            class="text-green-600 hover:text-green-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
                                            :title="downloadingServiceId === service.id ? 'Download wordt verwerkt...' : 'Download audio'">
                                            <svg v-if="downloadingServiceId !== service.id" class="w-5 h-5"
                                                fill="currentColor" viewBox="0 0 24 24">
                                                <path d="M19 9h-4V3H9v6H5l7 7 7-7zM5 18v2h14v-2H5z" />
                                            </svg>
                                            <svg v-else class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                    stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                                </path>
                                            </svg>
                                        </button>
                                        <!-- Audio icon (for all users if audio exists) -->
                                        <a v-if="service.youtube_video && service.youtube_video.has_audio"
                                            :href="`/audio/${service.id}`"
                                            class="text-blue-600 hover:text-blue-800 transition-colors"
                                            title="Luister naar audio">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                <path
                                                    d="M12 3v10.55c-.59-.34-1.27-.55-2-.55-2.21 0-4 1.79-4 4s1.79 4 4 4 4-1.79 4-4V7h4V3h-6z" />
                                            </svg>
                                        </a>
                                        <span v-else>
                                            Audio volgt
                                        </span>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
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
