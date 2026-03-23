<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import TableBase from '@/Components/TableBase.vue';
import ButtonBase from '@/Components/ButtonBase.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
    activos:    { type: Array, default: () => [] },
    cerrados:   { type: Array, default: () => [] },
    categorias: { type: Array, default: () => [] },
});

// ── Crear Ticket ─────────────────────────────────────────────
const mostrarFormulario    = ref(false);
const modo                 = ref(null);
const servicioSeleccionado = ref(null);

const form = useForm({ modo: '', servicio_id: '', asunto: '', descripcion: '' });

const seleccionarModo = (m) => {
    modo.value = m;
    form.modo = m;
    form.servicio_id = '';
    form.asunto = '';
    form.descripcion = '';
    servicioSeleccionado.value = null;
};

const onServicioChange = () => {
    const id = parseInt(form.servicio_id);
    let encontrado = null;
    for (const cat of props.categorias) {
        encontrado = cat.servicios.find(s => s.id === id) ?? null;
        if (encontrado) break;
    }
    servicioSeleccionado.value = encontrado;
    form.asunto = encontrado ? encontrado.nombre : '';
};

const cancelarFormulario = () => {
    mostrarFormulario.value = false;
    modo.value = null;
    form.reset();
    servicioSeleccionado.value = null;
};

const submit = () => {
    form.post(route('solicitante.crear'), {
        onSuccess: () => cancelarFormulario(),
    });
};

// ── Modal detalle ─────────────────────────────────────────────
const modalDetalle = ref(false);
const ticketDetalle = ref(null);

const verDetalle = (ticket) => {
    ticketDetalle.value = ticket;
    modalDetalle.value = true;
};

// ── Modal historial ──────────────────────────────────────────
const modalHistorial = ref(false);
const ticketActivo   = ref(null);

const verHistorial = (ticket) => {
    ticketActivo.value = ticket;
    modalHistorial.value = true;
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
    'EN_ESPERA':   'bg-yellow-100 text-yellow-700',
    'ASIGNADO':    'bg-purple-100 text-purple-700',
    'PROGRAMADO':  'bg-indigo-100 text-indigo-700',
    'ATENDIENDO':  'bg-blue-100 text-blue-700',
    'INFORMACION': 'bg-orange-100 text-orange-700',
    'ATENDIDO':    'bg-emerald-100 text-emerald-700',
    'CANCELADO':   'bg-red-100 text-red-700',
    'CERRADO':     'bg-gray-100 text-gray-500',
}[estado] ?? 'bg-gray-100 text-gray-500');

