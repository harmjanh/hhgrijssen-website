<template>
    <div class="bg-white py-24 sm:py-32 border-t border-gray-900/10">
        <div class="mx-auto max-w-7xl px-6 lg:px-8">
            <div class="mx-auto max-w-2xl lg:max-w-4xl">
                <h2 class="text-pretty text-4xl font-semibold tracking-tight text-gray-900 sm:text-5xl">Laatste
                    berichten
                </h2>
                <div class="mt-16 space-y-20 lg:mt-20 lg:space-y-20">
                    <article v-for="news in newsItems" :key="news.id"
                        class="relative isolate flex flex-col gap-8 lg:flex-row">
                        <div class="relative aspect-video sm:aspect-[2/1] lg:aspect-square lg:w-64 lg:shrink-0">
                            <img v-if="news.image" :src="'/storage/' + news.image" alt=""
                                class="absolute inset-0 size-full rounded-2xl bg-gray-50 object-cover" />
                            <div class="absolute inset-0 rounded-2xl ring-1 ring-inset ring-gray-900/10" />
                        </div>
                        <div class="w-full flex flex-col">
                            <div class="flex items-center gap-x-4 text-xs">
                                <time :datetime="news.updated_at" class="text-gray-500">{{
                                    formatDate(news.updated_at) }}</time>
                                <!-- <a :href="news.category.href"
                                    class="relative z-10 rounded-full bg-gray-50 px-3 py-1.5 font-medium text-gray-600 hover:bg-gray-100">{{
                                        news.category.title }}</a> -->
                            </div>
                            <div class="group relative max-w-xl">
                                <h3 class="mt-3 text-lg/6 font-semibold text-gray-900 group-hover:text-gray-600">
                                    <a :href="'/nieuws/' + news.id">
                                        <span class="absolute inset-0" />
                                        {{ news.title }}
                                    </a>
                                </h3>
                                <p class="mt-5 text-sm/6 text-gray-600">{{ news.description }}</p>
                            </div>
                            <div class="mt-6 flex border-t border-gray-900/5 pt-6 mt-auto">
                                <div class="relative flex items-center gap-x-4">

                                    <a :href="'/nieuws/' + news.id"
                                        class="text-sm font-semibold leading-6 hover:text-primary-500">
                                        Lees meer <span aria-hidden="true">→</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
defineProps<{
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
