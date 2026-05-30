<template>
    <div>
        <h2 class="text-pretty text-2xl font-semibold tracking-tight text-gray-900 sm:text-3xl">
            Laatste berichten
        </h2>
        <div class="mt-6 divide-y divide-gray-100">
            <article v-for="news in newsItems" :key="news.id"
                class="group relative isolate flex gap-3 py-4 first:pt-0">
                <div v-if="news.image" class="relative size-14 shrink-0 overflow-hidden rounded-lg">
                    <img :src="'/storage/' + news.image" :alt="news.title"
                        class="size-full bg-gray-50 object-cover" />
                    <div class="absolute inset-0 rounded-lg ring-1 ring-inset ring-gray-900/10" />
                </div>
                <div class="min-w-0 flex-1">
                    <time :datetime="news.updated_at" class="text-xs text-gray-500">
                        {{ formatDate(news.updated_at) }}
                    </time>
                    <h3 class="mt-0.5 text-sm font-semibold leading-snug text-gray-900 group-hover:text-primary-600">
                        <a :href="'/nieuws/' + news.slug">
                            <span class="absolute inset-0" />
                            {{ news.title }}
                        </a>
                    </h3>
                    <p v-if="news.description" class="mt-1 line-clamp-2 text-xs leading-relaxed text-gray-600">
                        {{ news.description }}
                    </p>
                </div>
            </article>
        </div>
        <a href="/nieuws"
            class="mt-6 inline-block text-sm font-semibold text-gray-900 hover:text-primary-500">
            Alle berichten <span aria-hidden="true">→</span>
        </a>
    </div>
</template>

<script setup lang="ts">
defineProps<{
    newsItems: {
        id: number;
        slug: string;
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
    });
};

</script>
