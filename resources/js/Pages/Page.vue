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

    <div class="mx-auto w-3/5 py-12">
        <h1 class="mb-6 text-3xl font-bold tracking-tight text-gray-900 text-left">{{ page.title }}</h1>
        <div class="prose max-w-none text-left text-gray-700" v-html="page.content"></div>
    </div>

    <PageFooter :pages="pages" />

</template>
