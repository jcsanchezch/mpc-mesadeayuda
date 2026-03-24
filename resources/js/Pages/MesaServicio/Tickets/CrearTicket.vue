<script setup>
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import ButtonBase from '@/Components/ButtonBase.vue';
import {useForm} from '@inertiajs/vue3';
import {ref, computed} from 'vue';
import {route} from 'ziggy-js';

const props = defineProps({
    canales: {type: Array, default: () => []},
    categorias: {type: Array, default: () => []},
});

const emit = defineEmits(['cancelar']);

const modo = ref('1');
const servicioSeleccionado = ref(null);
const searchServicio = ref('');
const showDropdown = ref(false);

// Trabajador async
const searchTrabajador = ref('');
const showDropdownTrabajador = ref(false);
const trabajadorSeleccionado = ref(null);
const resultadosTrabajadores = ref([]);
const buscandoTrabajador = ref(false);
let debounceTimer = null;

const form = useForm({
    trabajador_id: '',
    canal_id: '',
    modo: '1',
    servicio_id: '',
    asunto: '',
    celular: '',
    descripcion: '',
    archivos: [],
});

// ── Búsqueda async trabajador ─────────────────────────────────
const onTrabajadorInput = async () => {
    if (trabajadorSeleccionado.value && searchTrabajador.value !== trabajadorSeleccionado.value.label) {
        trabajadorSeleccionado.value = null;
        form.trabajador_id = '';
        form.celular = '';
    }

    clearTimeout(debounceTimer);
    const q = searchTrabajador.value.trim();

    if (q.length < 3) {
        resultadosTrabajadores.value = [];
        showDropdownTrabajador.value = false;
        return;
    }

    showDropdownTrabajador.value = true;
    buscandoTrabajador.value = true;

    debounceTimer = setTimeout(async () => {
        try {
            const res = await fetch(route('mesadeayuda.trabajadores.buscar') + '?q=' + encodeURIComponent(q), {
                headers: {'X-Requested-With': 'XMLHttpRequest'},
            });
            resultadosTrabajadores.value = await res.json();
        } finally {
            buscandoTrabajador.value = false;
        }
    }, 300);
};

const hideDropdownTrabajador = () => setTimeout(() => showDropdownTrabajador.value = false, 150);
const hideDropdown = () => setTimeout(() => showDropdown.value = false, 150);

const selectTrabajador = (trab) => {
    trabajadorSeleccionado.value = trab;
    searchTrabajador.value = trab.label;
    showDropdownTrabajador.value = false;
    form.trabajador_id = trab.id;
    form.celular = trab.celular ?? '';
};

// ── Filtros de búsqueda ───────────────────────────────────────
const filteredCategorias = computed(() => {
    const q = searchServicio.value.trim().toLowerCase();
    if (!q) return props.categorias;
    return props.categorias
        .map(cat => ({...cat, servicios: cat.servicios.filter(s => s.nombre.toLowerCase().includes(q))}))
        .filter(cat => cat.servicios.length > 0);
});

// ── Selecciones ───────────────────────────────────────────────
const seleccionarModo = (m) => {
    modo.value = m;
    form.modo = m;
    form.servicio_id = '';
    form.asunto = '';
    form.descripcion = '';
    form.archivos = [];
    servicioSeleccionado.value = null;
    searchServicio.value = '';
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
    form.servicio_id = srv.id;
    searchServicio.value = srv.nombre;
    showDropdown.value = false;
    onServicioChange();
};

const onSearchServicioInput = () => {
    if (servicioSeleccionado.value && searchServicio.value !== servicioSeleccionado.value.nombre) {
        servicioSeleccionado.value = null;
        form.servicio_id = '';
        form.asunto = '';
    }
    showDropdown.value = true;
};

const cancelar = () => {
    modo.value = '1';
    trabajadorSeleccionado.value = null;
    searchTrabajador.value = '';
    resultadosTrabajadores.value = [];
    searchServicio.value = '';
    form.reset();
    emit('cancelar');
};

const submit = () => {
    form.post(route('mesadeayuda.tickets.crear'), {
        onSuccess: () => cancelar(),
    });
};
</script>

