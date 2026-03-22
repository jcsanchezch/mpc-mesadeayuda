<script setup>
import { ref, computed } from 'vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';

defineProps({
    title: { type: String, default: '' },
});

const page      = usePage();
const user      = computed(() => page.props.auth.user);
const secciones = computed(() => page.props.auth.secciones ?? []);
const collapsed = ref(false);

// -----------------------------------------------------------------------
// Menú
// -----------------------------------------------------------------------
const allItems = [
    {
        key:   null,
        label: 'Inicio',
        path:  '/dashboard',
        icon:  'M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25',
        color: 'stone',
    },
    {
        key:   'mis_tickets',
        label: 'Mis Tickets',
        path:  '/mis-tickets',
        icon:  'M16.5 6v.75a3 3 0 01-3 3h-3a3 3 0 01-3-3V6m10.5 0H6.75M6.75 6A2.25 2.25 0 004.5 8.25v11.25A2.25 2.25 0 006.75 21.75h10.5A2.25 2.25 0 0019.5 19.5V8.25A2.25 2.25 0 0017.25 6',
        color: 'teal',
    },
    {
        key:   'mesa_servicio',
        label: 'Mesa de Servicio',
        path:  '/mesa-servicio',
        icon:  'M20.25 8.511c.884.284 1.5 1.128 1.5 2.097v4.286c0 1.136-.847 2.1-1.98 2.193-.34.027-.68.052-1.02.072v3.091l-3-3c-1.354 0-2.694-.055-4.02-.163a2.115 2.115 0 01-.825-.242m9.345-8.334a2.126 2.126 0 00-.476-.095 48.64 48.64 0 00-8.048 0c-1.131.094-1.976 1.057-1.976 2.192v4.286c0 .837.46 1.58 1.155 1.951m9.345-8.334V6.637c0-1.621-1.152-3.026-2.76-3.235A48.455 48.455 0 0011.25 3c-2.115 0-4.198.137-6.24.402-1.608.209-2.76 1.614-2.76 3.235v6.226c0 1.621 1.152 3.026 2.76 3.235.577.075 1.157.14 1.74.194V21l4.155-4.155',
        color: 'blue',
    },
    {
        key:   'reportes',
        label: 'Reportes',
        path:  '/reportes',
        icon:  'M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z',
        color: 'amber',
    },
    {
        key:   'admin',
        label: 'Administración',
        path:  '/admin',
        icon:  'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.076.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a7.003 7.003 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z M15 12a3 3 0 11-6 0 3 3 0 016 0z',
        color: 'rose',
    },
];

const visibleItems = computed(() =>
    allItems.filter(item => item.key === null || secciones.value.includes(item.key))
);

function isActive(path) {
    if (path === '/dashboard') return page.url === '/dashboard';
    return page.url.startsWith(path);
}

const activeClasses = {
    stone: 'bg-stone-100 text-stone-900',
    teal:  'bg-teal-50 text-teal-700',
    blue:  'bg-blue-50 text-blue-700',
    amber: 'bg-amber-50 text-amber-700',
    rose:  'bg-rose-50 text-rose-700',
};

const activeIconClasses = {
    stone: 'text-stone-600',
    teal:  'text-teal-600',
    blue:  'text-blue-600',
    amber: 'text-amber-600',
    rose:  'text-rose-600',
};

const activeDotClasses = {
    stone: 'bg-stone-400',
    teal:  'bg-teal-500',
    blue:  'bg-blue-500',
    amber: 'bg-amber-500',
    rose:  'bg-rose-500',
};

function navClass(item) {
    return isActive(item.path)
        ? activeClasses[item.color]
        : 'text-stone-500 hover:bg-stone-50 hover:text-stone-700';
}

function iconClass(item) {
    return isActive(item.path) ? activeIconClasses[item.color] : 'text-stone-400 group-hover:text-stone-500';
}

const initials = computed(() =>
    (user.value?.name ?? '?')
        .split(' ')
        .map(w => w[0])
        .slice(0, 2)
        .join('')
        .toUpperCase()
);

function logout() {
    router.post(route('logout'));
}
</script>

