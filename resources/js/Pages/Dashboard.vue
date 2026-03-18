<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';

defineProps({
    logoutUrl: { type: String, required: true },
    permissions: { type: Array, default: () => [] },
    roles: { type: Array, default: () => [] },
});

const page = usePage();
</script>

<template>
    <Head title="Dashboard" />

    <main class="min-h-screen bg-stone-950 px-6 py-10 text-stone-100">
        <div class="mx-auto max-w-5xl space-y-8">
            <header class="flex flex-col gap-5 rounded-3xl border border-white/10 bg-white/5 p-8 shadow-xl shadow-black/20 lg:flex-row lg:items-start lg:justify-between">
                <div>
                    <p class="text-sm font-semibold uppercase tracking-[0.3em] text-teal-300">Acceso activo</p>
                    <h1 class="mt-3 text-3xl font-semibold tracking-tight">Bienvenido, {{ page.props.auth.user?.name }}</h1>
                    <p class="mt-3 max-w-2xl text-sm leading-6 text-stone-300">
                        El sistema esta configurado para ingreso por usuarios creados internamente. No hay registro publico ni recuperacion automatica de contrasena.
                    </p>
                </div>

                <Link
                    :href="logoutUrl"
                    class="inline-flex items-center justify-center rounded-2xl border border-white/15 bg-white/10 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/20"
                >
                    Ir a cierre de sesion
                </Link>
            </header>

            <section class="grid gap-6 lg:grid-cols-2">
                <article class="rounded-3xl border border-white/10 bg-white p-8 text-stone-900 shadow-lg shadow-black/10">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-teal-700">Rol actual</p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        <span
                            v-for="role in roles"
                            :key="role"
                            class="rounded-full bg-teal-100 px-4 py-2 text-sm font-semibold text-teal-800"
                        >
                            {{ role }}
                        </span>
                        <span
                            v-if="roles.length === 0"
                            class="rounded-full bg-stone-100 px-4 py-2 text-sm font-semibold text-stone-600"
                        >
                            Sin roles asignados
                        </span>
                    </div>
                </article>

                <article class="rounded-3xl border border-white/10 bg-white/5 p-8 shadow-lg shadow-black/10">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-amber-300">Permisos disponibles</p>
                    <div class="mt-5 flex flex-wrap gap-3">
                        <span
                            v-for="permission in permissions"
                            :key="permission"
                            class="rounded-full border border-amber-300/30 bg-amber-300/10 px-4 py-2 text-sm font-medium text-amber-100"
                        >
                            {{ permission }}
                        </span>
                        <span
                            v-if="permissions.length === 0"
                            class="rounded-full border border-stone-300/20 bg-white/5 px-4 py-2 text-sm font-medium text-stone-300"
                        >
                            Sin permisos asignados
                        </span>
                    </div>
                </article>
            </section>

            <section class="rounded-3xl border border-teal-400/20 bg-teal-400/10 p-8 shadow-lg shadow-black/10">
                <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                    <div>
                        <p class="text-sm font-semibold uppercase tracking-[0.25em] text-teal-200">Resumen de sesion</p>
                        <p class="mt-3 text-lg font-semibold text-white">{{ page.props.auth.user?.email }}</p>
                        <p class="mt-2 text-sm leading-6 text-stone-300">
                            Desde aqui puedes validar tu acceso actual y salir del sistema de manera segura cuando termines.
                        </p>
                    </div>

                    <Link
                        :href="logoutUrl"
                        class="inline-flex items-center justify-center rounded-2xl bg-white px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-teal-100"
                    >
                        Cerrar sesion
                    </Link>
                </div>
            </section>
        </div>
    </main>
</template>
