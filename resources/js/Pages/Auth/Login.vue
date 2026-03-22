<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';

const page = usePage();

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login.store'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Iniciar sesion" />

    <main class="relative flex min-h-screen items-center justify-center overflow-hidden bg-[radial-gradient(circle_at_top,_rgba(15,118,110,0.18),_transparent_42%),linear-gradient(135deg,_#f5f5f4,_#fafaf9)] px-6 py-12">
        <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.72)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.72)_1px,transparent_1px)] bg-[size:42px_42px] opacity-50"></div>

        <section class="relative w-full max-w-md rounded-3xl border border-white/70 bg-white/85 p-8 shadow-2xl shadow-stone-300/40 backdrop-blur">
            <div class="mb-8">
                <p class="mb-3 text-sm font-semibold uppercase tracking-[0.35em] text-teal-700">Mesa de ayuda</p>
                <h1 class="text-3xl font-semibold tracking-tight text-stone-950">Iniciar sesion</h1>
                <p class="mt-3 text-sm leading-6 text-stone-600">
                    Accede con tu usuario autorizado. El registro y la recuperacion de contrasena estan deshabilitados.
                </p>
            </div>

            <div
                v-if="page.props.flash.status"
                class="mb-6 rounded-2xl border border-teal-200 bg-teal-50 px-4 py-3 text-sm text-teal-800"
            >
                {{ page.props.flash.status }}
            </div>

            <form class="space-y-5" @submit.prevent="submit">
                <div>
                    <label for="email" class="mb-2 block text-sm font-medium text-stone-700">Correo electronico</label>
                    <input
                        id="email"
                        v-model="form.email"
                        type="email"
                        required
                        autofocus
                        autocomplete="username"
                        class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-stone-900 outline-none transition focus:border-teal-600 focus:ring-4 focus:ring-teal-100"
                    >
                    <p v-if="form.errors.email" class="mt-2 text-sm text-red-600">{{ form.errors.email }}</p>
                </div>

                <div>
                    <label for="password" class="mb-2 block text-sm font-medium text-stone-700">Contrasena</label>
                    <input
                        id="password"
                        v-model="form.password"
                        type="password"
                        required
                        autocomplete="current-password"
                        class="w-full rounded-2xl border border-stone-300 bg-white px-4 py-3 text-sm text-stone-900 outline-none transition focus:border-teal-600 focus:ring-4 focus:ring-teal-100"
                    >
                </div>

                <label class="flex items-center gap-3 text-sm text-stone-600">
                    <input
                        v-model="form.remember"
                        type="checkbox"
                        class="h-4 w-4 rounded border-stone-300 text-teal-700 focus:ring-teal-200"
                    >
                    Mantener sesion iniciada
                </label>

                <button
                    type="submit"
                    :disabled="form.processing"
                    class="inline-flex w-full items-center justify-center rounded-2xl bg-stone-950 px-4 py-3 text-sm font-semibold text-white transition hover:bg-teal-700 focus:outline-none focus:ring-4 focus:ring-teal-200 disabled:cursor-not-allowed disabled:opacity-70"
                >
                    Entrar al sistema
                </button>
            </form>
        </section>
    </main>
</template>
