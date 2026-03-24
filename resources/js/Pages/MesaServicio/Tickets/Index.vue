<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import TableBase from '@/Components/TableBase.vue';
import ButtonBase from '@/Components/ButtonBase.vue';
import {router} from '@inertiajs/vue3';
import {ref, computed} from 'vue';
import {route} from 'ziggy-js';

const props = defineProps({
    tickets:     {type: Array, default: () => []},
    estados:     {type: Array, default: () => []},
    prioridades: {type: Array, default: () => []},
});

// ── Modal detalle ─────────────────────────────────────────────
const modalDetalle = ref(false);
const ticketDetalle = ref(null);

const verDetalle = (ticket) => {
    ticketDetalle.value = ticket;
    modalDetalle.value = true;
};

// ── Modal historial ──────────────────────────────────────────
const modalHistorial = ref(false);
const ticketHistorial = ref(null);

const verHistorial = (ticket) => {
    ticketHistorial.value = ticket;
    modalHistorial.value = true;
};

// ── Helpers ──────────────────────────────────────────────────
const estadoMap = computed(() =>
    Object.fromEntries(props.estados.map(e => [e.nombre, e]))
);

const estadoLabel = (nombre) => estadoMap.value[nombre]?.label ?? nombre;

const estadoClase = (nombre) => {
    const e = estadoMap.value[nombre];
    return `${e?.text_color ?? 'text-gray-500'} ${e?.bg_color ?? 'bg-gray-100'}`;
};

const prioridadMap = computed(() =>
    Object.fromEntries(props.prioridades.map(p => [p.nombre, p]))
);

const prioridadLabel = (nombre) => prioridadMap.value[nombre]?.label ?? nombre;

const prioridadClase = (nombre) => {
    const p = prioridadMap.value[nombre];
    return `${p?.text_color ?? 'text-gray-500'} ${p?.bg_color ?? 'bg-gray-100'}`;
};
</script>

