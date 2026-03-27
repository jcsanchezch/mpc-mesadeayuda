<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import TableBase from '@/Components/TableBase.vue';
import UiButton from '@/Components/Buttons/UiButton.vue';
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
    activos:  { type: Array,  default: () => [] },
    cerrados: { type: Array,  default: () => [] },
});

// ── Modal detalle (incluye historial) ────────────────────────
const modalDetalle  = ref(false);
const ticketDetalle = ref(null);

const verDetalle = (ticket) => {
    ticketDetalle.value = ticket;
    modalDetalle.value  = true;
};

// ── Modal conformidad ────────────────────────────────────────
const modalConformidad  = ref(false);
const ticketConformidad = ref(null);

const abrirConformidad = (ticket) => {
    ticketConformidad.value = ticket;
    modalConformidad.value  = true;
};

const formConformidad = useForm({ comentario: '' });

const confirmarConformidad = () => {
    formConformidad.post(
        route('solicitante.tickets.conformidad', ticketConformidad.value.id),
        { onSuccess: () => { modalConformidad.value = false; formConformidad.reset(); } }
    );
};

// ── Helpers ──────────────────────────────────────────────────
const estadoClase = (estado) => ({
    'REGISTRADO':   'bg-yellow-100 text-yellow-700',
    'ASIGNADO':    'bg-purple-100 text-purple-700',
    'PROGRAMADO':  'bg-indigo-100 text-indigo-700',
    'ATENDIENDO':  'bg-blue-100 text-blue-700',
    'INFORMACION': 'bg-orange-100 text-orange-700',
    'ATENDIDO':    'bg-emerald-100 text-emerald-700',
    'CANCELADO':   'bg-red-100 text-red-700',
    'CERRADO':     'bg-gray-100 text-gray-500',
}[estado] ?? 'bg-gray-100 text-gray-500');

const estadoLabel = (estado) => ({
    'REGISTRADO':   'En Espera',
    'ASIGNADO':    'Asignado',
    'PROGRAMADO':  'Programado',
    'ATENDIENDO':  'Atendiendo',
    'INFORMACION': 'Información',
    'ATENDIDO':    'Atendido',
    'CANCELADO':   'Cancelado',
    'CERRADO':     'Cerrado',
}[estado] ?? estado);
</script>

