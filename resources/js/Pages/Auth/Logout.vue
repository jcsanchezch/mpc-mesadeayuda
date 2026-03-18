<script setup>
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';

defineProps({
    dashboardUrl: { type: String, required: true },
    logoutUrl: { type: String, required: true },
});

const page = usePage();
const form = useForm({});

const submit = (logoutUrl) => {
    form.post(logoutUrl);
};
</script>

<template>
    <Head title="Cerrar sesion" />

    <main class="relative flex min-h-screen items-center justify-center overflow-hidden bg-[radial-gradient(circle_at_bottom_left,_rgba(251,191,36,0.18),_transparent_36%),linear-gradient(145deg,_#0c0a09,_#1c1917)] px-6 py-12 text-stone-100">
        <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.08)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.08)_1px,transparent_1px)] bg-[size:38px_38px] opacity-40"></div>

        <section class="relative w-full max-w-xl rounded-3xl border border-white/10 bg-white/8 p-8 shadow-2xl shadow-black/30 backdrop-blur">
            <p class="text-sm font-semibold uppercase tracking-[0.35em] text-amber-300">Cierre de sesion</p>
            <h1 class="mt-4 text-3xl font-semibold tracking-tight text-white">Estas a punto de salir del sistema</h1>
            <p class="mt-4 text-sm leading-7 text-stone-300">
                Tu sesion actual pertenece a <span class="font-semibold text-white">{{ page.props.auth.user?.email }}</span>.
                Si ya terminaste de trabajar, confirma el cierre para invalidar la sesion y volver al inicio.
            </p>

            <div class="mt-8 rounded-2xl border border-white/10 bg-black/20 p-5">
                <p class="text-sm text-stone-300">Usuario</p>
                <p class="mt-2 text-lg font-semibold text-white">{{ page.props.auth.user?.name }}</p>
            </div>

            <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                <button
                    type="button"
                    :disabled="form.processing"
                    class="inline-flex w-full flex-1 items-center justify-center rounded-2xl bg-amber-400 px-5 py-3 text-sm font-semibold text-stone-950 transition hover:bg-amber-300 disabled:cursor-not-allowed disabled:opacity-70"
                    @click="submit(logoutUrl)"
                >
                    Confirmar cierre de sesion
                </button>

                <Link
                    :href="dashboardUrl"
                    class="inline-flex flex-1 items-center justify-center rounded-2xl border border-white/15 bg-white/8 px-5 py-3 text-sm font-semibold text-white transition hover:bg-white/14"
                >
                    Volver al dashboard
                </Link>
            </div>
        </section>
    </main>
</template>
