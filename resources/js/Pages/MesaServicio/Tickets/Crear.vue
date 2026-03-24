<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import ButtonBase from '@/Components/ButtonBase.vue';
import {useForm, router} from '@inertiajs/vue3';
import {ref, computed} from 'vue';
import {route} from 'ziggy-js';

const props = defineProps({
    canales:      {type: Array, default: () => []},
    categorias:   {type: Array, default: () => []},
    dependencias: {type: Array, default: () => []},
    locales:      {type: Array, default: () => []},
});

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
    dependencia_id: '',
    local_id: '',
    canal_id: '',
    modo: '1',
    servicio_id: null,
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

// ── Dependencia (filtro local) ────────────────────────────────
const searchDependencia = ref('');
const showDropdownDependencia = ref(false);

const filteredDependencias = computed(() => {
    const q = searchDependencia.value.trim().toLowerCase();
    if (!q) return props.dependencias;
    return props.dependencias.filter(d => d.nombre.toLowerCase().includes(q));
});

const selectDependencia = (dep) => {
    form.dependencia_id = dep.id;
    searchDependencia.value = dep.nombre;
    showDropdownDependencia.value = false;
};

const onDependenciaInput = () => {
    const current = props.dependencias.find(d => d.id === form.dependencia_id);
    if (!current || searchDependencia.value !== current.nombre) form.dependencia_id = '';
    showDropdownDependencia.value = true;
};

const hideDropdownDependencia = () => setTimeout(() => showDropdownDependencia.value = false, 150);

// ── Local (filtro local) ──────────────────────────────────────
const searchLocal = ref('');
const showDropdownLocal = ref(false);

const filteredLocales = computed(() => {
    const q = searchLocal.value.trim().toLowerCase();
    if (!q) return props.locales;
    return props.locales.filter(l => l.nombre.toLowerCase().includes(q));
});

const selectLocal = (loc) => {
    form.local_id = loc.id;
    searchLocal.value = localLabel(loc);
    showDropdownLocal.value = false;
};

const localLabel = (loc) => loc.direccion ? `${loc.nombre} (${loc.direccion})` : loc.nombre;

const onLocalInput = () => {
    const current = props.locales.find(l => l.id === form.local_id);
    if (!current || searchLocal.value !== localLabel(current)) form.local_id = '';
    showDropdownLocal.value = true;
};

const hideDropdownLocal = () => setTimeout(() => showDropdownLocal.value = false, 150);

const selectTrabajador = (trab) => {
    trabajadorSeleccionado.value = trab;
    searchTrabajador.value = trab.label;
    showDropdownTrabajador.value = false;
    form.trabajador_id = trab.id;
    form.celular = trab.celular ?? '';
    form.dependencia_id = trab.dependencia_id ?? '';
    form.local_id = trab.local_id ?? '';
    searchDependencia.value = props.dependencias.find(d => d.id === trab.dependencia_id)?.nombre ?? '';
    const loc = props.locales.find(l => l.id === trab.local_id);
    searchLocal.value = loc ? localLabel(loc) : '';
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
    form.servicio_id = null;
    form.asunto = '';
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
        form.servicio_id = null;
    }
    showDropdown.value = true;
};

const cancelar = () => router.visit(route('mesadeayuda.tickets.index'));

// ── Validación cliente ────────────────────────────────────────
const hasAttemptedSubmit = ref(false);

const formErrors = computed(() => {
    const e = {};
    if (!form.trabajador_id) e.trabajador_id = 'Debe seleccionar un trabajador.';
    if (!form.dependencia_id) e.dependencia_id = 'Debe seleccionar una dependencia.';
    if (!form.local_id) e.local_id = 'Debe seleccionar un local.';
    if (!form.celular) {
        e.celular = 'El celular es obligatorio.';
    } else if (!/^\d+$/.test(form.celular)) {
        e.celular = 'Solo se permiten dígitos numéricos.';
    } else if (!form.celular.startsWith('9')) {
        e.celular = 'El celular debe empezar con 9.';
    }
    if (!form.canal_id) e.canal_id = 'Debe seleccionar un canal.';
    if (!form.descripcion.trim()) e.descripcion = 'La descripción es obligatoria.';
    if (modo.value === '1') {
        if (!form.asunto.trim()) e.asunto = 'El asunto es obligatorio.';
        else if (form.asunto.trim().length < 3) e.asunto = 'El asunto debe tener al menos 3 caracteres.';
    } else {
        if (!form.servicio_id) e.servicio_id = 'Debe seleccionar un servicio del catálogo.';
    }
    return e;
});

