<script setup lang="ts">
import { ref } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { ChevronDownIcon } from '@heroicons/vue/24/solid';
import { Disclosure, DisclosureButton, DisclosurePanel, Menu, MenuButton, MenuItem, MenuItems } from '@headlessui/vue';

interface AuthenticatedPage {
    id: number;
    title: string;
    slug: string;
    children?: AuthenticatedPage[];
}

const showingNavigationDropdown = ref(false);

const page = usePage();
const user = page.props.auth.user;
const authenticatedPages = (page.props.authenticatedPages || []) as AuthenticatedPage[];
const currentUrl = page.url;

const navigation = [
    { name: 'Website', href: '/', icon: '' },
    { name: 'Dashboard', href: route('dashboard'), icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Declaraties', href: route('declarations.index'), icon: 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z' },
    { name: 'Adreswijzigingen', href: route('address-submissions.index'), icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Munten Bestellen', href: route('coin-orders.index'), icon: 'M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z' },
    { name: 'Zaalreserveringen', href: route('room-reservations.index'), icon: 'M6 2v6h.01L6 8.01 10 12l-4 4 .01.01H6V22h12v-5.99h-.01L18 16l-4-4 4-3.99-.01-.01H18V2H6zm5 15.5V9H9v8.5l2-2z' },
];

// Add action buttons for creating declarations and ordering coins
const actionButtons = [
    { name: 'Nieuwe Declaratie', href: route('declarations.create'), icon: 'M12 4v16m8-8H4' },
    { name: 'Adreswijziging', href: route('address-submissions.create'), icon: 'M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6' },
    { name: 'Munten Bestellen', href: route('coin-orders.create'), icon: 'M12 6v6m0 0v6m0-6h6m-6 0H6' },
    { name: 'Zaal Reserveren', href: route('room-reservations.create'), icon: 'M6 2v6h.01L6 8.01 10 12l-4 4 .01.01H6V22h12v-5.99h-.01L18 16l-4-4 4-3.99-.01-.01H18V2H6zm5 15.5V9H9v8.5l2-2z' },
];

// Add admin navigation if user is admin
const adminNavigation = user?.role === 'admin' ? [
    { name: 'Admin', href: '/admin', icon: 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z' }
] : [];
</script>

<template>
    <div class="min-h-screen bg-gray-100">
        <div class="flex h-screen">
            <!-- Sidebar -->
            <div class="hidden md:flex md:flex-shrink-0">
                <div class="flex flex-col w-64">
                    <div class="flex flex-col flex-grow pt-5 pb-4 overflow-y-auto bg-white border-r">
                        <div class="flex items-center flex-shrink-0 px-4">
                            <ApplicationLogo class="block w-auto h-10 text-gray-600 fill-current" />
                        </div>
                        <div class="mt-5 flex-grow flex flex-col">
                            <nav class="flex-1 px-2 space-y-1">
                                <a v-for="item in navigation" :key="item.name" :href="item.href" :class="[
                                    route().current(item.href)
                                        ? 'bg-gray-100 text-gray-900'
                                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                    'group flex items-center px-2 py-2 text-sm font-medium rounded-md'
                                ]">
                                    <svg class="mr-3 h-6 w-6" :class="[
                                        route().current(item.href) ? 'text-gray-500' : 'text-gray-400 group-hover:text-gray-500'
                                    ]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            :d="item.icon" />
                                    </svg>
                                    {{ item.name }}
                                </a>

                                <!-- Action buttons -->
                                <div class="pt-4 mt-4 border-t border-gray-200">
                                    <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Acties
                                    </h3>
                                    <div class="mt-2 space-y-1">
                                        <a v-for="item in actionButtons" :key="item.name" :href="item.href" :class="[
                                            route().current(item.href)
                                                ? 'bg-indigo-50 text-indigo-700'
                                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                            'group flex items-center px-2 py-2 text-sm font-medium rounded-md'
                                        ]">
                                            <svg class="mr-3 h-6 w-6" :class="[
                                                route().current(item.href) ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500'
                                            ]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    :d="item.icon" />
                                            </svg>
                                            {{ item.name }}
                                        </a>
                                    </div>
                                </div>

                                <!-- Authenticated Pages -->
                                <template v-if="authenticatedPages.length > 0">
                                    <div class="pt-4 mt-4 border-t border-gray-200">
                                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Informatie
                                        </h3>
                                        <div class="mt-2 space-y-1">
                                            <template v-for="pageItem in authenticatedPages" :key="pageItem.id">
                                                <!-- Page without children -->
                                                <a v-if="!pageItem.children || pageItem.children.length === 0" 
                                                    :href="'/' + pageItem.slug"
                                                    :class="[
                                                        route().current('page.show', { slug: pageItem.slug }) || currentUrl === '/' + pageItem.slug
                                                            ? 'bg-gray-100 text-gray-900'
                                                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                                        'group flex items-center px-2 py-2 text-sm font-medium rounded-md'
                                                    ]">
                                                    <svg class="mr-3 h-6 w-6" :class="[
                                                        route().current('page.show', { slug: pageItem.slug }) || currentUrl === '/' + pageItem.slug 
                                                            ? 'text-gray-500' 
                                                            : 'text-gray-400 group-hover:text-gray-500'
                                                    ]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    {{ pageItem.title }}
                                                </a>
                                                <!-- Page with children -->
                                                <Disclosure v-else as="div" class="space-y-1">
                                                    <DisclosureButton :class="[
                                                        'group flex items-center w-full px-2 py-2 text-sm font-medium rounded-md',
                                                        'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                                                    ]">
                                                        <svg class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        {{ pageItem.title }}
                                                        <ChevronDownIcon class="ml-auto h-5 w-5 text-gray-400" />
                                                    </DisclosureButton>
                                                    <DisclosurePanel class="space-y-1 pl-9">
                                                        <a v-for="child in pageItem.children" :key="child.id"
                                                            :href="'/' + child.slug"
                                                            :class="[
                                                                route().current('page.show', { slug: child.slug }) || currentUrl === '/' + child.slug
                                                                    ? 'bg-gray-100 text-gray-900'
                                                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                                                'group flex items-center px-2 py-2 text-sm font-medium rounded-md'
                                                            ]">
                                                            {{ child.title }}
                                                        </a>
                                                    </DisclosurePanel>
                                                </Disclosure>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- Admin navigation -->
                                <template v-if="adminNavigation.length > 0">
                                    <div class="pt-4 mt-4 border-t border-gray-200">
                                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Beheer
                                        </h3>
                                        <div class="mt-2 space-y-1">
                                            <a v-for="item in adminNavigation" :key="item.name" :href="item.href"
                                                :class="[
                                                    route().current(item.href)
                                                        ? 'bg-gray-100 text-gray-900'
                                                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                                    'group flex items-center px-2 py-2 text-sm font-medium rounded-md'
                                                ]">
                                                <svg class="mr-3 h-6 w-6" :class="[
                                                    route().current(item.href) ? 'text-gray-500' : 'text-gray-400 group-hover:text-gray-500'
                                                ]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" :d="item.icon" />
                                                </svg>
                                                {{ item.name }}
                                            </a>
                                        </div>
                                    </div>
                                </template>
                            </nav>
                        </div>

                        <!-- Profile section -->
                        <div class="border-t border-gray-200 p-4">
                            <div class="flex items-center justify-between">
                                <div class="flex-shrink-0">
                                    <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                                    <p class="text-sm text-gray-500">{{ user.email }}</p>
                                </div>
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button class="text-gray-500 hover:text-gray-700">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path
                                                    d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">
                                            Profiel
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Uitloggen
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mobile menu -->
            <div v-show="showingNavigationDropdown" class="md:hidden">
                <div class="fixed inset-0 flex z-40">
                    <div class="fixed inset-0">
                        <div class="absolute inset-0 bg-gray-600 opacity-75" @click="showingNavigationDropdown = false">
                        </div>
                    </div>
                    <div class="relative flex-1 flex flex-col max-w-xs w-full bg-white">
                        <div class="absolute top-0 right-0 -mr-12 pt-2">
                            <button type="button"
                                class="ml-1 flex items-center justify-center h-10 w-10 rounded-full focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                                @click="showingNavigationDropdown = false">
                                <span class="sr-only">Close sidebar</span>
                                <svg class="h-6 w-6 text-white" xmlns="http://www.w3.org/2000/svg" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                        <div class="flex-1 h-0 pt-5 pb-4 overflow-y-auto">
                            <div class="flex-shrink-0 flex items-center px-4">
                                <ApplicationLogo class="block w-auto h-10 text-gray-600 fill-current" />
                            </div>
                            <nav class="mt-5 px-2 space-y-1">
                                <a v-for="item in navigation" :key="item.name" :href="item.href" :class="[
                                    route().current(item.href)
                                        ? 'bg-gray-100 text-gray-900'
                                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                    'group flex items-center px-2 py-2 text-base font-medium rounded-md'
                                ]">
                                    <svg class="mr-4 h-6 w-6" :class="[
                                        route().current(item.href) ? 'text-gray-500' : 'text-gray-400 group-hover:text-gray-500'
                                    ]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            :d="item.icon" />
                                    </svg>
                                    {{ item.name }}
                                </a>

                                <!-- Action buttons in mobile menu -->
                                <div class="pt-4 mt-4 border-t border-gray-200">
                                    <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        Acties
                                    </h3>
                                    <div class="mt-2 space-y-1">
                                        <a v-for="item in actionButtons" :key="item.name" :href="item.href" :class="[
                                            route().current(item.href)
                                                ? 'bg-indigo-50 text-indigo-700'
                                                : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                            'group flex items-center px-2 py-2 text-base font-medium rounded-md'
                                        ]">
                                            <svg class="mr-4 h-6 w-6" :class="[
                                                route().current(item.href) ? 'text-indigo-500' : 'text-gray-400 group-hover:text-gray-500'
                                            ]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" aria-hidden="true">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    :d="item.icon" />
                                            </svg>
                                            {{ item.name }}
                                        </a>
                                    </div>
                                </div>

                                <!-- Authenticated Pages in mobile menu -->
                                <template v-if="authenticatedPages.length > 0">
                                    <div class="pt-4 mt-4 border-t border-gray-200">
                                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Informatie
                                        </h3>
                                        <div class="mt-2 space-y-1">
                                            <template v-for="pageItem in authenticatedPages" :key="pageItem.id">
                                                <!-- Page without children -->
                                                <a v-if="!pageItem.children || pageItem.children.length === 0" 
                                                    :href="'/' + pageItem.slug"
                                                    :class="[
                                                        route().current('page.show', { slug: pageItem.slug }) || currentUrl === '/' + pageItem.slug
                                                            ? 'bg-gray-100 text-gray-900'
                                                            : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                                        'group flex items-center px-2 py-2 text-base font-medium rounded-md'
                                                    ]">
                                                    <svg class="mr-4 h-6 w-6" :class="[
                                                        route().current('page.show', { slug: pageItem.slug }) || currentUrl === '/' + pageItem.slug 
                                                            ? 'text-gray-500' 
                                                            : 'text-gray-400 group-hover:text-gray-500'
                                                    ]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                        stroke="currentColor" aria-hidden="true">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                    </svg>
                                                    {{ pageItem.title }}
                                                </a>
                                                <!-- Page with children -->
                                                <Disclosure v-else as="div" class="space-y-1">
                                                    <DisclosureButton :class="[
                                                        'group flex items-center w-full px-2 py-2 text-base font-medium rounded-md',
                                                        'text-gray-600 hover:bg-gray-50 hover:text-gray-900'
                                                    ]">
                                                        <svg class="mr-4 h-6 w-6 text-gray-400 group-hover:text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                            stroke="currentColor" aria-hidden="true">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                                        </svg>
                                                        {{ pageItem.title }}
                                                        <ChevronDownIcon class="ml-auto h-5 w-5 text-gray-400" />
                                                    </DisclosureButton>
                                                    <DisclosurePanel class="space-y-1 pl-9">
                                                        <a v-for="child in pageItem.children" :key="child.id"
                                                            :href="'/' + child.slug"
                                                            :class="[
                                                                route().current('page.show', { slug: child.slug }) || currentUrl === '/' + child.slug
                                                                    ? 'bg-gray-100 text-gray-900'
                                                                    : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                                                'group flex items-center px-2 py-2 text-base font-medium rounded-md'
                                                            ]">
                                                            {{ child.title }}
                                                        </a>
                                                    </DisclosurePanel>
                                                </Disclosure>
                                            </template>
                                        </div>
                                    </div>
                                </template>

                                <!-- Admin navigation in mobile menu -->
                                <template v-if="adminNavigation.length > 0">
                                    <div class="pt-4 mt-4 border-t border-gray-200">
                                        <h3 class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                            Beheer
                                        </h3>
                                        <div class="mt-2 space-y-1">
                                            <a v-for="item in adminNavigation" :key="item.name" :href="item.href"
                                                :class="[
                                                    route().current(item.href)
                                                        ? 'bg-gray-100 text-gray-900'
                                                        : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                                    'group flex items-center px-2 py-2 text-base font-medium rounded-md'
                                                ]">
                                                <svg class="mr-4 h-6 w-6" :class="[
                                                    route().current(item.href) ? 'text-gray-500' : 'text-gray-400 group-hover:text-gray-500'
                                                ]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor" aria-hidden="true">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2" :d="item.icon" />
                                                </svg>
                                                {{ item.name }}
                                            </a>
                                        </div>
                                    </div>
                                </template>
                            </nav>
                        </div>
                        <!-- Mobile profile section -->
                        <div class="flex-shrink-0 flex border-t border-gray-200 p-4">
                            <div class="flex items-center justify-between w-full">
                                <div>
                                    <p class="text-base font-medium text-gray-700">{{ user.name }}</p>
                                    <p class="text-sm font-medium text-gray-500">{{ user.email }}</p>
                                </div>
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button class="text-gray-500 hover:text-gray-700">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                                                fill="currentColor">
                                                <path
                                                    d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                        </button>
                                    </template>

                                    <template #content>
                                        <DropdownLink :href="route('profile.edit')">
                                            Profiel
                                        </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button">
                                            Uitloggen
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content area -->
            <div class="flex-1 flex flex-col overflow-hidden">
                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button type="button"
                        class="px-4 py-2 text-gray-500 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                        @click="showingNavigationDropdown = !showingNavigationDropdown">
                        <span class="sr-only">Open sidebar</span>
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>

                <!-- Page content -->
                <main class="flex-1 relative overflow-y-auto focus:outline-none">
                    <slot name="header" />
                    <div class="py-6">
                        <slot />
                    </div>
                </main>
            </div>
        </div>
    </div>
</template>
