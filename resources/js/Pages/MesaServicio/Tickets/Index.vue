<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import TableBase from '@/Components/TableBase.vue';
import ButtonBase from '@/Components/ButtonBase.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { route } from 'ziggy-js';

const props = defineProps({
    tickets:    { type: Array, default: () => [] },
    canales:    { type: Array, default: () => [] },
    categorias: { type: Array, default: () => [] },
});

// ── Crear Ticket ─────────────────────────────────────────────
const mostrarFormulario    = ref(false);
const modo                 = ref(null);
const servicioSeleccionado = ref(null);
const searchServicio       = ref('');
const showDropdown         = ref(false);

// Trabajador async
const searchTrabajador        = ref('');
const showDropdownTrabajador  = ref(false);
const trabajadorSeleccionado  = ref(null);
const resultadosTrabajadores  = ref([]);
const buscandoTrabajador      = ref(false);
let   debounceTimer           = null;

const form = useForm({
    trabajador_id: '',
    canal_id:      '',
    modo:          '',
    servicio_id:   '',
    asunto:        '',
    celular:       '',
    descripcion:   '',
    archivos:      [],
});

// ── Búsqueda async trabajador ─────────────────────────────────
const onTrabajadorInput = async () => {
    if (trabajadorSeleccionado.value && searchTrabajador.value !== trabajadorSeleccionado.value.label) {
        trabajadorSeleccionado.value = null;
        form.trabajador_id           = '';
        form.celular                 = '';
    }

    clearTimeout(debounceTimer);
    const q = searchTrabajador.value.trim();

    if (q.length < 4) {
        resultadosTrabajadores.value = [];
        showDropdownTrabajador.value = false;
        return;
    }

    showDropdownTrabajador.value = true;
    buscandoTrabajador.value     = true;

    debounceTimer = setTimeout(async () => {
        try {
            const res = await fetch(route('mesadeayuda.trabajadores.buscar') + '?q=' + encodeURIComponent(q), {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            });
            resultadosTrabajadores.value = await res.json();
        } finally {
            buscandoTrabajador.value = false;
        }
    }, 300);
};

const selectTrabajador = (trab) => {
    trabajadorSeleccionado.value  = trab;
    searchTrabajador.value        = trab.label;
    showDropdownTrabajador.value  = false;
    form.trabajador_id            = trab.id;
    form.celular                  = trab.celular ?? '';
};

// ── Filtros de búsqueda ───────────────────────────────────────
const filteredCategorias = computed(() => {
    const q = searchServicio.value.trim().toLowerCase();
    if (!q) return props.categorias;
    return props.categorias
        .map(cat => ({ ...cat, servicios: cat.servicios.filter(s => s.nombre.toLowerCase().includes(q)) }))
        .filter(cat => cat.servicios.length > 0);
});