<template>
    <div class="w-full space-y-5">

        <div class="flex items-center gap-3">
            <button type="button" @click="cancelar"
                    class="text-gray-400 hover:text-gray-600 transition">
                <i class="fa-solid fa-arrow-left"></i>
            </button>
            <h2 class="text-sm font-semibold text-gray-700">Nuevo Ticket</h2>
        </div>

        <div class="grid grid-cols-2 gap-5 items-start">

            <!-- ── Columna izquierda ───────────────────────────────── -->
            <div class="space-y-5">

                <!-- Card: Datos del solicitante -->
                <div class="bg-white border border-gray-200 rounded-5px p-6 space-y-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Solicitante</p>

                    <!-- Trabajador (búsqueda async) -->
                    <div class="relative">
                        <InputLabel value="Trabajador *"/>
                        <div class="relative mt-1">
                            <input
                                v-model="searchTrabajador"
                                @input="onTrabajadorInput"
                                @blur="hideDropdownTrabajador"
                                type="text"
                                placeholder="Escriba DNI o nombre (mínimo 4 caracteres)..."
                                class="w-full border border-gray-300 focus:border-blue-500 rounded-5px py-2.5 pl-2.5 pr-8 bg-white text-sm focus:outline-blue-500"
                                :class="{ 'border-red-400': form.errors.trabajador_id }"
                                autocomplete="off"
                            />
                            <i v-if="buscandoTrabajador"
                               class="fa-solid fa-circle-notch fa-spin absolute right-2.5 top-1/2 -translate-y-1/2 text-blue-400 text-xs pointer-events-none"></i>
                            <i v-else
                               class="fa-solid fa-magnifying-glass absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>

                            <div v-if="showDropdownTrabajador"
                                 class="absolute z-20 w-full bg-white border border-gray-200 rounded-5px shadow-lg mt-1 max-h-56 overflow-y-auto">
                                <template v-if="resultadosTrabajadores.length">
                                    <button v-for="trab in resultadosTrabajadores" :key="trab.id"
                                            type="button"
                                            @mousedown="selectTrabajador(trab)"
                                            class="w-full text-left px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                        {{ trab.label }}
                                    </button>
                                </template>
                                <div v-else-if="!buscandoTrabajador" class="px-4 py-3 text-sm text-gray-400 text-center">
                                    Sin resultados
                                </div>
                            </div>
                        </div>
                        <p v-if="form.errors.trabajador_id" class="mt-1 text-xs text-red-500">{{
                                form.errors.trabajador_id
                            }}</p>
                    </div>

                    <!-- Info del trabajador -->
                    <div class="space-y-3">
                        <div>
                            <InputLabel value="Dependencia"/>
                            <input :value="trabajadorSeleccionado?.dependencia ?? '—'" readonly
                                   class="mt-1 w-full border border-gray-200 bg-gray-50 text-gray-600 rounded-5px py-2.5 px-2.5 text-sm cursor-default"/>
                        </div>
                        <div>
                            <InputLabel value="Local"/>
                            <input :value="trabajadorSeleccionado?.local ?? '—'" readonly
                                   class="mt-1 w-full border border-gray-200 bg-gray-50 text-gray-600 rounded-5px py-2.5 px-2.5 text-sm cursor-default"/>
                        </div>
                        <div>
                            <InputLabel value="Celular de contacto"/>
                            <TextInput v-model="form.celular" class="mt-1 w-full"
                                       placeholder="Ej. 987654321" maxlength="15"
                                       :class="{ 'border-red-400': form.errors.celular }"/>
                            <p v-if="form.errors.celular" class="mt-1 text-xs text-red-500">{{ form.errors.celular }}</p>
                        </div>
                    </div>
                </div>

                <!-- Card: Canal de atención -->
                <div class="bg-white border border-gray-200 rounded-5px p-6 space-y-5">
                    <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Canal de atención</p>
                    <div>
                        <InputLabel value="Canal *"/>
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

            </div>

            <!-- ── Columna derecha ─────────────────────────────────── -->
            <div class="space-y-5">

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
                                <InputLabel value="Asunto *"/>
                                <TextInput v-model="form.asunto" class="mt-1 w-full"
                                           :class="{ 'border-red-400': form.errors.asunto }"
                                           placeholder="Describa brevemente el motivo del ticket" maxlength="500"/>
                                <p v-if="form.errors.asunto" class="mt-1 text-xs text-red-500">{{ form.errors.asunto }}</p>
                            </div>
                            <div>
                                <InputLabel value="Descripción"/>
                                <textarea v-model="form.descripcion" rows="5"
                                          placeholder="Detalle el problema o solicitud (opcional)"
                                          class="mt-1 w-full border border-gray-300 focus:border-blue-500 rounded-5px py-2.5 px-2.5 text-sm focus:outline-blue-500 resize-none"
                                          :class="{ 'border-red-400': form.errors.descripcion }"></textarea>
                                <p v-if="form.errors.descripcion" class="mt-1 text-xs text-red-500">
                                    {{ form.errors.descripcion }}</p>
                            </div>
                            <div>
                                <InputLabel value="Archivos adjuntos"/>
                                <label
                                    class="mt-1 flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-300 rounded-5px py-6 px-4 cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition">
                                    <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-400 mb-2"></i>
                                    <span class="text-sm text-gray-500">Arrastra archivos aquí o <span
                                        class="text-blue-500 underline">selecciona</span></span>
                                    <span class="text-xs text-gray-400 mt-1">PDF, Word, Excel, imágenes — máx. 10 MB por archivo</span>
                                    <input type="file" multiple class="hidden"
                                           @change="(e) => form.archivos = Array.from(e.target.files)"/>
                                </label>
                                <p v-if="form.errors.archivos" class="mt-1 text-xs text-red-500">{{
                                        form.errors.archivos
                                    }}</p>
                                <ul v-if="form.archivos.length" class="mt-2 space-y-1">
                                    <li v-for="(f, i) in form.archivos" :key="i"
                                        class="flex items-center justify-between text-xs text-gray-600 bg-gray-50 border border-gray-200 rounded-5px px-3 py-1.5">
                                        <span class="truncate"><i
                                            class="fa-solid fa-file mr-1.5 text-gray-400"></i>{{ f.name }}</span>
                                        <button type="button"
                                                class="ml-2 text-gray-400 hover:text-red-500 transition flex-shrink-0"
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
                                <InputLabel value="Servicio *"/>
                                <div class="relative mt-1">
                                    <input
                                        v-model="searchServicio"
                                        @input="onSearchServicioInput"
                                        @focus="showDropdown = true"
                                        @blur="hideDropdown"
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
                                                <div
                                                    class="px-3 py-1.5 text-xs font-semibold text-gray-400 uppercase bg-gray-50 sticky top-0">
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
                                        <div v-else class="px-4 py-3 text-sm text-gray-400 text-center">Sin coincidencias
                                        </div>
                                    </div>
                                </div>
                                <p v-if="form.errors.servicio_id" class="mt-1 text-xs text-red-500">
                                    {{ form.errors.servicio_id }}</p>
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
                        <div class="bg-white border border-gray-200 rounded-5px p-6 space-y-5">
                            <div>
                                <InputLabel value="Asunto"/>
                                <input :value="form.asunto || '—'" readonly
                                       class="mt-1 w-full border border-gray-200 bg-gray-50 text-gray-600 rounded-5px py-2.5 px-2.5 text-sm cursor-default"/>
                            </div>
                            <div>
                                <InputLabel value="Descripción"/>
                                <textarea v-model="form.descripcion" rows="5"
                                          placeholder="Detalle el problema o solicitud (opcional)"
                                          class="mt-1 w-full border border-gray-300 focus:border-blue-500 rounded-5px py-2.5 px-2.5 text-sm focus:outline-blue-500 resize-none"
                                          :class="{ 'border-red-400': form.errors.descripcion }"></textarea>
                                <p v-if="form.errors.descripcion" class="mt-1 text-xs text-red-500">
                                    {{ form.errors.descripcion }}</p>
                            </div>
                            <div>
                                <InputLabel value="Archivos adjuntos"/>
                                <label
                                    class="mt-1 flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-300 rounded-5px py-6 px-4 cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition">
                                    <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-400 mb-2"></i>
                                    <span class="text-sm text-gray-500">Arrastra archivos aquí o <span
                                        class="text-blue-500 underline">selecciona</span></span>
                                    <span class="text-xs text-gray-400 mt-1">PDF, Word, Excel, imágenes — máx. 10 MB por archivo</span>
                                    <input type="file" multiple class="hidden"
                                           @change="(e) => form.archivos = Array.from(e.target.files)"/>
                                </label>
                                <p v-if="form.errors.archivos" class="mt-1 text-xs text-red-500">{{
                                        form.errors.archivos
                                    }}</p>
                                <ul v-if="form.archivos.length" class="mt-2 space-y-1">
                                    <li v-for="(f, i) in form.archivos" :key="i"
                                        class="flex items-center justify-between text-xs text-gray-600 bg-gray-50 border border-gray-200 rounded-5px px-3 py-1.5">
                                        <span class="truncate"><i
                                            class="fa-solid fa-file mr-1.5 text-gray-400"></i>{{ f.name }}</span>
                                        <button type="button"
                                                class="ml-2 text-gray-400 hover:text-red-500 transition flex-shrink-0"
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
                                    :disabled="form.processing || !form.trabajador_id || !form.canal_id"/>
                        <button type="button" class="text-sm text-gray-500 hover:text-gray-700 transition"
                                @click="cancelar">
                            Cancelar
                        </button>
                    </div>
                </form>

            </div>

        </div>

    </div>
</template>
