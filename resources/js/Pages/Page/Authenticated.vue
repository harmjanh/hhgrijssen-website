<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PageHeader from '@/Components/PageHeader.vue';

defineProps({
    page: {
        type: Object,
        required: true
    }
});
</script>

<template>
    <Head :title="page.title" />

    <AuthenticatedLayout>
        <template #header>
            <PageHeader :title="page.title" />
        </template>

        <div class="py-6">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div v-if="page.header_image" class="mb-6">
                            <img :src="'/storage/' + page.header_image" :alt="page.title"
                                class="w-full h-64 object-cover rounded-lg" />
                        </div>
                        <div class="prose max-w-none text-gray-700" v-html="page.content"></div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

