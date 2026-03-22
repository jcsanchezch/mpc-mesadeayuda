<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const page      = usePage();
const secciones = computed(() => page.props.auth.secciones ?? []);

const seccionConfig = {
    mis_tickets:   { titulo: 'Mis Tickets',      descripcion: 'Revisa y da seguimiento a los tickets que has generado.',             ruta: '/mis-tickets',   color: 'teal'  },
    mesa_servicio: { titulo: 'Mesa de Servicio',  descripcion: 'Gestiona solicitudes entrantes, clasifica y asigna tickets.',         ruta: '/mesa-servicio', color: 'blue'  },
    reportes:      { titulo: 'Reportes',           descripcion: 'Genera y exporta reportes de atención y análisis de capacitaciones.', ruta: '/reportes',      color: 'amber' },
    admin:         { titulo: 'Administración',     descripcion: 'Gestiona usuarios, trabajadores y configuración del sistema.',        ruta: '/admin',         color: 'rose'  },
};

const colorCard = {
    teal:  { badge: 'bg-teal-50 text-teal-700 border-teal-200',   btn: 'bg-teal-600 text-white hover:bg-teal-700',   icon: 'text-teal-500'  },
    blue:  { badge: 'bg-blue-50 text-blue-700 border-blue-200',   btn: 'bg-blue-600 text-white hover:bg-blue-700',   icon: 'text-blue-500'  },
    amber: { badge: 'bg-amber-50 text-amber-700 border-amber-200', btn: 'bg-amber-500 text-white hover:bg-amber-600', icon: 'text-amber-500' },
    rose:  { badge: 'bg-rose-50 text-rose-700 border-rose-200',   btn: 'bg-rose-600 text-white hover:bg-rose-700',   icon: 'text-rose-500'  },
};
</script>

<template>
    <AppLayout title="Inicio">
        <main class="p-8">

            <!-- Encabezado -->
            <header class="mb-8">
                <p class="text-xs font-semibold uppercase tracking-[0.3em] text-stone-400">Panel de control</p>
                <h1 class="mt-1 text-2xl font-semibold tracking-tight text-stone-900">
                    Bienvenido, {{ page.props.auth.user?.name }}
                </h1>
                <p class="mt-1 text-sm text-stone-400">Selecciona una sección para comenzar.</p>
            </header>

            <!-- Sin secciones -->
            <div
                v-if="secciones.length === 0"
                class="rounded-xl border border-stone-200 bg-white p-10 text-center text-sm text-stone-400 shadow-sm"
            >
                Tu cuenta no tiene secciones asignadas. Contacta al administrador del sistema.
            </div>

            <!-- Tarjetas -->
            <section v-else class="grid gap-5 sm:grid-cols-2">
                <article
                    v-for="key in secciones"
                    :key="key"
                    class="flex flex-col justify-between gap-5 rounded-xl border border-stone-200 bg-white p-6 shadow-sm transition hover:shadow-md"
                >
                    <div>
                        <span
                            class="inline-block rounded-full border px-3 py-1 text-[11px] font-semibold uppercase tracking-widest"
                            :class="colorCard[seccionConfig[key].color].badge"
                        >
                            {{ seccionConfig[key].titulo }}
                        </span>
                        <p class="mt-3 text-sm leading-6 text-stone-500">
                            {{ seccionConfig[key].descripcion }}
                        </p>
                    </div>
                    <Link
                        :href="seccionConfig[key].ruta"
                        class="inline-flex w-full items-center justify-center rounded-lg px-4 py-2.5 text-sm font-semibold transition"
                        :class="colorCard[seccionConfig[key].color].btn"
                    >
                        Ir a {{ seccionConfig[key].titulo }}
                    </Link>
                </article>
            </section>

        </main>
    </AppLayout>
</template>
