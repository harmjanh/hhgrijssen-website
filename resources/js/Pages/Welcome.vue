<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import NavBar from "@/Components/NavBar.vue";
import PageFooter from "@/Components/PageFooter.vue";
import NewsItems from "@/Components/NewsItems.vue";
import Visit from "@/Components/Visit.vue";

import { LifebuoyIcon, NewspaperIcon, PhoneIcon } from '@heroicons/vue/20/solid'

defineProps<{
    canLogin?: boolean;
    canRegister?: boolean;
    laravelVersion: string;
    phpVersion: string;
    pages: { id: number; title: string; slug: string }[];
    page: {
        title: string;
        content: string;
    };
    newsItems: {
        id: number;
        title: string;
        content: string;
        description: string;
        image: string;
        created_at: string;
        updated_at: string;
        visible_from: string;
        visible_until: string;
    }[];
    upcomingServices?: {
        id: number;
        pastor: string;
        title: string;
        start_date: string;
        start_time: string;
    }[];
}>();

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}
</script>

<template>

    <Head title="Welkom!" />
    <NavBar :pages="pages" />

    <!-- header -->

    <div class="relative isolate overflow-hidden pt-14">
        <img src="/images/home_start.jpg" alt="" class="absolute inset-0 -z-10 size-full object-cover" />
        <!--<div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">-->
        <!--    &lt;!&ndash;<div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" />&ndash;&gt;-->
        <!--</div>-->
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl py-16 sm:py-24 lg:py-28">
                <div class="hidden sm:mb-8 sm:flex sm:justify-center">
                    <!--<div class="relative rounded-full px-3 py-1 text-sm/6 text-gray-400 ring-1 ring-white/10 hover:ring-white/20">-->
                    <!--    Announcing our next round of funding. <a href="#" class="font-semibold text-white"><span class="absolute inset-0" aria-hidden="true" />Read more <span aria-hidden="true">&rarr;</span></a>-->
                    <!--</div>-->
                </div>
                <div class="text-center">
                    <h1 class="text-balance text-5xl font-semibold tracking-tight text-white sm:text-7xl">Ik ben het
                        Brood des levens</h1>
                    <!--<p class="mt-8 text-pretty text-lg font-medium text-gray-400 sm:text-xl/8">Anim aute id magna aliqua ad ad non deserunt sunt. Qui irure qui lorem cupidatat commodo. Elit sunt amet fugiat veniam occaecat.</p>-->
                    <div class="mt-10 flex items-center justify-center gap-x-6">
                        <a href="/kerkdiensten-live-luisteren"
                            class="rounded-md bg-primary-500 px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm hover:bg-primary-600 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-primary-400">
                            Luister mee <span aria-hidden="true">→</span></a>
                        <!--<a href="#" class="text-sm/6 font-semibold text-white">Kom meer te weten <span aria-hidden="true">→</span></a>-->
                    </div>
                </div>
            </div>
        </div>



        <div class="absolute inset-x-0 top-[calc(100%-13rem)] -z-10 transform-gpu overflow-hidden blur-3xl sm:top-[calc(100%-30rem)]"
            aria-hidden="true">
            <div class="relative left-[calc(50%+3rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 bg-gradient-to-tr from-[#ff80b5] to-[#9089fc] opacity-20 sm:left-[calc(50%+36rem)] sm:w-[72.1875rem]"
                style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)" />
        </div>
    </div>

    <!-- Upcoming Services Cards -->
    <div v-if="upcomingServices && upcomingServices.length > 0" class="mx-auto max-w-7xl px-3 py-8 md:px-6 lg:px-8">
        <div class="mb-6 text-center">
            <h2 class="text-2xl font-semibold text-gray-900">Komende diensten</h2>
        </div>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div v-for="service in upcomingServices" :key="service.id"
                class="bg-white rounded-lg shadow-md p-4 border border-gray-200 hover:shadow-lg transition-shadow">
                <div class="mb-2">
                    <h3 class="text-lg font-semibold text-gray-900">{{ service.title }}</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ service.start_date }} om {{ service.start_time }}</p>
                    <p v-if="service.pastor && service.pastor !== service.title" class="text-sm text-gray-500 mt-1">
                        {{ service.pastor }}
                    </p>
                </div>
                <Link href="/kerkdiensten-live-luisteren#liturgie"
                    class="inline-flex items-center text-sm font-medium text-primary-600 hover:text-primary-700">
                    Meer informatie
                    <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </Link>
            </div>
        </div>
    </div>

    <div class="mx-auto w-full px-3 py-12 md:w-3/5 md:px-0">
        <!-- <h1 class="mb-6 text-3xl font-bold tracking-tight text-gray-900 text-left">{{ page.title }}</h1> -->
        <div class="prose max-w-none text-left text-gray-700 text-xl/8" v-html="page.content"></div>
    </div>



    <NewsItems :newsItems="newsItems" />

    <Visit />

    <PageFooter :pages="pages" />
</template>