const estadoLabel = (estado) => ({
    'EN_ESPERA':   'En Espera',
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

        <!-- ── Vista: Formulario ────────────────────────────────────── -->
        <div v-if="mostrarFormulario" class="max-w-2xl space-y-5">

            <div class="flex items-center gap-3">
                <button type="button" @click="cancelarFormulario"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <h2 class="text-sm font-semibold text-gray-700">Nuevo Ticket</h2>
            </div>

            <!-- Switch de modo -->
            <div class="grid grid-cols-2 gap-3">
                <button type="button" @click="seleccionarModo('1')"
                    :class="['text-left p-4 border-2 rounded-5px transition',
                        modo === '1' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-white hover:border-blue-300 hover:bg-blue-50/40']">
                    <div class="flex items-start gap-3">
                        <div :class="['mt-0.5 w-4 h-4 rounded-full border-2 flex-shrink-0 flex items-center justify-center',
                            modo === '1' ? 'border-blue-500' : 'border-gray-300']">
                            <div v-if="modo === '1'" class="w-2 h-2 rounded-full bg-blue-500"></div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">No conozco el servicio</p>
                            <p class="text-xs text-gray-400 mt-0.5">Describa su solicitud y nosotros la clasificaremos</p>
                        </div>
                    </div>
                </button>
                <button type="button" @click="seleccionarModo('2')"
                    :class="['text-left p-4 border-2 rounded-5px transition',
                        modo === '2' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-white hover:border-blue-300 hover:bg-blue-50/40']">
                    <div class="flex items-start gap-3">
                        <div :class="['mt-0.5 w-4 h-4 rounded-full border-2 flex-shrink-0 flex items-center justify-center',
                            modo === '2' ? 'border-blue-500' : 'border-gray-300']">
                            <div v-if="modo === '2'" class="w-2 h-2 rounded-full bg-blue-500"></div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Conozco el servicio</p>
                            <p class="text-xs text-gray-400 mt-0.5">Seleccione el servicio específico que necesita</p>
                        </div>
                    </div>
                </button>
            </div>

            <!-- Formulario -->
            <form v-if="modo" @submit.prevent="submit"
                class="bg-white border border-gray-200 rounded-5px p-6 space-y-5">

                <!-- MODO 2: selector de servicio -->
                <template v-if="modo === '2'">
                    <div>
                        <InputLabel value="Servicio *" />
                        <select v-model="form.servicio_id" @change="onServicioChange"
                            class="mt-1 w-full border border-gray-300 focus:border-blue-500 rounded-5px py-2.5 px-2.5 bg-white text-sm focus:outline-blue-500"
                            :class="{ 'border-red-400': form.errors.servicio_id }">
                            <option value="" disabled>-- Seleccione un servicio --</option>
                            <optgroup v-for="cat in categorias" :key="cat.id" :label="cat.nombre">
                                <option v-for="srv in cat.servicios" :key="srv.id" :value="srv.id">
                                    {{ srv.nombre }}
                                </option>
                            </optgroup>
                        </select>
                        <p v-if="form.errors.servicio_id" class="mt-1 text-xs text-red-500">{{ form.errors.servicio_id }}</p>
                    </div>

                    <div v-if="servicioSeleccionado">
                        <InputLabel value="Asunto" />
                        <input :value="form.asunto" readonly
                            class="mt-1 w-full border border-gray-200 bg-gray-50 text-gray-600 rounded-5px py-2.5 px-2.5 text-sm cursor-default" />
                        <p v-if="servicioSeleccionado.descripcion" class="mt-2 text-xs text-gray-500 leading-relaxed">
                            {{ servicioSeleccionado.descripcion }}
                        </p>
                        <div v-if="servicioSeleccionado.formatos?.length" class="mt-3">
                            <p class="text-xs font-semibold text-gray-600 mb-1.5">
                                <i class="fa-solidfa-file-lines mr-1"></i>Formatos requeridos
                            </p>
                            <div class="space-y-1">
                                <a v-for="fmt in servicioSeleccionado.formatos" :key="fmt.id"
                                    :href="fmt.archivo?.ruta" target="_blank"
                                    class="flex items-center gap-2 text-xs text-blue-600 hover:text-blue-800 hover:underline">
                                    <i class="fa-solid fa-download text-blue-400 w-3"></i>
                                    <span>{{ fmt.nombre }}</span>
                                    <span v-if="fmt.descripcion" class="text-gray-400">— {{ fmt.descripcion }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- MODO 1: asunto libre -->
                <template v-if="modo === '1'">
                    <div>
                        <InputLabel value="Asunto *" />
                        <TextInput v-model="form.asunto" class="mt-1 w-full"
                            :class="{ 'border-red-400': form.errors.asunto }"
                            placeholder="Describa brevemente el motivo del ticket" maxlength="500" />
                        <p v-if="form.errors.asunto" class="mt-1 text-xs text-red-500">{{ form.errors.asunto }}</p>
                    </div>
                </template>

                <!-- Descripción -->
                <div>
                    <InputLabel value="Descripción" />
                    <textarea v-model="form.descripcion" rows="5"
                        placeholder="Detalle el problema o solicitud (opcional)"
                        class="mt-1 w-full border border-gray-300 focus:border-blue-500 rounded-5px py-2.5 px-2.5 text-sm focus:outline-blue-500 resize-none"
                        :class="{ 'border-red-400': form.errors.descripcion }"></textarea>
                    <p v-if="form.errors.descripcion" class="mt-1 text-xs text-red-500">{{ form.errors.descripcion }}</p>
                </div>

                <!-- Adjuntos -->
                <div>
                    <InputLabel value="Archivos adjuntos" />
                    <label class="mt-1 flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-300 rounded-5px py-8 px-4 cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition">
                        <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-400 mb-2"></i>
                        <span class="text-sm text-gray-500">Arrastra archivos aquí o <span class="text-blue-500 underline">selecciona</span></span>
                        <span class="text-xs text-gray-400 mt-1">PDF, Word, Excel, imágenes — máx. 10 MB por archivo</span>
                        <input type="file" multiple class="hidden" />
                    </label>
                </div>

                <!-- Acciones -->
                <div class="flex items-center gap-3 pt-1">
                    <ButtonBase type="submit" label="Enviar Ticket" icon="fa-solid fa-paper-plane"
                        :disabled="form.processing" />
                    <button type="button" class="text-sm text-gray-500 hover:text-gray-700 transition"
                        @click="cancelarFormulario">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>

        <!-- ── Vista: Lista de tickets ──────────────────────────────── -->
        <template v-else>
            <div class="flex justify-start mb-4">
                <ButtonBase label="Nuevo Ticket" icon="fa-solid fa-plus"
                    @click="mostrarFormulario = true" />
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
                                <span :class="['px-2 py-1 rounded-5px text-xs font-medium', estadoClase(ticket.estado)]">
                                    {{ estadoLabel(ticket.estado) }}
                                </span>
                            </td>
                            <td class="px-3 py-3 border-l border-l-gray-200 text-center whitespace-nowrap text-gray-500">
                                {{ ticket.fecha }}
                            </td>
                            <td class="px-3 py-3 border-l border-l-gray-200 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" title="Ver detalle"
                                        class="p-1.5 text-gray-400 hover:text-gray-700 transition"
                                        @click="verDetalle(ticket)">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button type="button" title="Ver historial"
                                        class="p-1.5 text-gray-400 hover:text-blue-600 transition"
                                        @click="verHistorial(ticket)">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </button>
                                    <button v-if="ticket.estado === 'ATENDIDO'" type="button"
                                        title="Dar conformidad"
                                        class="p-1.5 text-gray-400 hover:text-emerald-600 transition"
                                        @click="abrirConformidad(ticket)">
                                        <i class="fa-solid fa-circle-check"></i>
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
                                <span :class="['px-2 py-1 rounded-5px text-xs font-medium', estadoClase(ticket.estado)]">
                                    {{ estadoLabel(ticket.estado) }}
                                </span>
                            </td>
                            <td class="px-3 py-3 border-l border-l-gray-200 text-center whitespace-nowrap">
                                {{ ticket.fecha }}
                            </td>
                            <td class="px-3 py-3 border-l border-l-gray-200 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button type="button" title="Ver detalle"
                                        class="p-1.5 text-gray-300 hover:text-gray-600 transition"
                                        @click="verDetalle(ticket)">
                                        <i class="fa-solid fa-eye"></i>
                                    </button>
                                    <button type="button" title="Ver historial"
                                        class="p-1.5 text-gray-300 hover:text-blue-500 transition"
                                        @click="verHistorial(ticket)">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </template>
                </template>
            </TableBase>
        </template>

        <!-- ── Modal: Detalle ──────────────────────────────────────── -->
        <Teleport to="body">
            <div v-if="modalDetalle"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
                @click.self="modalDetalle = false">
                <div class="bg-white rounded-5px shadow-xl w-full max-w-xl mx-4 flex flex-col max-h-[85vh]">
                    <!-- Header -->
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

                    <!-- Body -->
                    <div class="overflow-y-auto px-5 py-4 space-y-4">

                        <!-- Info general -->
                        <div class="grid grid-cols-2 gap-x-6 gap-y-3 text-xs">
                            <div>
                                <p class="text-gray-400 mb-0.5">Código</p>
                                <p class="font-medium text-gray-700">{{ ticketDetalle?.codigo }}</p>
                            </div>
                            <div>
                                <p class="text-gray-400 mb-0.5">Estado</p>
                                <span :class="['px-2 py-1 rounded-5px text-xs font-medium', estadoClase(ticketDetalle?.estado)]">
                                    {{ estadoLabel(ticketDetalle?.estado) }}
                                </span>
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

                        <!-- Asunto -->
                        <div>
                            <p class="text-xs text-gray-400 mb-1">Asunto</p>
                            <p class="text-sm font-medium text-gray-700">{{ ticketDetalle?.asunto }}</p>
                        </div>

                        <!-- Descripción -->
                        <div v-if="ticketDetalle?.descripcion">
                            <p class="text-xs text-gray-400 mb-1">Descripción</p>
                            <p class="text-sm text-gray-600 whitespace-pre-line leading-relaxed">{{ ticketDetalle?.descripcion }}</p>
                        </div>

                        <!-- Resolución -->
                        <div v-if="ticketDetalle?.resolucion">
                            <p class="text-xs text-gray-400 mb-1">Resolución</p>
                            <p class="text-sm text-gray-600 whitespace-pre-line leading-relaxed bg-emerald-50 border border-emerald-100 rounded-5px px-3 py-2">
                                {{ ticketDetalle?.resolucion }}
                            </p>
                        </div>

                        <!-- Archivos adjuntos -->
                        <div>
                            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">
                                <i class="fa-solid fa-paperclip mr-1"></i>Archivos adjuntos
                            </p>
                            <div v-if="!ticketDetalle?.archivos?.length"
                                class="text-xs text-gray-400 italic">
                                Sin archivos adjuntos
                            </div>
                            <div v-else class="space-y-2">
                                <a v-for="arch in ticketDetalle.archivos" :key="arch.id"
                                    :href="arch.ruta" target="_blank"
                                    class="flex items-center gap-3 p-2.5 border border-gray-200 rounded-5px hover:border-blue-300 hover:bg-blue-50/40 transition group">
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
                            <p class="text-xs text-gray-400">{{ ticketActivo?.codigo }}</p>
                        </div>
                        <button type="button" @click="modalHistorial = false"
                            class="text-gray-400 hover:text-gray-600 transition">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                    <div class="overflow-y-auto px-5 py-4">
                        <div v-if="!ticketActivo?.historial?.length"
                            class="text-center text-gray-400 py-8 text-sm">
                            Sin movimientos registrados
                        </div>
                        <div v-else class="relative">
                            <div class="absolute left-3.5 top-2 bottom-2 w-px bg-gray-200"></div>
                            <div v-for="h in ticketActivo.historial" :key="h.id" class="relative flex gap-4 pb-5">
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
                                        <span :class="['text-xs font-semibold px-2 py-0.5 rounded-5px', estadoClase(h.estado_nuevo)]">
                                            {{ estadoLabel(h.estado_nuevo) }}
                                        </span>
                                        <span v-if="h.es_conformidad"
                                            class="text-xs bg-emerald-100 text-emerald-600 px-2 py-0.5 rounded-5px font-medium">
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
        </Teleport>

        <!-- ── Modal: Conformidad ───────────────────────────────────── -->
        <Teleport to="body">
            <div v-if="modalConformidad"
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/40"
                @click.self="modalConformidad = false">
                <div class="bg-white rounded-5px shadow-xl w-full max-w-md mx-4">
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
                                class="w-full border border-gray-300 focus:border-emerald-500 rounded-5px py-2.5 px-2.5 text-sm focus:outline-emerald-500 resize-none">
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
                            class="bg-emerald-500 border-b-2 border-b-emerald-600 text-white px-4 py-2 rounded-5px text-sm cursor-pointer hover:bg-emerald-600 transition disabled:opacity-60"
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
