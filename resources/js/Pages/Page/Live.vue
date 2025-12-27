<script setup>
import { Head } from '@inertiajs/vue3';
import NavBar from '@/Components/NavBar.vue';
import PageFooter from '@/Components/PageFooter.vue';

const props = defineProps({
    page: {
        type: Object,
        required: true
    },
    pages: {
        type: Array,
        default: () => [],
    },
    upcomingServices: {
        type: Array,
        default: () => [],
    }
});

// Check if service is still active (not ended yet)
const isServiceActive = (service) => {
    if (!service.end_date) {
        return false;
    }
    const now = Math.floor(Date.now() / 1000); // Current time in seconds
    return service.end_date > now;
};
</script>

<template>

    <Head :title="page.title" />

    <NavBar :pages="pages" />

    <div class="relative isolate overflow-hidden pt-14">
        <img :src="'/storage/' + page.header_image" :alt="page.title"
            class="absolute inset-0 -z-10 size-full object-cover" />
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl py-32 sm:py-48 lg:py-56">
            </div>
        </div>
    </div>

    <!-- Live Page Layout -->
    <div class="mx-auto max-w-7xl px-6 py-12 lg:px-8">
        <h1 class="mb-8 text-4xl font-bold tracking-tight text-gray-900 text-center">{{ page.title }}</h1>

        <!-- Live Listening Action Blocks -->
        <div class="mb-16">
            <h2 class="mb-8 text-2xl font-semibold text-gray-900 text-center">Live Luisteren</h2>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-2 center">
                <!-- Action Block 1 -->
                <div
                    class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-blue-50 to-indigo-100 p-6 shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">YouTube Live</h3>
                            <p class="text-sm text-gray-600 mb-4">Bekijk de dienst live via YouTube</p>
                            <a href="https://www.youtube.com/@hhg_rijssen" target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                                </svg>
                                Bekijk Live
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Action Block 2 -->
                <div
                    class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-green-50 to-emerald-100 p-6 shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Audio Stream</h3>
                            <p class="text-sm text-gray-600 mb-4">Luister naar de audio stream via Kerkomroep</p>
                            <a href="https://www.kerkomroep.nl/kerken/21598" target="_blank"
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z" />
                                </svg>
                                Luister Live
                            </a>
                        </div>
                    </div>
                </div>

                <div
                    class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-yellow-50 to-amber-100 p-6 shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Kerkradio / Scanner</h3>
                            <p class="text-sm text-gray-600 mb-4">Frequentie 148,2625 Mhz, alleen te ontvangen in
                                omgeving Rijssen

                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-blue-50 to-indigo-100 p-6 shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Kerkomroep App</h3>
                            <p class="text-sm text-gray-600 mb-4">App is te downloaden in <a
                                    href="https://apps.apple.com/nl/app/kerkomroep/id541059718"
                                    target="_blank">App-Store</a> of <a
                                    href="https://play.google.com/store/apps/details?id=corp.csnet.nl.kerkomroep&hl=nl"
                                    target="_blank">Google Play</a>
                                omgeving Rijssen

                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order of Service Sections -->
        <div class="mb-16" v-if="upcomingServices.length > 0">
            <h2 class="mb-8 text-2xl font-semibold text-gray-900 text-center">Komende diensten</h2>
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <div v-for="(service, index) in upcomingServices" :key="service.id"
                    class="bg-white rounded-xl shadow-lg p-6 border border-gray-200 text-xl/8 flex flex-col h-full">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center">
                            <div :class="[
                                'w-8 h-8 rounded-full flex items-center justify-center mr-3',
                                index === 0 ? 'bg-blue-100' : index === 1 ? 'bg-green-100' : 'bg-purple-100'
                            ]">
                                <span :class="[
                                    'font-semibold text-sm',
                                    index === 0 ? 'text-blue-600' : index === 1 ? 'text-green-600' : 'text-purple-600'
                                ]">{{ index + 1 }}</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ service.title }}</h3>
                                <p class="text-sm text-gray-500">{{ service.start_date }} om {{ service.start_time }}
                                </p>
                                <p class="text-sm text-gray-600 mt-1"
                                    v-if="service.pastor && service.pastor !== service.title">{{ service.pastor
                                    }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="flex-grow space-y-3 text-sm text-gray-600 mb-4" v-if="service.liturgy">
                        <div class="prose prose-sm max-w-none text-xl/8" v-html="service.liturgy"></div>
                    </div>
                    <div v-else class="flex-grow text-sm text-gray-400 italic mb-4">
                        Nog geen liturgie beschikbaar
                    </div>

                    <div class="mt-auto" v-if="service.youtube_url && isServiceActive(service)">
                        <a :href="service.youtube_url" target="_blank"
                            class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                            </svg>
                            Bekijk Live
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Paragraph -->
        <div class="bg-gray-50 rounded-xl p-8">
            <h2 class="mb-6 text-2xl font-semibold text-gray-900">Collecten</h2>
            <div class="prose max-w-none text-left text-gray-700 text-xl/8">
                <p>

                    Er zijn 3 opties om uw collecten op afstand te kunnen geven.
                </p>
                <p>
                    1) Via het online collecteformulier.
                    <br />
                    2) Via de Scipio-app, waarvoor gemeenteleden een uitnodiging hebben ontvangen.
                    <br />
                    3) Zelf overmaken op het bankrekeningnummer van de kerkvoogdij: NL86 RABO 0347 3572 53 t.n.v.
                    Hersteld Hervormde Gemeente Rijssen o.v.v. collecten. <br /> Indien niet gespecificeerd, wordt dit
                    ontvangen bedrag verdeeld tussen de kerkvoogdij en diaconie volgens de verdeling zoals deze in de
                    loop van de tijd als een gemiddelde is waargenomen.
                </p>
                <br />
                <p>
                    Klik hier voor meer informatie over het collecteformulier en de Scipio-app.
                </p>
                <br />
                <p>
                    Alvast hartelijk dank voor uw steun aan onze gemeente!
                </p>
            </div>
        </div>
    </div>

    <PageFooter :pages="pages" />

</template>