// ── Selecciones ───────────────────────────────────────────────
const seleccionarModo = (m) => {
    modo.value           = m;
    form.modo            = m;
    form.servicio_id     = '';
    form.asunto          = '';
    form.descripcion     = '';
    form.archivos        = [];
    servicioSeleccionado.value = null;
    searchServicio.value       = '';
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

const selectServicio = (srv) => {
    form.servicio_id       = srv.id;
    searchServicio.value   = srv.nombre;
    showDropdown.value     = false;
    onServicioChange();
};

const onSearchServicioInput = () => {
    if (servicioSeleccionado.value && searchServicio.value !== servicioSeleccionado.value.nombre) {
        servicioSeleccionado.value = null;
        form.servicio_id           = '';
        form.asunto                = '';
    }
    showDropdown.value = true;
};

const cancelarFormulario = () => {
    mostrarFormulario.value      = false;
    modo.value                   = null;
    trabajadorSeleccionado.value = null;
    searchTrabajador.value       = '';
    resultadosTrabajadores.value = [];
    searchServicio.value         = '';
    form.reset();
};

const submit = () => {
    form.post(route('mesadeayuda.tickets.crear'), {
        onSuccess: () => cancelarFormulario(),
    });
};

// ── Modal detalle ─────────────────────────────────────────────
const modalDetalle  = ref(false);
const ticketDetalle = ref(null);

const verDetalle = (ticket) => {
    ticketDetalle.value = ticket;
    modalDetalle.value  = true;
};

// ── Modal historial ──────────────────────────────────────────
const modalHistorial  = ref(false);
const ticketHistorial = ref(null);

const verHistorial = (ticket) => {
    ticketHistorial.value = ticket;
    modalHistorial.value  = true;
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
        <template #header>Mesa de Servicio — Tickets sin asignar</template>

        <!-- ── Vista: Formulario ────────────────────────────────────── -->
        <div v-if="mostrarFormulario" class="max-w-2xl space-y-5">

            <div class="flex items-center gap-3">
                <button type="button" @click="cancelarFormulario"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <h2 class="text-sm font-semibold text-gray-700">Nuevo Ticket</h2>
            </div>

            <!-- Card: Datos del solicitante -->
            <div class="bg-white border border-gray-200 rounded-5px p-6 space-y-5">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Solicitante</p>

                <!-- Trabajador (búsqueda async) -->
                <div class="relative">
                    <InputLabel value="Trabajador *" />
                    <div class="relative mt-1">
                        <input
                            v-model="searchTrabajador"
                            @input="onTrabajadorInput"
                            @blur="setTimeout(() => showDropdownTrabajador = false, 150)"
                            type="text"
                            placeholder="Escriba DNI o nombre (mínimo 4 caracteres)..."
                            class="w-full border border-gray-300 focus:border-blue-500 rounded-5px py-2.5 pl-2.5 pr-8 bg-white text-sm focus:outline-blue-500"
                            :class="{ 'border-red-400': form.errors.trabajador_id }"
                            autocomplete="off"
                        />
                        <i v-if="buscandoTrabajador"
                            class="fa-solid fa-circle-notch fa-spin absolute right-2.5 top-1/2 -translate-y-1/2 text-blue-400 text-xs pointer-events-none"></i>
                        <i v-else class="fa-solid fa-magnifying-glass absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>

                        <div v-if="showDropdownTrabajador"
                            class="absolute z-20 w-full bg-white border border-gray-200 rounded-5px shadow-lg mt-1 max-h-56 overflow-y-auto">
                            <template v-if="resultadosTrabajadores.length">
                                <button v-for="trab in resultadosTrabajadores" :key="trab.id"
                                    type="button"
                                    @mousedown="selectTrabajador(trab)"
                                    class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors font-mono">
                                    {{ trab.label }}
                                </button>
                            </template>
                            <div v-else-if="!buscandoTrabajador" class="px-4 py-3 text-sm text-gray-400 text-center">
                                Sin resultados
                            </div>
                        </div>
                    </div>
                    <p v-if="form.errors.trabajador_id" class="mt-1 text-xs text-red-500">{{ form.errors.trabajador_id }}</p>
                </div>

                <!-- Info del trabajador seleccionado -->
                <div v-if="trabajadorSeleccionado" class="grid grid-cols-3 gap-3">
                    <div>
                        <InputLabel value="Dependencia" />
                        <input :value="trabajadorSeleccionado.dependencia ?? '—'" readonly
                            class="mt-1 w-full border border-gray-200 bg-gray-50 text-gray-600 rounded-5px py-2.5 px-2.5 text-xs cursor-default truncate" />
                    </div>
                    <div>
                        <InputLabel value="Cargo" />
                        <input :value="trabajadorSeleccionado.cargo ?? '—'" readonly
                            class="mt-1 w-full border border-gray-200 bg-gray-50 text-gray-600 rounded-5px py-2.5 px-2.5 text-xs cursor-default truncate" />
                    </div>
                    <div>
                        <InputLabel value="Local" />
                        <input :value="trabajadorSeleccionado.local ?? '—'" readonly
                            class="mt-1 w-full border border-gray-200 bg-gray-50 text-gray-600 rounded-5px py-2.5 px-2.5 text-xs cursor-default truncate" />
                    </div>
                </div>

                <!-- Canal -->
                <div>
                    <InputLabel value="Canal de atención *" />
                    <select v-model="form.canal_id"
                        class="mt-1 w-full border border-gray-300 focus:border-blue-500 rounded-5px py-2.5 px-2.5 text-sm bg-white focus:outline-blue-500"
                        :class="{ 'border-red-400': form.errors.canal_id }">
                        <option value="" disabled>Seleccione un canal...</option>
                        <option v-for="canal in canales" :key="canal.id" :value="canal.id">
                            {{ canal.label }}
                        </option>
                    </select>
                    <p v-if="form.errors.canal_id" class="mt-1 text-xs text-red-500">{{ form.errors.canal_id }}</p>
                </div>
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
                            <p class="text-sm font-semibold text-gray-700">Sin servicio específico</p>
                            <p class="text-xs text-gray-400 mt-0.5">Ingrese el asunto manualmente</p>
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
                            <p class="text-sm font-semibold text-gray-700">Con servicio específico</p>
                            <p class="text-xs text-gray-400 mt-0.5">Seleccione el servicio del catálogo</p>
                        </div>
                    </div>
                </button>
            </div>

            <!-- Formulario -->
            <form v-if="modo" @submit.prevent="submit" class="space-y-4">

                <!-- MODO 1 -->
                <template v-if="modo === '1'">
                    <div class="bg-white border border-gray-200 rounded-5px p-6 space-y-5">
                        <div>
                            <InputLabel value="Asunto *" />
                            <TextInput v-model="form.asunto" class="mt-1 w-full"
                                :class="{ 'border-red-400': form.errors.asunto }"
                                placeholder="Describa brevemente el motivo del ticket" maxlength="500" />
                            <p v-if="form.errors.asunto" class="mt-1 text-xs text-red-500">{{ form.errors.asunto }}</p>
                        </div>
                        <div>
                            <InputLabel value="Celular de contacto" />
                            <TextInput v-model="form.celular" class="mt-1 w-full"
                                placeholder="Ej. 987654321" maxlength="15"
                                :class="{ 'border-red-400': form.errors.celular }" />
                            <p v-if="form.errors.celular" class="mt-1 text-xs text-red-500">{{ form.errors.celular }}</p>
                        </div>
                        <div>
                            <InputLabel value="Descripción" />
                            <textarea v-model="form.descripcion" rows="5"
                                placeholder="Detalle el problema o solicitud (opcional)"
                                class="mt-1 w-full border border-gray-300 focus:border-blue-500 rounded-5px py-2.5 px-2.5 text-sm focus:outline-blue-500 resize-none"
                                :class="{ 'border-red-400': form.errors.descripcion }"></textarea>
                            <p v-if="form.errors.descripcion" class="mt-1 text-xs text-red-500">{{ form.errors.descripcion }}</p>
                        </div>
                        <div>
                            <InputLabel value="Archivos adjuntos" />
                            <label class="mt-1 flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-300 rounded-5px py-6 px-4 cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition">
                                <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-400 mb-2"></i>
                                <span class="text-sm text-gray-500">Arrastra archivos aquí o <span class="text-blue-500 underline">selecciona</span></span>
                                <span class="text-xs text-gray-400 mt-1">PDF, Word, Excel, imágenes — máx. 10 MB por archivo</span>
                                <input type="file" multiple class="hidden"
                                    @change="(e) => form.archivos = Array.from(e.target.files)" />
                            </label>
                            <p v-if="form.errors.archivos" class="mt-1 text-xs text-red-500">{{ form.errors.archivos }}</p>
                            <ul v-if="form.archivos.length" class="mt-2 space-y-1">
                                <li v-for="(f, i) in form.archivos" :key="i"
                                    class="flex items-center justify-between text-xs text-gray-600 bg-gray-50 border border-gray-200 rounded-5px px-3 py-1.5">
                                    <span class="truncate"><i class="fa-solid fa-file mr-1.5 text-gray-400"></i>{{ f.name }}</span>
                                    <button type="button" class="ml-2 text-gray-400 hover:text-red-500 transition flex-shrink-0"
                                        @click="form.archivos = form.archivos.filter((_, j) => j !== i)">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </template>

                <!-- MODO 2 -->
                <template v-if="modo === '2'">
                    <!-- Card 1: Selector de servicio -->
                    <div class="bg-white border border-gray-200 rounded-5px p-6 space-y-4">
                        <div class="relative">
                            <InputLabel value="Servicio *" />
                            <div class="relative mt-1">
                                <input
                                    v-model="searchServicio"
                                    @input="onSearchServicioInput"
                                    @focus="showDropdown = true"
                                    @blur="setTimeout(() => showDropdown = false, 150)"
                                    type="text"
                                    placeholder="Buscar servicio..."
                                    class="w-full border border-gray-300 focus:border-blue-500 rounded-5px py-2.5 pl-2.5 pr-8 bg-white text-sm focus:outline-blue-500"
                                    :class="{ 'border-red-400': form.errors.servicio_id }"
                                    autocomplete="off"
                                />
                                <i class="fa-solid fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                                <div v-if="showDropdown"
                                    class="absolute z-20 w-full bg-white border border-gray-200 rounded-5px shadow-lg mt-1 max-h-60 overflow-y-auto">
                                    <template v-if="filteredCategorias.length">
                                        <template v-for="cat in filteredCategorias" :key="cat.id">
                                            <div class="px-3 py-1.5 text-xs font-semibold text-gray-400 uppercase bg-gray-50 sticky top-0">
                                                {{ cat.nombre }}
                                            </div>
                                            <button v-for="srv in cat.servicios" :key="srv.id"
                                                type="button"
                                                @mousedown="selectServicio(srv)"
                                                class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                                {{ srv.nombre }}
                                            </button>
                                        </template>
                                    </template>
                                    <div v-else class="px-4 py-3 text-sm text-gray-400 text-center">Sin coincidencias</div>
                                </div>
                            </div>
                            <p v-if="form.errors.servicio_id" class="mt-1 text-xs text-red-500">{{ form.errors.servicio_id }}</p>
                        </div>
                        <div v-if="servicioSeleccionado?.descripcion"
                            class="bg-blue-50 border border-blue-100 rounded-5px px-4 py-3 text-sm text-blue-700 leading-relaxed">
                            {{ servicioSeleccionado.descripcion }}
                        </div>
                        <div v-if="servicioSeleccionado?.formatos?.length">
                            <p class="text-xs font-semibold text-gray-600 mb-1.5">
                                <i class="fa-solid fa-file-lines mr-1"></i>Formatos requeridos
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

                    <!-- Card 2: Detalle del ticket -->
                    <div v-if="servicioSeleccionado" class="bg-white border border-gray-200 rounded-5px p-6 space-y-5">
                        <div>
                            <InputLabel value="Asunto" />
                            <input :value="form.asunto" readonly
                                class="mt-1 w-full border border-gray-200 bg-gray-50 text-gray-600 rounded-5px py-2.5 px-2.5 text-sm cursor-default" />
                        </div>
                        <div>
                            <InputLabel value="Celular de contacto" />
                            <TextInput v-model="form.celular" class="mt-1 w-full"
                                placeholder="Ej. 987654321" maxlength="15"
                                :class="{ 'border-red-400': form.errors.celular }" />
                            <p v-if="form.errors.celular" class="mt-1 text-xs text-red-500">{{ form.errors.celular }}</p>
                        </div>
                        <div>
                            <InputLabel value="Descripción" />
                            <textarea v-model="form.descripcion" rows="5"
                                placeholder="Detalle el problema o solicitud (opcional)"
                                class="mt-1 w-full border border-gray-300 focus:border-blue-500 rounded-5px py-2.5 px-2.5 text-sm focus:outline-blue-500 resize-none"
                                :class="{ 'border-red-400': form.errors.descripcion }"></textarea>
                            <p v-if="form.errors.descripcion" class="mt-1 text-xs text-red-500">{{ form.errors.descripcion }}</p>
                        </div>
                        <div>
                            <InputLabel value="Archivos adjuntos" />
                            <label class="mt-1 flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-300 rounded-5px py-6 px-4 cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition">
                                <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-400 mb-2"></i>
                                <span class="text-sm text-gray-500">Arrastra archivos aquí o <span class="text-blue-500 underline">selecciona</span></span>
                                <span class="text-xs text-gray-400 mt-1">PDF, Word, Excel, imágenes — máx. 10 MB por archivo</span>
                                <input type="file" multiple class="hidden"
                                    @change="(e) => form.archivos = Array.from(e.target.files)" />
                            </label>
                            <p v-if="form.errors.archivos" class="mt-1 text-xs text-red-500">{{ form.errors.archivos }}</p>
                            <ul v-if="form.archivos.length" class="mt-2 space-y-1">
                                <li v-for="(f, i) in form.archivos" :key="i"
                                    class="flex items-center justify-between text-xs text-gray-600 bg-gray-50 border border-gray-200 rounded-5px px-3 py-1.5">
                                    <span class="truncate"><i class="fa-solid fa-file mr-1.5 text-gray-400"></i>{{ f.name }}</span>
                                    <button type="button" class="ml-2 text-gray-400 hover:text-red-500 transition flex-shrink-0"
                                        @click="form.archivos = form.archivos.filter((_, j) => j !== i)">
                                        <i class="fa-solid fa-xmark"></i>
                                    </button>
                                </li>
                            </ul>
                        </div>
                    </div>
                </template>

                <!-- Acciones -->
                <div class="flex items-center gap-3 pt-1">
                    <ButtonBase type="submit" label="Crear Ticket" icon="fa-solid fa-paper-plane"
                        :disabled="form.processing || !form.trabajador_id || !form.canal_id" />
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
                        <th class="px-3 py-3 border-l border-l-gray-200">Solicitante</th>
                        <th class="px-3 py-3 border-l border-l-gray-200">Celular</th>
                        <th class="px-3 py-3 border-l border-l-gray-200">Servicio</th>
                        <th class="px-3 py-3 border-l border-l-gray-200">Asunto</th>
                        <th class="px-3 py-3 border-l border-l-gray-200 text-center">Estado</th>
                        <th class="px-3 py-3 border-l border-l-gray-200 text-center">Fecha</th>
                        <th class="px-3 py-3 border-l border-l-gray-200 text-center">Acciones</th>
                    </tr>
                </template>
                <template #tbody>
                    <tr v-if="!tickets.length">
                        <td colspan="9" class="text-center text-gray-400 py-10 font-light text-base">
                            No hay tickets pendientes de asignación
                        </td>
                    </tr>
                    <tr v-for="(ticket, i) in tickets" :key="ticket.id"
                        class="text-xs text-gray-600 hover:bg-blue-50 transition duration-150">
                        <td class="px-3 py-3 text-center text-gray-400">{{ i + 1 }}</td>
                        <td class="px-3 py-3 border-l border-l-gray-200 font-medium text-gray-700 whitespace-nowrap">
                            {{ ticket.codigo }}
                        </td>
                        <td class="px-3 py-3 border-l border-l-gray-200">
                            {{ ticket.solicitante }}
                        </td>
                        <td class="px-3 py-3 border-l border-l-gray-200 whitespace-nowrap">
                            {{ ticket.celular ?? '—' }}
                        </td>
                        <td class="px-3 py-3 border-l border-l-gray-200">
                            <div class="text-gray-400">{{ ticket.categoria ?? '—' }}</div>
                            <div>{{ ticket.servicio ?? '—' }}</div>
                        </td>
                        <td class="px-3 py-3 border-l border-l-gray-200 max-w-xs truncate">
                            {{ ticket.asunto }}
                        </td>
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
        </template>

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
                                <span :class="['px-2 py-1 rounded-5px text-xs font-medium', estadoClase(ticketDetalle?.estado)]">
                                    {{ estadoLabel(ticketDetalle?.estado) }}
                                </span>
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
                            <p class="text-sm text-gray-600 whitespace-pre-line leading-relaxed">{{ ticketDetalle?.descripcion }}</p>
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
                                        <i v-if="h.estado_anterior" class="fa-solid fa-arrow-right text-gray-300 text-xs"></i>
                                        <span :class="['text-xs font-semibold px-2 py-0.5 rounded-5px', estadoClase(h.estado_nuevo)]">
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