<template>
    <AuthLayout>
        <template #header>Mis Tickets</template>

        <!-- ── Lista de tickets ─────────────────────────────────── -->
        <div class="flex justify-start mb-4">
            <UiButton
                label="Nuevo Ticket"
                icon="fa-solid fa-plus"
                size="sm"
                @click="router.visit(route('solicitante.tickets.crear'))"
            />
        </div>

            <TableBase>
                <template #thead>
                    <tr class="text-left text-xs font-bold text-gray-500 uppercase">
                        <th class="px-3 py-3 text-center w-10">#</th>
                        <th class="px-3 py-3 border-l border-l-gray-200">Código</th>
                        <th class="px-3 py-3 border-l border-l-gray-200">Servicio</th>
                        <th class="px-3 py-3 border-l border-l-gray-200">Asunto</th>
                        <th class="px-3 py-3 border-l border-l-gray-200 text-center">Estado</th>
                        <th class="px-3 py-3 border-l border-l-gray-200 text-center">Fecha</th>
                        <th class="px-3 py-3 border-l border-l-gray-200 text-center">Acciones</th>
                    </tr>
                </template>
                <template #tbody>
                    <tr v-if="!activos.length && !cerrados.length">
                        <td colspan="7" class="text-center text-gray-400 py-10 font-light text-base">
                            No tienes tickets registrados
                        </td>
                    </tr>

                    <!-- Tickets activos -->
                    <template v-if="activos.length">
                        <tr v-for="(ticket, i) in activos" :key="ticket.id"
                            class="text-xs text-gray-600 hover:bg-blue-50 transition duration-150">
                            <td class="px-3 py-3 text-center text-gray-400">{{ i + 1 }}</td>
                            <td class="px-3 py-3 border-l border-l-gray-200 font-medium text-gray-700 whitespace-nowrap">
                                {{ ticket.codigo }}
                            </td>
                            <td class="px-3 py-3 border-l border-l-gray-200">
                                <div class="text-gray-400 text-xs">{{ ticket.categoria }}</div>
                                <div>{{ ticket.servicio ?? '—' }}</div>
                            </td>
                            <td class="px-3 py-3 border-l border-l-gray-200 max-w-xs truncate">{{ ticket.asunto }}</td>
                            <td class="px-3 py-3 border-l border-l-gray-200 text-center">
                                <span :class="['px-2 py-1 rounded-[4px] text-xs font-medium', estadoClase(ticket.estado)]">
                                    {{ estadoLabel(ticket.estado) }}
                                </span>
                            </td>
                            <td class="px-3 py-3 border-l border-l-gray-200 text-center whitespace-nowrap text-gray-500">
                                {{ ticket.fecha }}
                            </td>
                            <td class="px-3 py-3 border-l border-l-gray-200 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button"
                                        class="bg-blue-500 border-b-2 border-b-blue-600 text-white py-1.5 px-3 rounded-[4px] text-xs cursor-pointer hover:bg-blue-600 transition flex items-center gap-1.5"
                                        @click="verDetalle(ticket)">
                                        <i class="fa-solid fa-eye"></i> Detalle
                                    </button>
                                    <button v-if="ticket.estado === 'ATENDIDO'" type="button"
                                        title="Dar conformidad"
                                        class="bg-emerald-500 border-b-2 border-b-emerald-600 text-white py-1.5 px-3 rounded-[4px] text-xs cursor-pointer hover:bg-emerald-600 transition flex items-center gap-1.5"
                                        @click="abrirConformidad(ticket)">
                                        <i class="fa-solid fa-circle-check"></i> Conformidad
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>

                    <!-- Separador cerrados -->
                    <tr v-if="activos.length && cerrados.length">
                        <td colspan="7" class="px-3 py-2 bg-gray-50 border-t border-b border-gray-200">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                Tickets Cerrados
                            </span>
                        </td>
                    </tr>

                    <!-- Tickets cerrados -->
                    <template v-if="cerrados.length">
                        <tr v-for="(ticket, i) in cerrados" :key="ticket.id"
                            class="text-xs text-gray-400 hover:bg-gray-50 transition duration-150">
                            <td class="px-3 py-3 text-center text-gray-300">{{ activos.length + i + 1 }}</td>
                            <td class="px-3 py-3 border-l border-l-gray-200 font-medium whitespace-nowrap">
                                {{ ticket.codigo }}
                            </td>
                            <td class="px-3 py-3 border-l border-l-gray-200">
                                <div class="text-xs">{{ ticket.categoria }}</div>
                                <div>{{ ticket.servicio ?? '—' }}</div>
                            </td>
                            <td class="px-3 py-3 border-l border-l-gray-200 max-w-xs truncate">{{ ticket.asunto }}</td>
                            <td class="px-3 py-3 border-l border-l-gray-200 text-center">
                                <span :class="['px-2 py-1 rounded-[4px] text-xs font-medium', estadoClase(ticket.estado)]">
                                    {{ estadoLabel(ticket.estado) }}
                                </span>
                            </td>
                            <td class="px-3 py-3 border-l border-l-gray-200 text-center whitespace-nowrap">
                                {{ ticket.fecha }}
                            </td>
                            <td class="px-3 py-3 border-l border-l-gray-200 text-center">
                                <button type="button"
                                    class="bg-blue-300 border-b-2 border-b-blue-400 text-white py-1.5 px-3 rounded-[4px] text-xs cursor-pointer hover:bg-blue-400 transition flex items-center gap-1.5 mx-auto"
                                    @click="verDetalle(ticket)">
                                    <i class="fa-solid fa-eye"></i> Detalle
                                </button>
                            </td>
                        </tr>
                    </template>
                </template>
            </TableBase>

        <!-- ── Modal: Detalle + Historial ─────────────────────────── -->
        <Teleport to="body">
            <div v-if="modalDetalle"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
                @click.self="modalDetalle = false">
                <div class="bg-white rounded-[4px] shadow-xl w-full max-w-3xl flex flex-col max-h-[90vh]">

                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200 flex-shrink-0">
                        <div class="flex items-center gap-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-700">Detalle del ticket</p>
                                <p class="text-xs text-gray-400">{{ ticketDetalle?.codigo }}</p>
                            </div>
                            <span :class="['px-2 py-1 rounded-[4px] text-xs font-medium', estadoClase(ticketDetalle?.estado)]">
                                {{ estadoLabel(ticketDetalle?.estado) }}
                            </span>
                        </div>
                        <button type="button" @click="modalDetalle = false"
                            class="text-gray-400 hover:text-gray-600 transition">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>

                    <!-- Body scrollable -->
                    <div class="overflow-y-auto px-6 py-5 space-y-6">

                        <!-- Datos del ticket -->
                        <div class="space-y-4">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">Datos del Ticket</p>

                            <div class="grid grid-cols-3 gap-x-6 gap-y-3 text-xs">
                                <div>
                                    <p class="text-gray-400 mb-0.5">Código</p>
                                    <p class="font-medium text-gray-700">{{ ticketDetalle?.codigo }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 mb-0.5">Fecha de creación</p>
                                    <p class="text-gray-700">{{ ticketDetalle?.fecha }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 mb-0.5">Estado</p>
                                    <span :class="['px-2 py-1 rounded-[4px] text-xs font-medium', estadoClase(ticketDetalle?.estado)]">
                                        {{ estadoLabel(ticketDetalle?.estado) }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-gray-400 mb-0.5">Categoría</p>
                                    <p class="text-gray-700">{{ ticketDetalle?.categoria ?? '—' }}</p>
                                </div>
                                <div class="col-span-2">
                                    <p class="text-gray-400 mb-0.5">Servicio</p>
                                    <p class="text-gray-700">{{ ticketDetalle?.servicio ?? '—' }}</p>
                                </div>
                            </div>

                            <div>
                                <p class="text-xs text-gray-400 mb-1">Asunto</p>
                                <p class="text-sm font-medium text-gray-700">{{ ticketDetalle?.asunto }}</p>
                            </div>

                            <div v-if="ticketDetalle?.descripcion">
                                <p class="text-xs text-gray-400 mb-1">Descripción</p>
                                <p class="text-sm text-gray-600 whitespace-pre-line leading-relaxed bg-gray-50 border border-gray-100 rounded-[4px] px-3 py-2.5">{{ ticketDetalle?.descripcion }}</p>
                            </div>

                            <div v-if="ticketDetalle?.resolucion">
                                <p class="text-xs text-gray-400 mb-1">Resolución</p>
                                <p class="text-sm text-gray-600 whitespace-pre-line leading-relaxed bg-emerald-50 border border-emerald-100 rounded-[4px] px-3 py-2.5">{{ ticketDetalle?.resolucion }}</p>
                            </div>

                            <!-- Archivos -->
                            <div>
                                <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">
                                    <i class="fa-solid fa-paperclip mr-1"></i>Archivos adjuntos
                                </p>
                                <p v-if="!ticketDetalle?.archivos?.length" class="text-xs text-gray-400 italic">Sin archivos adjuntos</p>
                                <div v-else class="space-y-1.5">
                                    <a v-for="arch in ticketDetalle.archivos" :key="arch.id"
                                        :href="arch.ruta" target="_blank"
                                        class="flex items-center gap-3 p-2.5 border border-gray-200 rounded-[4px] hover:border-blue-300 hover:bg-blue-50/40 transition group">
                                        <i class="fa-solid fa-file text-gray-300 group-hover:text-blue-400 text-lg w-5 text-center"></i>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-gray-700 truncate">{{ arch.nombre }}</p>
                                            <p class="text-xs text-gray-400">
                                                {{ arch.peso }}
                                                <span v-if="arch.tipo !== 'adjunto'" class="ml-2 px-1.5 py-0.5 bg-gray-100 rounded text-gray-500">{{ arch.tipo }}</span>
                                                <span v-if="arch.firmado" class="ml-2 px-1.5 py-0.5 bg-emerald-100 text-emerald-600 rounded">firmado</span>
                                            </p>
                                        </div>
                                        <i class="fa-solid fa-download text-gray-300 group-hover:text-blue-400 text-xs"></i>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-200"></div>

                        <!-- Historial -->
                        <div class="space-y-3">
                            <p class="text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                <i class="fa-solid fa-clock-rotate-left mr-1"></i>Historial
                            </p>
                            <div v-if="!ticketDetalle?.historial?.length"
                                class="text-center text-gray-400 py-6 text-sm">
                                Sin movimientos registrados
                            </div>
                            <div v-else class="relative">
                                <div class="absolute left-3.5 top-2 bottom-2 w-px bg-gray-200"></div>
                                <div v-for="h in ticketDetalle.historial" :key="h.id" class="relative flex gap-4 pb-4">
                                    <div :class="['relative z-10 flex-shrink-0 w-7 h-7 rounded-full border-2 flex items-center justify-center text-xs',
                                        h.es_conformidad ? 'border-emerald-400 bg-emerald-50 text-emerald-500' : 'border-blue-300 bg-blue-50 text-blue-400']">
                                        <i :class="h.es_conformidad ? 'fa-solid fa-check' : 'fa-solid fa-arrow-right'"></i>
                                    </div>
                                    <div class="flex-1 pt-0.5">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <span v-if="h.estado_anterior" class="text-xs text-gray-400">
                                                {{ estadoLabel(h.estado_anterior) }}
                                            </span>
                                            <i v-if="h.estado_anterior" class="fa-solid fa-arrow-right text-gray-300 text-xs"></i>
                                            <span :class="['text-xs font-semibold px-2 py-0.5 rounded-[4px]', estadoClase(h.estado_nuevo)]">
                                                {{ estadoLabel(h.estado_nuevo) }}
                                            </span>
                                            <span v-if="h.es_conformidad"
                                                class="text-xs bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded-[4px] font-medium">
                                                Conformidad
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
            </div>
        </Teleport>

        <!-- ── Modal: Conformidad ──────────────────────────────────── -->
        <Teleport to="body">
            <div v-if="modalConformidad"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
                @click.self="modalConformidad = false">
                <div class="bg-white rounded-[4px] shadow-xl w-full max-w-md mx-4">
                    <div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Dar conformidad</p>
                            <p class="text-xs text-gray-400">{{ ticketConformidad?.codigo }}</p>
                        </div>
                        <button type="button" @click="modalConformidad = false"
                            class="text-gray-400 hover:text-gray-600 transition">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                    <div class="px-5 py-4 space-y-4">
                        <p class="text-sm text-gray-600">
                            Al dar conformidad confirmas que el ticket fue atendido satisfactoriamente
                            y quedará registrado como cerrado.
                        </p>
                        <div>
                            <label class="block text-xs font-normal text-gray-600 mb-1">Comentario (opcional)</label>
                            <textarea v-model="formConformidad.comentario" rows="3"
                                placeholder="Puede agregar un comentario adicional..."
                                class="w-full border border-gray-300 focus:border-emerald-500 rounded-[4px] py-2.5 px-2.5 text-sm focus:outline-emerald-500 resize-none">
                            </textarea>
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 px-5 py-4 border-t border-gray-100">
                        <button type="button"
                            class="text-sm text-gray-500 hover:text-gray-700 transition"
                            @click="modalConformidad = false">
                            Cancelar
                        </button>
                        <button type="button"
                            class="bg-emerald-500 border-b-2 border-b-emerald-600 text-white px-4 py-2 rounded-[4px] text-sm cursor-pointer hover:bg-emerald-600 transition disabled:opacity-60"
                            :disabled="formConformidad.processing"
                            @click="confirmarConformidad">
                            <i class="fa-solid fa-circle-check mr-1.5"></i>Confirmar conformidad
                        </button>
                    </div>
                </div>
            </div>
        </Teleport>

    </AuthLayout>
</template>
