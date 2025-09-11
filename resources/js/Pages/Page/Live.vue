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
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Action Block 1 -->
                <div
                    class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-blue-50 to-indigo-100 p-6 shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">YouTube Live</h3>
                            <p class="text-sm text-gray-600 mb-4">Bekijk de dienst live via YouTube</p>
                            <button
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                                </svg>
                                Bekijk Live
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Action Block 2 -->
                <div
                    class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-green-50 to-emerald-100 p-6 shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Audio Stream</h3>
                            <p class="text-sm text-gray-600 mb-4">Luister naar de audio stream</p>
                            <button
                                class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 14.5v-9l6 4.5-6 4.5z" />
                                </svg>
                                Luister Live
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Action Block 3 -->
                <div
                    class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-purple-50 to-violet-100 p-6 shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Podcast</h3>
                            <p class="text-sm text-gray-600 mb-4">Download als podcast</p>
                            <button
                                class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z" />
                                </svg>
                                Download
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Action Block 4 -->
                <div
                    class="group relative overflow-hidden rounded-xl bg-gradient-to-br from-orange-50 to-amber-100 p-6 shadow-lg transition-all duration-300 hover:shadow-xl hover:scale-105">
                    <div class="flex items-center justify-between">
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Social Media</h3>
                            <p class="text-sm text-gray-600 mb-4">Volg ons op social media</p>
                            <button
                                class="inline-flex items-center px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                                </svg>
                                Volg Live
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order of Service Sections -->
        <div class="mb-16">
            <h2 class="mb-8 text-2xl font-semibold text-gray-900 text-center">Dienstrooster</h2>
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
            <h2 class="mb-6 text-2xl font-semibold text-gray-900">Over de Dienst</h2>
            <div class="prose max-w-none text-gray-700 leading-relaxed" v-html="page.content"></div>
        </div>
    </div>

    <PageFooter :pages="pages" />

</template>

