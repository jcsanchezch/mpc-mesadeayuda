<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import TableBase from '@/Components/TableBase.vue';
import ButtonBase from '@/Components/ButtonBase.vue';
import {router} from '@inertiajs/vue3';
import {computed} from 'vue';
import {route} from 'ziggy-js';
import {upperCase} from "lodash-es";

const props = defineProps({
    tickets:     {type: Array, default: () => []},
    estados:     {type: Array, default: () => []},
    prioridades: {type: Array, default: () => []},
});

// ── Helpers ──────────────────────────────────────────────────
const estadoMap = computed(() =>
    Object.fromEntries(props.estados.map(e => [e.codigo, e]))
);

const estadoLabel = (nombre) => estadoMap.value[nombre]?.label ?? nombre;

const estadoClase = (nombre) => {
    const e = estadoMap.value[nombre];
    return `${e?.text_color ?? 'text-gray-500'} ${e?.bg_color ?? 'bg-gray-100'}`;
};

const prioridadMap = computed(() =>
    Object.fromEntries(props.prioridades.map(p => [p.codigo, p]))
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
                        <th class="px-1.5 py-2 text-center w-10">#</th>
                        <th class="px-1.5 py-2 border-l border-l-gray-200">Código</th>
                        <th class="px-1.5 py-2 border-l border-l-gray-200">DNI</th>
                        <th class="px-1.5 py-2 border-l border-l-gray-200">Solicitante</th>
                        <th class="px-1.5 py-2 border-l border-l-gray-200">Celular</th>
                        <th class="px-1.5 py-2 border-l border-l-gray-200">Asunto</th>
                        <th class="px-1.5 py-2 border-l border-l-gray-200 text-center">Fecha</th>
                        <th class="px-1.5 py-2 border-l border-l-gray-200 text-center">Estado</th>
                        <th class="px-1.5 py-2 border-l border-l-gray-200">Servicio</th>
                        <th class="px-1.5 py-2 border-l border-l-gray-200 text-center">Prioridad</th>
                        <th class="px-1.5 py-2 border-l border-l-gray-200 text-center">Acciones</th>
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
                        <td class="px-3 py-2 text-center text-gray-400">{{ i + 1 }}</td>
                        <td class="px-1.5 py-2 border-l border-l-gray-200 font-medium text-gray-700 whitespace-nowrap">
                            {{ ticket.codigo }}
                        </td>
                        <td class="px-1.5 py-2 border-l border-l-gray-200 whitespace-nowrap">
                            {{ ticket.dni ?? '—' }}
                        </td>
                        <td class="px-1.5 py-2 border-l border-l-gray-200  whitespace-nowrap">
                            {{ ticket.solicitante }}
                        </td>
                        <td class="px-1.5 py-2 border-l border-l-gray-200 whitespace-nowrap">
                            {{ ticket.celular ?? '—' }}
                        </td>
                        <td class="px-1.5 py-2 border-l border-l-gray-200 w-1/2 truncate">
                            {{ ticket.asunto }}
                        </td>
                        <td class="px-1.5 py-2 border-l border-l-gray-200 text-center whitespace-nowrap text-gray-500">
                            {{ ticket.fecha }}
                        </td>
                        <td class="px-1.5 py-2 border-l border-l-gray-200 text-center">
                            <span class="px-2 py-1 rounded-5px text-xs font-medium text-nowrap" :class="estadoClase(ticket.estado)">
                                {{ upperCase(estadoLabel(ticket.estado)) }}
                            </span>
                        </td>
                        <td class="px-1.5 py-2 border-l border-l-gray-200  w-1/2">
                            {{ ticket.servicio ?? '—' }}
                        </td>
                        <td class="px-1.5 py-2 border-l border-l-gray-200 text-center">
                            <span v-if="ticket.prioridad" class="px-2 py-1 rounded-5px text-xs font-medium" :class="prioridadClase(ticket.prioridad)">
                                {{ prioridadLabel(ticket.prioridad) }}
                            </span>
                            <span v-else class="text-gray-300">—</span>
                        </td>
                        <td class="px-1.5 py-2 border-l border-l-gray-200 text-center">
                            <div class="inline-flex items-center gap-1.5">
                                <button type="button" title="Ver ticket"
                                        class="inline-flex items-center gap-1.5 bg-gray-100 border-b-2 border-b-gray-200 text-gray-600 px-2.5 py-1.5 rounded-5px text-xs cursor-pointer hover:bg-gray-200 transition"
                                        @click="router.visit(route('mesadeayuda.tickets.ver', ticket.id))">
                                    <i class="fa-solid fa-eye"></i> Ver
                                </button>
                                <button v-if="ticket.estado === 'EN_ESPERA'" type="button" title="Clasificar ticket"
                                        class="inline-flex items-center gap-1.5 bg-blue-500 border-b-2 border-b-blue-600 text-white px-2.5 py-1.5 rounded-5px text-xs cursor-pointer hover:bg-blue-600 transition"
                                        @click="router.visit(route('mesadeayuda.tickets.clasificar.vista', ticket.id))">
                                    <i class="fa-solid fa-tags"></i> Clasificar
                                </button>
                            </div>
                        </td>
                    </tr>
                </template>
            </TableBase>

    </AuthLayout>
</template>
