<script setup>
import { computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    tickets: { type: Array, default: () => [] },
});

const page = usePage();
const flash = computed(() => page.props.flash?.status);

const estadoLabel = {
    nuevo:                'Nuevo',
    en_clasificacion:     'En clasificación',
    pendiente_aprobacion: 'Pend. aprobación',
    en_atencion:          'En atención',
    pendiente_info:       'Pend. información',
    transferido:          'Transferido',
    resuelto:             'Resuelto',
    cerrado:              'Cerrado',
    cancelado:            'Cancelado',
    rechazado:            'Rechazado',
};

const estadoColor = {
    nuevo:                'bg-stone-100 text-stone-600',
    en_clasificacion:     'bg-blue-50 text-blue-700',
    pendiente_aprobacion: 'bg-amber-50 text-amber-700',
    en_atencion:          'bg-teal-50 text-teal-700',
    pendiente_info:       'bg-orange-50 text-orange-700',
    transferido:          'bg-purple-50 text-purple-700',
    resuelto:             'bg-green-50 text-green-700',
    cerrado:              'bg-stone-100 text-stone-500',
    cancelado:            'bg-red-50 text-red-600',
    rechazado:            'bg-red-50 text-red-700',
};

function formatDate(iso) {
    if (!iso) return '—';
    return new Date(iso).toLocaleDateString('es-PE', { day: '2-digit', month: 'short', year: 'numeric' });
}
</script>

<template>
    <AppLayout title="Mis Tickets">
        <main class="p-8">

            <!-- Encabezado -->
            <header class="mb-6 flex items-start justify-between gap-4">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-teal-600">Mis Tickets</p>
                    <h1 class="mt-1 text-2xl font-semibold tracking-tight text-stone-900">Mis solicitudes</h1>
                    <p class="mt-1 text-sm text-stone-400">Historial de tickets registrados por tu cuenta.</p>
                </div>
                <Link
                    :href="route('mis-tickets.create')"
                    class="inline-flex items-center gap-2 rounded-lg bg-teal-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-teal-700"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nuevo ticket
                </Link>
            </header>

            <!-- Flash -->
            <div v-if="flash" class="mb-5 rounded-lg border border-teal-200 bg-teal-50 px-4 py-3 text-sm text-teal-800">
                {{ flash }}
            </div>

            <!-- Sin tickets -->
            <div
                v-if="tickets.length === 0"
                class="rounded-xl border border-stone-200 bg-white p-12 text-center shadow-sm"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mx-auto mb-3 h-10 w-10 text-stone-300">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75a3 3 0 01-3 3h-3a3 3 0 01-3-3V6m10.5 0H6.75M6.75 6A2.25 2.25 0 004.5 8.25v11.25A2.25 2.25 0 006.75 21.75h10.5A2.25 2.25 0 0019.5 19.5V8.25A2.25 2.25 0 0017.25 6" />
                </svg>
                <p class="text-sm font-medium text-stone-500">No tienes tickets registrados todavía.</p>
                <p class="mt-1 text-sm text-stone-400">Usa el botón <strong>Nuevo ticket</strong> para registrar tu primera solicitud.</p>
            </div>

            <!-- Tabla de tickets -->
            <div v-else class="overflow-hidden rounded-xl border border-stone-200 bg-white shadow-sm">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-stone-100 bg-stone-50 text-left text-xs font-semibold uppercase tracking-wider text-stone-400">
                            <th class="px-5 py-3">Código</th>
                            <th class="px-5 py-3">Título</th>
                            <th class="px-5 py-3">Estado</th>
                            <th class="px-5 py-3">Tipo</th>
                            <th class="px-5 py-3">Fecha</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-stone-100">
                        <tr
                            v-for="ticket in tickets"
                            :key="ticket.id_ticket"
                            class="transition hover:bg-stone-50"
                        >
                            <td class="px-5 py-3.5 font-mono text-xs text-stone-500">{{ ticket.codigo }}</td>
                            <td class="px-5 py-3.5 font-medium text-stone-800">{{ ticket.titulo }}</td>
                            <td class="px-5 py-3.5">
                                <span
                                    class="inline-block rounded-full px-2.5 py-0.5 text-[11px] font-semibold"
                                    :class="estadoColor[ticket.estado] ?? 'bg-stone-100 text-stone-600'"
                                >
                                    {{ estadoLabel[ticket.estado] ?? ticket.estado }}
                                </span>
                            </td>
                            <td class="px-5 py-3.5 capitalize text-stone-500">
                                {{ ticket.clasificacion ?? '—' }}
                            </td>
                            <td class="px-5 py-3.5 text-stone-400">{{ formatDate(ticket.created_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </main>
    </AppLayout>
</template>