<template>
    <Head :title="title ? `${title} · Mesa de Ayuda` : 'Mesa de Ayuda'" />

    <div class="flex min-h-screen bg-stone-50 text-stone-800">

        <!-- ================================================================
             SIDEBAR
        ================================================================ -->
        <aside
            class="sticky top-0 flex h-screen flex-shrink-0 flex-col overflow-hidden border-r border-stone-200 bg-white transition-all duration-300"
            :class="collapsed ? 'w-[66px]' : 'w-60'"
        >
            <!-- Cabecera -->
            <div
                class="flex h-14 flex-shrink-0 items-center gap-3 border-b border-stone-100 px-3"
                :class="collapsed ? 'justify-center' : ''"
            >
                <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-lg bg-teal-600 text-[11px] font-bold tracking-wide text-white shadow-sm">
                    MPC
                </div>
                <div v-if="!collapsed" class="min-w-0 overflow-hidden">
                    <p class="truncate text-[13px] font-semibold leading-tight text-stone-800">Mesa de Ayuda</p>
                    <p class="truncate text-[11px] text-stone-400">Municipalidad Provincial</p>
                </div>
            </div>

            <!-- Navegación -->
            <nav class="flex-1 overflow-y-auto px-2 py-3">
                <p
                    v-if="!collapsed"
                    class="mb-1 px-2 text-[10px] font-semibold uppercase tracking-widest text-stone-400"
                >
                    Navegación
                </p>

                <Link
                    v-for="item in visibleItems"
                    :key="item.path"
                    :href="item.path"
                    class="group relative mb-0.5 flex items-center gap-2.5 rounded-lg px-2 py-2 text-[13px] font-medium transition-colors"
                    :class="[navClass(item), collapsed ? 'justify-center' : '']"
                    :title="collapsed ? item.label : undefined"
                >
                    <!-- Indicador activo -->
                    <span
                        v-if="isActive(item.path)"
                        class="absolute left-0 top-1/2 h-4 w-0.5 -translate-y-1/2 rounded-full"
                        :class="activeDotClasses[item.color]"
                    />

                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke-width="1.75"
                        stroke="currentColor"
                        class="h-[18px] w-[18px] flex-shrink-0 transition-colors"
                        :class="iconClass(item)"
                        v-html="`<path stroke-linecap='round' stroke-linejoin='round' d='${item.icon}'/>`"
                    />
                    <span v-if="!collapsed" class="truncate">{{ item.label }}</span>
                </Link>
            </nav>

            <!-- Usuario -->
            <div class="border-t border-stone-100 px-2 py-3">
                <div
                    class="flex items-center gap-2.5 rounded-lg px-2 py-2"
                    :class="collapsed ? 'justify-center' : ''"
                >
                    <div class="flex h-7 w-7 flex-shrink-0 items-center justify-center rounded-full bg-teal-100 text-[11px] font-bold text-teal-700">
                        {{ initials }}
                    </div>
                    <div v-if="!collapsed" class="min-w-0 flex-1 overflow-hidden">
                        <p class="truncate text-[13px] font-medium text-stone-700">{{ user?.name }}</p>
                        <p class="truncate text-[11px] text-stone-400">{{ user?.email }}</p>
                    </div>
                </div>

                <!-- Botón cerrar sesión -->
                <button
                    class="mt-1 flex w-full items-center gap-2.5 rounded-lg px-2 py-2 text-[13px] font-medium text-stone-400 transition hover:bg-red-50 hover:text-red-600"
                    :class="collapsed ? 'justify-center' : ''"
                    :title="collapsed ? 'Cerrar sesión' : undefined"
                    @click="logout"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-[18px] w-[18px] flex-shrink-0">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                    </svg>
                    <span v-if="!collapsed">Cerrar sesión</span>
                </button>

                <!-- Botón colapsar -->
                <button
                    class="mt-1 flex w-full items-center gap-2.5 rounded-lg px-2 py-2 text-[13px] text-stone-400 transition hover:bg-stone-50 hover:text-stone-600"
                    :class="collapsed ? 'justify-center' : ''"
                    :title="collapsed ? 'Expandir panel' : undefined"
                    @click="collapsed = !collapsed"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" class="h-[18px] w-[18px] flex-shrink-0 transition-transform duration-300" :class="collapsed ? 'rotate-180' : ''">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
                    </svg>
                    <span v-if="!collapsed">Colapsar panel</span>
                </button>
            </div>
        </aside>

        <!-- ================================================================
             CONTENIDO PRINCIPAL
        ================================================================ -->
        <div class="flex min-w-0 flex-1 flex-col">
            <slot />
        </div>

    </div>
</template>
