<script setup>
import { ref } from 'vue';
import { Link, usePage } from "@inertiajs/vue3";
import logoImage from '../../images/logo.png';
import logoImage2 from '../../images/logo2.png';
import logoImage3 from '../../images/logo3.png';
import { route } from "ziggy-js";

defineProps({
    collapsed: { type: Boolean, default: false }
});

const navigation = [
    {
        name: 'Mis Tickets',
        icon: 'fa-regular fa-rectangle-list',
        route: 'solicitante.index',
        pattern: 'solicitante.index',
    },
    {
        name: 'Mesa de Servicio',
        icon: 'fa-solid fa-headset',
        route: 'mesadeayuda.tickets.index',
        pattern: 'mesadeayuda.tickets.index',
    },
];

const openGroups = ref({});
const toggleGroup = (name) => { openGroups.value[name] = !openGroups.value[name]; };
const isGroupActive = (group) => group.children?.some(c => route().current(c.pattern)) ?? false;

const page = usePage();
const user = page.props.auth?.user;
</script>

<template>
    <!-- Logo -->
    <div :class="['flex flex-col items-center px-0 leading-4 transition-all duration-300', collapsed ? 'my-4' : 'my-8']">

        <img :src="collapsed ? logoImage3 : logoImage" :class="collapsed ? 'w-8' : 'w-36'" alt="Logo" />

        <div v-if="!collapsed"
            class="mt-3 px-6 py-2.5 text-center inline-block text-[20px] font-bold tracking-normal leading-6 bg-cyan-100 text-sky-700 border-b-2 border-b-cyan-200">
            Mesa de Ayuda
        </div>
    </div>

    <!-- Navegación -->
    <div class="flex grow flex-col">
        <nav class="flex-1 space-y-1">
            <div v-if="!collapsed" class="text-gray-400 uppercase py-1 px-2 text-xs font-medium tracking-wider">Menu</div>

            <Link :href="route('home')"
                :class="[
                    route().current('home') ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-200 hover:text-gray-700',
                    collapsed ? 'justify-center text-lg' : ' text-sm',
                    'group flex items-center px-4 py-2.5 font-medium'
                ]"
                :title="collapsed ? 'Home' : ''">
                <i class="fa-regular fa-house" :class="collapsed ? '' : 'mr-1.5'"></i>
                <span v-if="!collapsed">Home</span>
            </Link>

            <template v-for="item in navigation" :key="item.name">
                <!-- Enlace directo (sin children) -->
                <Link v-if="!item.children"
                    :href="route(item.route)"
                    :class="[
                        route().current(item.pattern) ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:bg-gray-200 hover:text-gray-700',
                        collapsed ? 'justify-center text-lg' : ' text-sm',
                        'group flex items-center px-4 py-2.5 font-medium'
                    ]"
                    :title="collapsed ? item.name : ''">
                    <i :class="[item.icon, collapsed ? '' : 'mr-1.5']"></i>
                    <span v-if="!collapsed">{{ item.name }}</span>
                </Link>

                <!-- Grupo colapsable (con children) -->
                <div v-else>
                    <button
                        @click="!collapsed && toggleGroup(item.name)"
                        :class="[
                            isGroupActive(item) ? 'text-blue-600' : 'text-gray-600 hover:bg-gray-200 hover:text-gray-700',
                            collapsed ? 'justify-center' : 'justify-between',
                            'w-full flex items-center px-4 py-2.5 text-sm font-medium'
                        ]"
                        :title="collapsed ? item.name : ''">
                        <div class="flex items-center" :class="collapsed ? '' : 'gap-1.5'">
                            <i :class="[item.icon]"></i>
                            <span v-if="!collapsed">{{ item.name }}</span>
                        </div>
                        <i v-if="!collapsed"
                            :class="['fa-solid fa-chevron-down text-xs transition-transform duration-200', openGroups[item.name] ? 'rotate-180' : '']">
                        </i>
                    </button>
                    <div v-if="!collapsed && openGroups[item.name]" class="ml-3 mt-0.5 space-y-0.5 border-l border-gray-300 pl-2">
                        <Link v-for="child in item.children" :key="child.name"
                            :href="route(child.route)"
                            :class="[
                                route().current(child.pattern) ? 'bg-blue-500 text-blue-50' : 'text-gray-600 hover:bg-gray-200 hover:text-gray-700',
                                'flex items-center px-4 py-2.5 text-sm font-medium'
                            ]">
                            {{ child.name }}
                        </Link>
                    </div>
                </div>
            </template>
        </nav>
    </div>

    <!-- Usuario -->
    <div class="border-t border-gray-200 py-3 mt-2">
        <div v-if="!collapsed" class="px-2 mb-2 space-y-0.5">
            <div class="text-xs font-medium text-gray-600 truncate">{{ user?.nombres }} {{ user?.paterno }} {{ user?.materno }}</div>
            <div class="text-xs text-gray-400 truncate">{{ user?.usuario }} ({{ user?.dni }})</div>
        </div>
        <div v-else class="flex justify-center mb-2">
            <div class="w-8 h-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-xs font-bold"
                :title="`${user?.nombres} ${user?.paterno}`">
                {{ user?.nombres?.charAt(0) }}{{ user?.paterno?.charAt(0) }}
            </div>
        </div>
        <Link :href="route('perfil')"
            :class="[
                route().current('perfil') ? 'bg-blue-100 text-blue-700' : 'text-gray-500 hover:bg-gray-200 hover:text-gray-700',
                'w-full text-sm py-2.5 transition flex items-center',
                collapsed ? 'justify-center px-4' : 'px-4 gap-1.5'
            ]"
            :title="collapsed ? 'Mi Perfil' : ''">
            <i class="fa-solid fa-user"></i>
            <span v-if="!collapsed">Mi Perfil</span>
        </Link>
        <Link :href="route('logout')" method="post" as="button"
            :class="[
                'w-full text-sm text-red-500 hover:bg-red-50 py-2.5 transition flex items-center',
                collapsed ? 'justify-center px-4' : 'px-4 gap-1.5'
            ]"
            :title="collapsed ? 'Cerrar Sesión' : ''">
            <i class="fa-solid fa-right-from-bracket"></i>
            <span v-if="!collapsed">Cerrar Sesión</span>
        </Link>
    </div>
</template>

