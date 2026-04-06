<script setup>
import {ref} from 'vue';
import {Link, usePage} from "@inertiajs/vue3";
import imgLogoTextoDerecha from '../../images/logoTextoDerecha.png';
import imgLogo from '../../images/logo.png';
import {route} from "ziggy-js";
import navigation from '../navigation.json';

defineProps({
    collapsed: {type: Boolean, default: false}
});

const appName = import.meta.env.VITE_APP_NAME ?? 'App Name';

const isChildActive = (child) => {
    if (!route().current(child.pattern)) return false;
    if (!child.query) return true;
    const url = new URL(window.location.href);
    return Object.entries(child.query).every(([k, v]) => url.searchParams.get(k) === v);
};

const isGroupActive = (group) => group.children?.some(c => isChildActive(c)) ?? false;

const openGroups = ref(
    Object.fromEntries(navigation.filter(i => i.children).map(i => [i.name, isGroupActive(i)]))
);
const toggleGroup = (name) => {
    openGroups.value[name] = !openGroups.value[name];
};


const childHref = (child) => {
    const base = route(child.route);
    if (!child.query) return base;
    return base + '?' + new URLSearchParams(child.query).toString();
};


const page = usePage();
const user = page.props.auth?.user;



</script>

<template>
    <!-- Logo -->
    <div
        :class="['flex flex-col items-center px-0 leading-4 transition-all duration-300', collapsed ? 'my-4' : 'my-8']">
        <img :src="collapsed ? imgLogo : imgLogoTextoDerecha" :class="collapsed ? 'w-10' : 'w-36'" alt="Logo"/>
        <div v-if="!collapsed"
             class="mt-3 px-6 py-2.5 text-center inline-block text-[20px] font-bold tracking-normal leading-6 bg-cyan-100 text-sky-700 border-b-2 border-b-cyan-200">
            {{appName}}
        </div>
    </div>

    <!-- Navegación -->
    <div class="flex grow flex-col">
        <nav class="flex-1 space-y-1" :class="collapsed ? 'px-1' : 'px-2'">
            <div v-if="!collapsed" class="text-gray-400 uppercase py-1 px-2 text-xs font-medium tracking-wider">Menu
            </div>

            <div v-if="collapsed" class="h-6"></div>

            <template v-for="item in navigation" :key="item.name">
                <!-- Enlace directo (sin children) -->
                <Link v-if="!item.children"
                      :href="route(item.route)"
                      :class="[
                        route().current(item.pattern) ? 'bg-blue-200 text-blue-700' : 'text-gray-600 hover:bg-gray-300 hover:text-gray-700',
                        collapsed ? 'justify-center text-lg py-3 ' : ' text-sm py-2 ',
                        'group flex items-center px-4 font-medium rounded-[4px]'
                    ]"
                      :title="collapsed ? item.name : ''">
                    <i :class="[item.icon, collapsed ? '' : 'mr-1.5']"></i>
                    <span v-if="!collapsed">{{ item.name }}</span>
                </Link>

                <!-- colapsable (con children) -->
                <div v-else>
                    <button
                        @click="toggleGroup(item.name)"
                        :class="[
                            isGroupActive(item) || openGroups[item.name] ? 'bg-blue-200 text-blue-700' : 'text-gray-600 hover:bg-gray-300 hover:text-gray-700',
                            collapsed ? 'justify-center text-lg py-3' : 'text-sm py-2 justify-between',
                            'w-full flex items-center px-4 text-sm font-medium rounded-[4px]'
                        ]"
                        :title="collapsed ? item.name : ''">
                        <span class="flex items-center" :class="collapsed ? '' : 'gap-1.5'">
                            <i :class="[item.icon]"></i>
                            <span v-if="!collapsed">{{ item.name }}</span>
                        </span>
                        <i v-if="!collapsed"
                           :class="['fa-solid fa-chevron-down text-xs transition-transform duration-200', openGroups[item.name] ? 'rotate-180' : '']">
                        </i>
                    </button>

                    <!-- Submenús (sidebar normal y colapsado) -->
                    <div v-if="openGroups[item.name]" class=" mt-0.5 p-0.5 space-y-0.5 rounded-[4px] bg-gray-200 border-b-2 border-b-gray-200"
                         :class="collapsed
                            ? ' '
                            : ' '">
                        <Link v-for="child in item.children" :key="child.name"
                              :href="childHref(child)"
                              :title="collapsed ? child.name : ''"
                              :class="[
                                isChildActive(child) ? 'bg-blue-200 text-blue-700' : 'text-gray-600 hover:bg-gray-300 hover:text-gray-700',
                                collapsed ? 'justify-center py-2' : 'gap-2 px-4 py-2',
                                'flex items-center text-sm font-medium rounded-[4px]'
                            ]">
                            <i v-if="child.icon" :class="child.icon" class="text-xs w-3.5 text-center"></i>
                            <span v-if="!collapsed">{{ child.name }}</span>
                        </Link>
                    </div>
                </div>
            </template>
        </nav>
    </div>

</template>

