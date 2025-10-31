<script setup>
import { Head } from '@inertiajs/vue3';
import NavBar from '@/Components/NavBar.vue';
import PageFooter from '@/Components/PageFooter.vue';

defineProps({
    page: {
        type: Object,
        required: true
    },
    pages: {
        type: Array,
        default: () => [],
    }
});
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
                            <p class="text-sm text-gray-600 mb-4">Luister naar de audio stream via Kerkomrope</p>
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
            </div>
        </div>

        <!-- Order of Service Sections -->
        <div class="mb-16">
            <h2 class="mb-8 text-2xl font-semibold text-gray-900 text-center">Komende diensten</h2>
            <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
                <!-- Section 1 -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-blue-600 font-semibold text-sm">1</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Opening & Welkom</h3>
                    </div>
                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Votum & Groet</span>
                            <span class="text-gray-400">5 min</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Psalm 100</span>
                            <span class="text-gray-400">3 min</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Gebed</span>
                            <span class="text-gray-400">4 min</span>
                        </div>
                    </div>
                </div>

                <!-- Section 2 -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-green-600 font-semibold text-sm">2</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Woord & Gebed</h3>
                    </div>
                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Schriftlezing</span>
                            <span class="text-gray-400">8 min</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Preek</span>
                            <span class="text-gray-400">25 min</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Gebed</span>
                            <span class="text-gray-400">5 min</span>
                        </div>
                    </div>
                </div>

                <!-- Section 3 -->
                <div class="bg-white rounded-xl shadow-lg p-6 border border-gray-200">
                    <div class="flex items-center mb-4">
                        <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                            <span class="text-purple-600 font-semibold text-sm">3</span>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900">Afsluiting</h3>
                    </div>
                    <div class="space-y-3 text-sm text-gray-600">
                        <div class="flex justify-between">
                            <span>Psalm 134</span>
                            <span class="text-gray-400">3 min</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Zegen</span>
                            <span class="text-gray-400">2 min</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Uitgang</span>
                            <span class="text-gray-400">-</span>
                        </div>
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