const isFormValid = computed(() => Object.keys(formErrors.value).length === 0);

const ve = (field) => form.errors[field] ?? (hasAttemptedSubmit.value ? formErrors.value[field] : null);

const submit = () => {
    hasAttemptedSubmit.value = true;
    console.log('[submit] formErrors:', JSON.parse(JSON.stringify(formErrors.value)));
    console.log('[submit] isFormValid:', isFormValid.value);
    console.log('[submit] form data:', {
        trabajador_id: form.trabajador_id,
        dependencia_id: form.dependencia_id,
        local_id: form.local_id,
        canal_id: form.canal_id,
        modo: form.modo,
        servicio_id: form.servicio_id,
        asunto: form.asunto,
        celular: form.celular,
        descripcion: form.descripcion,
        archivos: form.archivos.map(f => ({ name: f.name, size: f.size, type: f.type })),
    });
    if (!isFormValid.value) return;
    form.post(route('mesadeayuda.tickets.crear'), {
        forceFormData: true,
        onSuccess: () => cancelar(),
        onError: (errors) => console.log('[submit] server errors:', errors),
    });
};
</script>

<template>
    <AuthLayout>
        <template #header>Mesa de Servicio — Nuevo Ticket</template>

        <form @submit.prevent="submit" class="max-w-4xl space-y-4">

            <!-- ── Solicitante ─────────────────────────────────────────── -->
            <div class="bg-white border border-gray-200 rounded-5px p-6 space-y-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Solicitante</p>
                <div class="space-y-4">

                    <!-- Trabajador -->
                    <div class="relative">
                        <InputLabel value="Trabajador *"/>
                        <div class="relative mt-1">
                            <input
                                v-model="searchTrabajador"
                                @input="onTrabajadorInput"
                                @blur="hideDropdownTrabajador"
                                type="text"
                                placeholder="Escriba DNI o nombre (mínimo 4 caracteres)..."
                                class="w-full border border-gray-300 focus:border-blue-500 rounded-5px py-2.5 pl-3 pr-8 text-sm bg-white focus:outline-blue-500"
                                :class="{ 'border-red-400': ve('trabajador_id') }"
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
                                            class="w-full text-left px-3 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                        {{ trab.label }}
                                    </button>
                                </template>
                                <div v-else-if="!buscandoTrabajador"
                                     class="px-3 py-3 text-sm text-gray-400 text-center">
                                    Sin resultados
                                </div>
                            </div>
                        </div>
                        <p v-if="ve('trabajador_id')" class="mt-1 text-xs text-red-500">{{ ve('trabajador_id') }}</p>
                    </div>


                    <!-- Dependencia -->
                    <div class="relative">
                        <InputLabel value="Dependencia"/>
                        <div class="relative mt-1">
                            <input
                                v-model="searchDependencia"
                                @input="onDependenciaInput"
                                @focus="showDropdownDependencia = true"
                                @blur="hideDropdownDependencia"
                                type="text"
                                placeholder="Buscar dependencia..."
                                :class="['w-full border focus:border-blue-500 rounded-5px py-2.5 pl-3 pr-8 text-sm bg-white focus:outline-blue-500', ve('dependencia_id') ? 'border-red-400' : 'border-gray-300']"
                                autocomplete="off"
                            />
                            <i class="fa-solid fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                            <div v-if="showDropdownDependencia"
                                 class="absolute z-20 w-full bg-white border border-gray-200 rounded-5px shadow-lg mt-1 max-h-56 overflow-y-auto">
                                <button v-for="dep in filteredDependencias" :key="dep.id"
                                        type="button"
                                        @mousedown="selectDependencia(dep)"
                                        class="w-full text-left px-3 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                    {{ dep.nombre }}
                                </button>
                                <div v-if="!filteredDependencias.length" class="px-3 py-3 text-sm text-gray-400 text-center">Sin coincidencias</div>
                            </div>
                        </div>
                        <p v-if="ve('dependencia_id')" class="mt-1 text-xs text-red-500">{{ ve('dependencia_id') }}</p>
                    </div>

                    <!-- Local -->
                    <div class="relative">
                        <InputLabel value="Local"/>
                        <div class="relative mt-1">
                            <input
                                v-model="searchLocal"
                                @input="onLocalInput"
                                @focus="showDropdownLocal = true"
                                @blur="hideDropdownLocal"
                                type="text"
                                placeholder="Buscar local..."
                                :class="['w-full border focus:border-blue-500 rounded-5px py-2.5 pl-3 pr-8 text-sm bg-white focus:outline-blue-500', ve('local_id') ? 'border-red-400' : 'border-gray-300']"
                                autocomplete="off"
                            />
                            <i class="fa-solid fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                            <div v-if="showDropdownLocal"
                                 class="absolute z-20 w-full bg-white border border-gray-200 rounded-5px shadow-lg mt-1 max-h-56 overflow-y-auto">
                                <button v-for="loc in filteredLocales" :key="loc.id"
                                        type="button"
                                        @mousedown="selectLocal(loc)"
                                        class="w-full text-left px-3 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                    {{ loc.nombre }}{{ loc.direccion ? ` (${loc.direccion})` : '' }}
                                </button>
                                <div v-if="!filteredLocales.length" class="px-3 py-3 text-sm text-gray-400 text-center">Sin coincidencias</div>
                            </div>
                        </div>
                        <p v-if="ve('local_id')" class="mt-1 text-xs text-red-500">{{ ve('local_id') }}</p>
                    </div>

                    <!-- Celular -->
                    <div>
                        <InputLabel value="Celular de contacto *"/>
                        <TextInput v-model="form.celular" class="mt-1 w-full"
                                   placeholder="" maxlength="9"
                                   :class="{ 'border-red-400': ve('celular') }"/>
                        <p v-if="ve('celular')" class="mt-1 text-xs text-red-500">{{ ve('celular') }}</p>
                    </div>


                </div>
            </div>

            <div class="bg-white border border-gray-200 rounded-5px p-6 space-y-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Canal de atenciòn</p>
                <div class="space-y-4">
                    <!-- Canal -->
                    <div>
                        <InputLabel value="Canal *"/>
                        <select v-model="form.canal_id"
                                class="mt-1 w-full border border-gray-300 focus:border-blue-500 rounded-5px py-2.5 px-3 text-sm bg-white focus:outline-blue-500"
                                :class="{ 'border-red-400': ve('canal_id') }">
                            <option value="" disabled>Seleccione un canal...</option>
                            <option v-for="canal in canales" :key="canal.id" :value="canal.id">
                                {{ canal.label }}
                            </option>
                        </select>
                        <p v-if="ve('canal_id')" class="mt-1 text-xs text-red-500">{{ ve('canal_id') }}</p>
                    </div>

                </div>
            </div>

            <!-- ── Ticket ──────────────────────────────────────────────── -->
            <div class="bg-white border border-gray-200 rounded-5px p-6 space-y-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Ticket</p>
                <div class="space-y-4">

                    <!-- Switch de modo -->
                    <div class="flex gap-2">
                        <button type="button" @click="seleccionarModo('1')"
                                :class="['w-full text-left p-4 border-2 rounded-5px transition',
                                modo === '1' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-white hover:border-blue-300 hover:bg-blue-50/40']">
                            <div class="flex items-start gap-3">
                                <div :class="['mt-0.5 w-4 h-4 rounded-full border-2 flex-shrink-0 flex items-center justify-center',
                                    modo === '1' ? 'border-blue-500' : 'border-gray-300']">
                                    <div v-if="modo === '1'" class="w-2 h-2 rounded-full bg-blue-500"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-700">Sin servicio</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Asunto manual</p>
                                </div>
                            </div>
                        </button>
                        <button type="button" @click="seleccionarModo('2')"
                                :class="['w-full text-left p-4 border-2 rounded-5px transition',
                                modo === '2' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-white hover:border-blue-300 hover:bg-blue-50/40']">
                            <div class="flex items-start gap-3">
                                <div :class="['mt-0.5 w-4 h-4 rounded-full border-2 flex-shrink-0 flex items-center justify-center',
                                    modo === '2' ? 'border-blue-500' : 'border-gray-300']">
                                    <div v-if="modo === '2'" class="w-2 h-2 rounded-full bg-blue-500"></div>
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-700">Con servicio</p>
                                    <p class="text-xs text-gray-400 mt-0.5">Del catálogo</p>
                                </div>
                            </div>
                        </button>
                    </div>

                    <!-- MODO 1 -->
                    <template v-if="modo === '1'">
                        <div>
                            <InputLabel value="Asunto *"/>
                            <TextInput v-model="form.asunto" class="mt-1 w-full"
                                       :class="{ 'border-red-400': ve('asunto') }"
                                       placeholder="Describa brevemente el motivo del ticket" maxlength="500"/>
                            <p v-if="ve('asunto')" class="mt-1 text-xs text-red-500">{{ ve('asunto') }}</p>
                        </div>
                    </template>

                    <!-- MODO 2 -->
                    <template v-if="modo === '2'">
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
                                    class="w-full border border-gray-300 focus:border-blue-500 rounded-5px py-2.5 pl-3 pr-8 text-sm bg-white focus:outline-blue-500"
                                    :class="{ 'border-red-400': ve('servicio_id') }"
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
                                                    class="w-full text-left px-3 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                                {{ srv.nombre }}
                                            </button>
                                        </template>
                                    </template>
                                    <div v-else class="px-3 py-3 text-sm text-gray-400 text-center">Sin coincidencias
                                    </div>
                                </div>
                            </div>
                            <p v-if="ve('servicio_id')" class="mt-1 text-xs text-red-500">{{ ve('servicio_id') }}</p>
                        </div>
                        <div v-if="servicioSeleccionado?.descripcion"
                             class="bg-gray-50 border border-gray-200 rounded-5px px-4 py-3 text-sm text-gray-600 leading-relaxed">
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
                    </template>

                    <!-- Descripción (ambos modos) -->
                    <div>
                        <InputLabel value="Descripción *"/>
                        <textarea v-model="form.descripcion" rows="5"
                                  placeholder="Detalle el problema o solicitud"
                                  class="mt-1 w-full border border-gray-300 focus:border-blue-500 rounded-5px py-2.5 px-3 text-sm focus:outline-blue-500 resize-none"
                                  :class="{ 'border-red-400': ve('descripcion') }"></textarea>
                        <p v-if="ve('descripcion')" class="mt-1 text-xs text-red-500">{{ ve('descripcion') }}</p>
                    </div>

                    <!-- Archivos adjuntos (ambos modos) -->
                    <div>
                        <InputLabel value="Archivos adjuntos"/>
                        <label
                            class="mt-1 flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-300 rounded-5px py-6 px-4 cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-400 mb-2"></i>
                            <span class="text-sm text-gray-500">Arrastra archivos aquí o <span
                                class="text-blue-500 underline">selecciona</span></span>
                            <span
                                class="text-xs text-gray-400 mt-1">PDF, Word, Excel, imágenes — máx. 10 MB por archivo</span>
                            <input type="file" multiple class="hidden"
                                   @change="(e) => { const nuevos = Array.from(e.target.files); form.archivos = [...form.archivos, ...nuevos].slice(0, 10); e.target.value = ''; }"/>
                        </label>
                        <p v-if="form.errors.archivos" class="mt-1 text-xs text-red-500">{{ form.errors.archivos }}</p>
                        <ul v-if="form.archivos.length" class="mt-2 space-y-1">
                            <li v-for="(f, i) in form.archivos" :key="i"
                                class="flex items-center justify-between text-xs text-gray-600 bg-gray-50 border border-gray-200 rounded-5px px-3 py-1.5">
                                <span class="truncate"><i class="fa-solid fa-file mr-1.5 text-gray-400"></i>{{ f.name }}</span>
                                <button type="button"
                                        class="ml-2 text-gray-400 hover:text-red-500 transition flex-shrink-0"
                                        @click="form.archivos = form.archivos.filter((_, j) => j !== i)">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </li>
                        </ul>
                    </div>

                </div>
            </div>

            <!-- ── Acciones ────────────────────────────────────────────── -->
            <div class="flex items-center gap-3">
                <ButtonBase type="submit" label="Crear Ticket" icon="fa-solid fa-paper-plane"
                            :disabled="form.processing"/>
                <button type="button" class="text-sm text-gray-500 hover:text-gray-700 transition"
                        @click="cancelar">
                    Cancelar
                </button>
            </div>

        </form>
    </AuthLayout>
</template>

