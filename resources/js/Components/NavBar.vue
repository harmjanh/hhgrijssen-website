<template>
    <header class="bg-white">
        <nav class="mx-auto flex max-w-7xl flex-col p-6 lg:px-8" aria-label="Global">
            <div class="relative flex w-full items-center justify-center">
                <a href="#" class="-m-1.5 p-1.5">
                    <span class="sr-only">HHG Rijssen</span>
                    <img class="h-8 w-auto" src="/images/logo.png" alt="HHG Rijssen" />
                </a>
                <div class="absolute right-0 flex lg:hidden">
                    <button type="button"
                        class="-m-2.5 inline-flex items-center justify-center rounded-md p-2.5 text-gray-700"
                        @click="mobileMenuOpen = true">
                        <span class="sr-only">Open main menu</span>
                        <Bars3Icon class="size-6" aria-hidden="true" />
                    </button>
                </div>
            </div>
            <div class="mt-6 flex w-full items-center justify-between">
                <PopoverGroup class="hidden lg:flex lg:gap-x-12">
                    <div v-for="page in pages" :key="page.id">
                        <a :href="'/' + page.slug" v-if="page.children?.length === 0"
                            class="text-sm/6 font-semibold text-gray-900">{{ page.title }}</a>

                        <Popover class="relative" v-if="page.children?.length > 0">
                            <PopoverButton class="flex items-center gap-x-1 text-sm/6 font-semibold text-gray-900">
                                {{ page.title }}
                                <ChevronDownIcon class="size-5 flex-none text-gray-400" aria-hidden="true" />
                            </PopoverButton>

                            <transition enter-active-class="transition ease-out duration-200"
                                enter-from-class="opacity-0 translate-y-1" enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition ease-in duration-150"
                                leave-from-class="opacity-100 translate-y-0" leave-to-class="opacity-0 translate-y-1">
                                <PopoverPanel
                                    class="absolute -left-8 top-full z-10 mt-3 w-56 rounded-xl bg-white p-2 shadow-lg ring-1 ring-gray-900/5">
                                    <a v-for="item in page.children" :key="item.title" :href="'/' + item.slug"
                                        class="block rounded-lg px-3 py-2 text-sm/6 font-semibold text-gray-900 hover:bg-gray-50">{{
                                            item.title }}</a>
                                </PopoverPanel>
                            </transition>
                        </Popover>
                    </div>

                </PopoverGroup>
                <div class="hidden lg:flex lg:flex-1 lg:justify-end">
                    <!-- Not logged in -->
                    <a v-if="!$page.props.auth.user" :href="route('login')"
                        class="text-sm/6 font-semibold text-gray-900">
                        Log in <span aria-hidden="true">&rarr;</span>
                    </a>

                    <!-- Logged in with no role -->
                    <a v-else-if="$page.props.auth.user" :href="route('dashboard')"
                        class="text-sm/6 font-semibold text-gray-900">
                        Dashboard <span aria-hidden="true">&rarr;</span>
                    </a>
                </div>
            </div>
        </nav>
        <Dialog class="lg:hidden" @close="mobileMenuOpen = false" :open="mobileMenuOpen">
            <div class="fixed inset-0 z-10" />
            <DialogPanel
                class="fixed inset-y-0 right-0 z-10 w-full overflow-y-auto bg-white px-6 py-6 sm:max-w-sm sm:ring-1 sm:ring-gray-900/10">
                <div class="flex items-center justify-between">
                    <a href="#" class="-m-1.5 p-1.5">
                        <span class="sr-only">Your Company</span>
                        <img class="h-8 w-auto"
                            src="https://tailwindui.com/plus/img/logos/mark.svg?color=indigo&shade=600" alt="" />
                    </a>
                    <button type="button" class="-m-2.5 rounded-md p-2.5 text-gray-700" @click="mobileMenuOpen = false">
                        <span class="sr-only">Close menu</span>
                        <XMarkIcon class="size-6" aria-hidden="true" />
                    </button>
                </div>
                <div class="mt-6 flow-root">
                    <div class="-my-6 divide-y divide-gray-500/10">
                        <div class="space-y-2 py-6">
                            <Disclosure as="div" class="-mx-3" v-slot="{ open }">
                                <DisclosureButton
                                    class="flex w-full items-center justify-between rounded-lg py-2 pl-3 pr-3.5 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">
                                    Product
                                    <ChevronDownIcon :class="[open ? 'rotate-180' : '', 'size-5 flex-none']"
                                        aria-hidden="true" />
                                </DisclosureButton>
                                <DisclosurePanel class="mt-2 space-y-2">
                                    <DisclosureButton v-for="item in [...products, ...callsToAction]" :key="item.name"
                                        as="a" :href="item.href"
                                        class="block rounded-lg py-2 pl-6 pr-3 text-sm/7 font-semibold text-gray-900 hover:bg-gray-50">
                                        {{ item.name }}</DisclosureButton>
                                </DisclosurePanel>
                            </Disclosure>

                            <a href="#"
                                class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Features</a>
                            <a href="#"
                                class="-mx-3 block rounded-lg px-3 py-2 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Marketplace</a>

                            <Disclosure as="div" class="-mx-3" v-slot="{ open }">
                                <DisclosureButton
                                    class="flex w-full items-center justify-between rounded-lg py-2 pl-3 pr-3.5 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">
                                    Company
                                    <ChevronDownIcon :class="[open ? 'rotate-180' : '', 'size-5 flex-none']"
                                        aria-hidden="true" />
                                </DisclosureButton>
                                <DisclosurePanel class="mt-2 space-y-2">
                                    <DisclosureButton v-for="item in company" :key="item.name" as="a" :href="item.href"
                                        class="block rounded-lg py-2 pl-6 pr-3 text-sm/7 font-semibold text-gray-900 hover:bg-gray-50">
                                        {{ item.name }}</DisclosureButton>
                                </DisclosurePanel>
                            </Disclosure>
                        </div>
                        <div class="py-6">
                            <!-- Not logged in -->
                            <a v-if="!$page.props.auth.user" :href="route('login')"
                                class="-mx-3 block rounded-lg px-3 py-2.5 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Log
                                in</a>

                            <!-- Logged in with no role -->
                            <a v-else-if="$page.props.auth.user && !$page.props.auth.user.role"
                                :href="route('dashboard')"
                                class="-mx-3 block rounded-lg px-3 py-2.5 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Dashboard</a>

                            <!-- Logged in with admin role -->
                            <a v-else-if="$page.props.auth.user && $page.props.auth.user.role === 'admin'"
                                :href="'/admin'"
                                class="-mx-3 block rounded-lg px-3 py-2.5 text-base/7 font-semibold text-gray-900 hover:bg-gray-50">Admin</a>
                        </div>
                    </div>
                </div>
            </DialogPanel>
        </Dialog>
    </header>
</template>

<script setup>
import { ref } from 'vue'
import {
    Dialog,
    DialogPanel,
    Disclosure,
    DisclosureButton,
    DisclosurePanel,
    Popover,
    PopoverButton,
    PopoverGroup,
    PopoverPanel,
} from '@headlessui/vue'
import {
    ArrowPathIcon,
    Bars3Icon,
    ChartPieIcon,
    CursorArrowRaysIcon,
    FingerPrintIcon,
    SquaresPlusIcon,
    XMarkIcon,
} from '@heroicons/vue/24/outline'
import { ChevronDownIcon, PhoneIcon, PlayCircleIcon } from '@heroicons/vue/20/solid'
import { usePage } from '@inertiajs/vue3'

defineProps({
    pages: {
        type: Array,
        default: () => [],
    }
});

const mobileMenuOpen = ref(false)
</script>