<template>
    <AuthLayout>
        <template #header>Mesa de Servicio — Tickets sin asignar</template>

        <div class="flex justify-start mb-4">
            <ButtonBase label="Nuevo Ticket" icon="fa-solid fa-plus"
                        @click="router.visit(route('mesadeayuda.tickets.crear.vista'))"/>
        </div>

            <TableBase>
                <template #thead>
                    <tr class="text-left text-xs font-bold text-gray-500 uppercase">
                        <th class="px-2 py-2 text-center w-10">#</th>
                        <th class="px-2 py-2 border-l border-l-gray-200">Código</th>
                        <th class="px-2 py-2 border-l border-l-gray-200">DNI</th>
                        <th class="px-2 py-2 border-l border-l-gray-200">Solicitante</th>
                        <th class="px-2 py-2 border-l border-l-gray-200">Celular</th>
                        <th class="px-2 py-2 border-l border-l-gray-200">Asunto</th>
                        <th class="px-2 py-2 border-l border-l-gray-200 text-center">Fecha</th>
                        <th class="px-2 py-2 border-l border-l-gray-200">Categoría</th>
                        <th class="px-2 py-2 border-l border-l-gray-200">Servicio</th>
                        <th class="px-2 py-2 border-l border-l-gray-200 text-center">Prioridad</th>
                        <th class="px-2 py-2 border-l border-l-gray-200 text-center">Estado</th>
                        <th class="px-2 py-2 border-l border-l-gray-200 text-center">Acciones</th>
                    </tr>
                </template>
                <template #tbody>
                    <tr v-if="!tickets.length">
                        <td colspan="13" class="text-center text-gray-400 py-10 font-light text-base">
                            No hay tickets pendientes de asignación
                        </td>
                    </tr>
                    <tr v-for="(ticket, i) in tickets" :key="ticket.id"
                        class="text-xs text-gray-600 hover:bg-blue-50 transition duration-150">
                        <td class="px-2 py-2 text-center text-gray-400">{{ i + 1 }}</td>
                        <td class="px-2 py-2 border-l border-l-gray-200 font-medium text-gray-700 whitespace-nowrap">
                            {{ ticket.codigo }}
                        </td>
                        <td class="px-2 py-2 border-l border-l-gray-200 font-mono whitespace-nowrap">
                            {{ ticket.dni ?? '—' }}
                        </td>
                        <td class="px-2 py-2 border-l border-l-gray-200">
                            {{ ticket.solicitante }}
                        </td>
                        <td class="px-2 py-2 border-l border-l-gray-200 whitespace-nowrap font-mono">
                            {{ ticket.celular ?? '—' }}
                        </td>
                        <td class="px-2 py-2 border-l border-l-gray-200 max-w-xs truncate">
                            {{ ticket.asunto }}
                        </td>
                        <td class="px-2 py-2 border-l border-l-gray-200 text-center whitespace-nowrap text-gray-500">
                            {{ ticket.fecha }}
                        </td>
                        <td class="px-2 py-2 border-l border-l-gray-200">
                            {{ ticket.categoria ?? '—' }}
                        </td>
                        <td class="px-2 py-2 border-l border-l-gray-200">
                            {{ ticket.servicio ?? '—' }}
                        </td>
                        <td class="px-2 py-2 border-l border-l-gray-200 text-center">
                            <span v-if="ticket.prioridad" class="px-2 py-1 rounded-5px text-xs font-medium" :class="prioridadClase(ticket.prioridad)">
                                {{ prioridadLabel(ticket.prioridad) }}
                            </span>
                            <span v-else class="text-gray-300">—</span>
                        </td>
                        <td class="px-2 py-2 border-l border-l-gray-200 text-center">
                            <span class="px-2 py-2 rounded-5px text-xs font-medium text-nowrap" :class="estadoClase(ticket.estado)">
                                {{ estadoLabel(ticket.estado) }}
                            </span>
                        </td>
                        <td class="px-2 py-2 border-l border-l-gray-200 text-center">
                            <div class="flex items-center justify-center gap-2">
                                <button type="button" title="Ver detalle"
                                        class="bg-blue-500 border-b-2 border-b-blue-600 text-white p-1.5 rounded-5px text-xs cursor-pointer hover:bg-blue-600 transition"
                                        @click="verDetalle(ticket)">
                                    <i class="fa-solid fa-eye"></i>
                                </button>
                                <button type="button" title="Ver historial"
                                        class="bg-blue-500 border-b-2 border-b-blue-600 text-white p-1.5 rounded-5px text-xs cursor-pointer hover:bg-blue-600 transition"
                                        @click="verHistorial(ticket)">
                                    <i class="fa-solid fa-clock-rotate-left"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </TableBase>

        <!-- ── Modal: Detalle ──────────────────────────────────────── -->
        <Teleport to="body">
            <div v-if="modalDetalle"
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
                 @click.self="modalDetalle = false">
                <div class="bg-white rounded-5px shadow-xl w-full max-w-xl mx-4 flex flex-col max-h-[85vh]">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Detalle del ticket</p>
                            <p class="text-xs text-gray-400">{{ ticketDetalle?.codigo }}</p>
                        </div>
                        <button type="button" @click="modalDetalle = false"
                                class="text-gray-400 hover:text-gray-600 transition">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                    <div class="overflow-y-auto px-5 py-4 space-y-4">
                        <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-xs">
                            <div>
                                <p class="text-gray-400 mb-0.5">Código</p>
                                <p class="font-medium text-gray-700">{{ ticketDetalle?.codigo }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 mb-0.5">Estado</p>
                                <span class="px-2 py-2 rounded-5px text-xs font-medium text-nowrap"
                                    :class="estadoClase(ticketDetalle?.estado)">
                                    {{ estadoLabel(ticketDetalle?.estado) }}
                                </span>
                            </div>
                            <div>
                                <p class="text-gray-400 mb-0.5">DNI</p>
                                <p class="text-gray-700 font-mono">{{ ticketDetalle?.dni ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 mb-0.5">Solicitante</p>
                                <p class="text-gray-700">{{ ticketDetalle?.solicitante }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 mb-0.5">Celular</p>
                                <p class="text-gray-700">{{ ticketDetalle?.celular ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 mb-0.5">Categoría</p>
                                <p class="text-gray-700">{{ ticketDetalle?.categoria ?? '—' }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 mb-0.5">Servicio</p>
                                <p class="text-gray-700">{{ ticketDetalle?.servicio ?? '—' }}</p>
                            </div>
                            <div class="col-span-2">
                                <p class="text-gray-400 mb-0.5">Fecha de creación</p>
                                <p class="text-gray-700">{{ ticketDetalle?.fecha }}</p>
                            </div>
                        </div>
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Asunto</p>
                            <p class="text-sm font-medium text-gray-700">{{ ticketDetalle?.asunto }}</p>
                        </div>
                        <div v-if="ticketDetalle?.descripcion">
                            <p class="text-xs text-gray-400 mb-1">Descripción</p>
                            <p class="text-sm text-gray-600 whitespace-pre-line leading-relaxed">
                                {{ ticketDetalle?.descripcion }}</p>
                        </div>
                        <div v-if="ticketDetalle?.archivos?.length">
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                <i class="fa-solid fa-paperclip mr-1"></i>Archivos adjuntos
                            </p>
                            <div class="space-y-2">
                                <a v-for="arch in ticketDetalle.archivos" :key="arch.id"
                                   :href="arch.ruta" target="_blank"
                                   class="flex items-center gap-3 p-2.5 border border-gray-200 rounded-5px hover:border-blue-300 hover:bg-blue-50/40 transition group">
                                    <i class="fa-solid fa-file text-gray-300 group-hover:text-blue-400 text-lg w-5 text-center"></i>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-medium text-gray-700 truncate">{{ arch.nombre }}</p>
                                        <p class="text-xs text-gray-400">{{ arch.peso }}</p>
                                    </div>
                                    <i class="fa-solid fa-download text-gray-300 group-hover:text-blue-400 text-xs"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>

        <!-- ── Modal: Historial ─────────────────────────────────────── -->
        <Teleport to="body">
            <div v-if="modalHistorial"
                 class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
                 @click.self="modalHistorial = false">
                <div class="bg-white rounded-5px shadow-xl w-full max-w-lg mx-4 flex flex-col max-h-[80vh]">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Historial del ticket</p>
                            <p class="text-xs text-gray-400">{{ ticketHistorial?.codigo }}</p>
                        </div>
                        <button type="button" @click="modalHistorial = false"
                                class="text-gray-400 hover:text-gray-600 transition">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                    <div class="overflow-y-auto px-5 py-4">
                        <div v-if="!ticketHistorial?.historial?.length"
                             class="text-center text-gray-400 py-8 text-sm">
                            Sin movimientos registrados
                        </div>
                        <div v-else class="relative">
                            <div class="absolute left-3.5 top-2 bottom-2 w-px bg-gray-200"></div>
                            <div v-for="h in ticketHistorial.historial" :key="h.id" class="relative flex gap-4 pb-5">
                                <div :class="['relative z-10 flex-shrink-0 w-7 h-7 rounded-full border-2 flex items-center justify-center text-xs',
                                    h.es_conformidad ? 'border-emerald-400 bg-emerald-50 text-emerald-500' : 'border-blue-300 bg-blue-50 text-blue-400']">
                                    <i :class="h.es_conformidad ? 'fa-solid fa-check' : 'fa-solid fa-arrow-right'"></i>
                                </div>
                                <div class="flex-1 pt-0.5">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span v-if="h.estado_anterior" class="text-xs text-gray-400">
                                            {{ estadoLabel(h.estado_anterior) }}
                                        </span>
                                        <i v-if="h.estado_anterior"
                                           class="fa-solid fa-arrow-right text-gray-300 text-xs"></i>
                                        <span class="text-xs font-semibold px-2 py-0.5 rounded-5px"
                                            :class="estadoClase(h.estado_nuevo)">
                                            {{ estadoLabel(h.estado_nuevo) }}
                                        </span>
                                    </div>
                                    <p v-if="h.comentario" class="mt-1 text-xs text-gray-600">{{ h.comentario }}</p>
                                    <p class="mt-0.5 text-xs text-gray-400">{{ h.usuario }} · {{ h.fecha }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </AuthLayout>
</template>
