<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import Dropdown from "@/Components/Dropdown.vue";
import Siderbar from "@/Layouts/Siderbar.vue";
import { Dialog, DialogPanel, TransitionChild, TransitionRoot } from "@headlessui/vue";
import { route } from "ziggy-js";

const sidebarOpen = ref(false);
const collapsed = ref(false);
</script>

<template>
    <div class="min-w-6xl">
        <!-- Sidebar móvil -->
        <TransitionRoot as="template" :show="sidebarOpen">
            <Dialog as="div" class="relative z-40 lg:hidden" @close="sidebarOpen = false">
                <TransitionChild as="template" enter="transition-opacity ease-linear duration-300"
                    enter-from="opacity-0" enter-to="opacity-100" leave="transition-opacity ease-linear duration-300"
                    leave-from="opacity-100" leave-to="opacity-0">
                    <div class="fixed inset-0 bg-gray-600 bg-opacity-75" />
                </TransitionChild>
                <div class="fixed inset-0 z-20 flex">
                    <TransitionChild as="template" enter="transition ease-in-out duration-300 transform"
                        enter-from="-translate-x-full" enter-to="translate-x-0"
                        leave="transition ease-in-out duration-300 transform" leave-from="translate-x-0"
                        leave-to="-translate-x-full">
                        <DialogPanel class="relative flex w-full max-w-64 flex-1 flex-col bg-gray-100 pt-5 pb-4 px-3">
                            <TransitionChild as="template" enter="ease-in-out duration-300" enter-from="opacity-0"
                                enter-to="opacity-100" leave="ease-in-out duration-300" leave-from="opacity-100"
                                leave-to="opacity-0">
                                <div class="absolute top-0 right-0 -mr-12 pt-2">
                                    <button type="button"
                                        class="text-[24px] text-white ml-1 flex h-10 w-10 items-center justify-center rounded-full focus:outline-hidden focus:ring-2 focus:ring-inset focus:ring-white"
                                        @click="sidebarOpen = false">
                                        <span class="sr-only">Close sidebar</span>
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </div>
                            </TransitionChild>
                            <Siderbar :collapsed="false" />
                        </DialogPanel>
                    </TransitionChild>
                    <div class="w-14 shrink-0" aria-hidden="true" />
                </div>
            </Dialog>
        </TransitionRoot>

        <!-- Sidebar escritorio -->
        <div :class="[
            'hidden lg:fixed lg:inset-y-0 lg:flex lg:flex-col transition-all duration-300 ease-in-out',
            collapsed ? 'lg:w-16' : 'lg:w-64'
        ]">
            <div class="flex grow flex-col overflow-y-auto overflow-x-hidden bg-gray-100 px-2">
                <Siderbar :collapsed="collapsed" />
            </div>
        </div>

        <!-- Contenido principal -->
        <div :class="[
            'flex flex-1 flex-col transition-all duration-300 ease-in-out',
            collapsed ? 'lg:pl-16' : 'lg:pl-64'
        ]">
            <div class="sticky top-0 z-10 flex h-16 shrink-0 bg-white border-b border-b-gray-200">
                <!-- Botón hamburger móvil -->
                <button type="button"
                    class="text-[24px] pt-1 border-r border-gray-200 px-3.5 text-gray-500 focus:outline-hidden lg:hidden"
                    @click="sidebarOpen = true">
                    <span class="sr-only">Open sidebar</span>
                    <i class="fa-solid fa-bars"></i>
                </button>
                <!-- Botón collapse escritorio -->
                <button type="button"
                    class="hidden lg:flex items-center border-r border-gray-200 px-3.5 text-gray-400 hover:text-gray-600 transition-colors"
                    @click="collapsed = !collapsed">
                    <i :class="collapsed ? 'fa-solid fa-bars' : 'fa-solid fa-bars-staggered'"></i>
                </button>
                <div class="flex flex-1 justify-between px-2 sm:px-6">
                    <div class="flex flex-1 items-center">
                        <h2 class="font-light text-[22px] text-gray-600 leading-none">
                            <slot name="header" />
                        </h2>
                    </div>
                </div>
                <!--
                <div class="flex items-center justify-end px-2 sm:px-6">
                    <Dropdown width="48">
                        <template #trigger>
                            {{ $page.props.auth.user.nombres }} ({{ $page.props.auth.user.dni }})
                        </template>
                        <template #content>
                            <Link :href="route('perfil')"
                                class="block w-full px-4 py-2 text-start text-sm leading-5 text-gray-500 hover:bg-blue-100 hover:text-blue-500 focus:outline-none focus:bg-blue-200 transition duration-150 ease-in-out group">
                                <i class="fa-solid fa-user mr-1 text-xs text-gray-500 group-hover:text-blue-500"></i>
                                Mi Perfil
                            </Link>
                            <Link :href="route('logout')" method="post" as="button"
                                class="block w-full px-4 py-2 text-start text-sm leading-5 text-red-500 hover:bg-red-100 hover:text-red-500 focus:outline-none focus:bg-red-200 transition duration-150 ease-in-out group cursor-pointer">
                                <i class="fa-solid fa-lock mr-1 text-xs text-red-500"></i>
                                Cerrar Sesión
                            </Link>
                        </template>
                    </Dropdown>
                </div>
                -->
            </div>
            <main class="py-2 sm:py-6 px-2 sm:px-6">
                <slot />
            </main>
        </div>
    </div>
</template>
