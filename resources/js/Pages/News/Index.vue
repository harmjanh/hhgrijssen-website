<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import NavBar from "@/Components/NavBar.vue";
import PageFooter from "@/Components/PageFooter.vue";
import { Link } from '@inertiajs/vue3';

defineProps<{
    pages: { id: number; title: string; slug: string }[];
    news: {
        data: {
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
        links: Array<{
            url?: string;
            label: string;
            active: boolean;
        }>;
    };
}>();

const formatDate = (date: string) => {
    return new Date(date).toLocaleString('nl-NL', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Nieuws" />

    <NavBar :pages="pages" />

    <div class="bg-white py-24 sm:py-32">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:max-w-4xl">
                <h2 class="text-pretty text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl">Nieuws</h2>
                <div class="mt-16 space-y-20 lg:mt-20 lg:space-y-20">
                    <article v-for="item in news.data" :key="item.id"
                        class="relative isolate flex flex-col gap-8 lg:flex-row">
                        <div class="relative aspect-video sm:aspect-[2/1] lg:aspect-square lg:w-64 lg:shrink-0">
                            <img v-if="item.image" :src="'/storage/' + item.image" :alt="item.title"
                                class="absolute inset-0 size-full rounded-2xl bg-gray-50 object-cover" />
                            <div class="absolute inset-0 rounded-2xl ring-1 ring-inset ring-gray-900/10" />
                        </div>
                        <div>
                            <div class="flex items-center gap-x-4 text-xs">
                                <time :datetime="item.updated_at" class="text-gray-500">
                                    {{ formatDate(item.updated_at) }}
                                </time>
                            </div>
                            <div class="group relative max-w-xl">
                                <h3 class="mt-3 text-lg/6 font-semibold text-gray-900 group-hover:text-gray-600">
                                    <a :href="'/nieuws/' + item.id">
                                        <span class="absolute inset-0" />
                                        {{ item.title }}
                                    </a>
                                </h3>
                                <p class="mt-5 text-sm/6 text-gray-600">{{ item.description }}</p>
                            </div>
                            <div class="mt-6 flex border-t border-gray-900/5 pt-6">
                                <div class="relative flex items-center gap-x-4">
                                    <a :href="'/nieuws/' + item.id"
                                        class="text-sm font-semibold leading-6 text-indigo-600 hover:text-indigo-500">
                                        Lees meer <span aria-hidden="true">→</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Pagination -->
                <div class="mt-16 flex items-center justify-between border-t border-gray-200 px-4 py-3 sm:px-6">
                    <nav class="isolate inline-flex -space-x-px rounded-md shadow-sm" aria-label="Pagination">
                        <Link v-for="link in news.links"
                            :key="link.label"
                            :href="link.url || '#'"
                            :class="[
                                'relative inline-flex items-center px-4 py-2 text-sm font-semibold',
                                link.active
                                    ? 'z-10 bg-indigo-600 text-white focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600'
                                    : 'text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 focus:outline-offset-0',
                                !link.url && 'cursor-not-allowed opacity-50'
                            ]"
                            v-html="link.label"
                        />
                    </nav>
                </div>
            </div>
        </div>
    </div>

    <PageFooter :pages="pages" />
</template>
