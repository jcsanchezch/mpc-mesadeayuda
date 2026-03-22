<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

const page = usePage();
const form = useForm({});

const submit = () => {
    form.post(route('logout'));
};
</script>

<template>
    <Head title="Cerrar sesión" />

    <main class="relative flex min-h-screen items-center justify-center overflow-hidden bg-stone-50 px-6 py-12">
        <!-- Fondo sutil -->
        <div class="absolute inset-0 bg-[linear-gradient(rgba(0,0,0,0.04)_1px,transparent_1px),linear-gradient(90deg,rgba(0,0,0,0.04)_1px,transparent_1px)] bg-[size:40px_40px]"></div>

        <section class="relative w-full max-w-md rounded-2xl border border-stone-200 bg-white p-8 shadow-lg shadow-stone-200/60">
            <!-- Logo -->
            <div class="mb-6 flex items-center gap-3">
                <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-teal-600 text-[11px] font-bold tracking-wide text-white">
                    MPC
                </div>
                <div>
                    <p class="text-[13px] font-semibold text-stone-800">Mesa de Ayuda</p>
                    <p class="text-[11px] text-stone-400">Municipalidad Provincial</p>
                </div>
            </div>

            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-400">Cierre de sesión</p>
            <h1 class="mt-2 text-2xl font-semibold tracking-tight text-stone-900">¿Deseas salir del sistema?</h1>
            <p class="mt-3 text-sm leading-6 text-stone-500">
                Sesión activa como <span class="font-semibold text-stone-700">{{ page.props.auth.user?.name }}</span>
                <span class="text-stone-400"> — {{ page.props.auth.user?.email }}</span>.
            </p>

            <div class="mt-6 flex flex-col gap-3 sm:flex-row">
                <button
                    type="button"
                    :disabled="form.processing"
                    class="inline-flex flex-1 items-center justify-center rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-60"
                    @click="submit"
                >
                    Confirmar cierre de sesión
                </button>
                <Link
                    :href="route('dashboard')"
                    class="inline-flex flex-1 items-center justify-center rounded-lg border border-stone-200 bg-stone-50 px-4 py-2.5 text-sm font-semibold text-stone-700 transition hover:bg-stone-100"
                >
                    Cancelar
                </Link>
            </div>
        </section>
    </main>
</template>
