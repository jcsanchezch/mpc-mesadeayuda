<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import {useForm, router} from '@inertiajs/vue3';
import {computed, ref, watch} from 'vue';
import {route} from 'ziggy-js';

const props = defineProps({
    ticket:        {type: Object, required: true},
    estados:       {type: Array,  default: () => []},
    tipos:         {type: Array,  default: () => []},
    servicios:     {type: Array,  default: () => []},
    prioridades:   {type: Array,  default: () => []},
    especialistas: {type: Array,  default: () => []},
});

const estadoMap = computed(() =>
    Object.fromEntries(props.estados.map(e => [e.codigo, e]))
);
const estadoLabel = (codigo) => estadoMap.value[codigo]?.label ?? codigo;
const estadoClase = (codigo) => {
    const e = estadoMap.value[codigo];
    return `${e?.text_color ?? 'text-gray-500'} ${e?.bg_color ?? 'bg-gray-100'}`;
};

// ── Tipo → filtra servicios ───────────────────────────────────
const tipoSeleccionado = ref(null);

const serviciosFiltrados = computed(() => {
    if (!tipoSeleccionado.value) return props.servicios;
    return props.servicios.filter(s => s.tipo_id === tipoSeleccionado.value);
});

watch(tipoSeleccionado, () => {
    const existe = serviciosFiltrados.value.find(s => s.id === form.servicio_id);
    if (!existe) form.servicio_id = null;
});

// ── Formulario ────────────────────────────────────────────────
const form = useForm({
    servicio_id:     props.ticket.servicio_id ?? null,
    prioridad_id:    props.ticket.prioridad_id ?? null,
    especialista_id: null,
});

const submit = () => {
    form.post(route('mesadeayuda.tickets.clasificar', props.ticket.id));
};

const volver = () => router.visit(route('mesadeayuda.tickets.ver', props.ticket.id));

const selectClass = 'w-full border border-gray-300 rounded-5px py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-400 focus:border-blue-400 bg-white';
const selectErrorClass = 'w-full border border-red-400 rounded-5px py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-red-400 bg-white';
</script>

<template>
    <AuthLayout>
        <template #header>Mesa de Servicio — Clasificar Ticket {{ ticket.codigo }}</template>

        <form @submit.prevent="submit" class="max-w-4xl space-y-4">

            <!-- ── Botón volver ──────────────────────────────────────── -->
            <div>
                <button type="button" @click="volver"
                        class="inline-flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-700 transition">
                    <i class="fa-solid fa-arrow-left text-xs"></i> Volver al ticket
                </button>
            </div>

            <!-- ── Datos del solicitante ─────────────────────────────── -->
            <div class="bg-white border border-gray-200 rounded-5px p-6 space-y-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Solicitante</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <p class="text-xs text-gray-400 mb-1">DNI</p>
                        <input type="text" readonly :value="ticket.dni ?? '—'"
                               class="w-full border border-gray-200 bg-gray-50 rounded-5px py-2 px-3 text-sm text-gray-700 font-mono"/>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400 mb-1">Solicitante</p>
                        <input type="text" readonly :value="ticket.solicitante"
                               class="w-full border border-gray-200 bg-gray-50 rounded-5px py-2 px-3 text-sm text-gray-700"/>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400 mb-1">Dependencia</p>
                        <input type="text" readonly :value="ticket.dependencia ?? '—'"
                               class="w-full border border-gray-200 bg-gray-50 rounded-5px py-2 px-3 text-sm text-gray-700"/>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400 mb-1">Local</p>
                        <input type="text" readonly :value="ticket.local ?? '—'"
                               class="w-full border border-gray-200 bg-gray-50 rounded-5px py-2 px-3 text-sm text-gray-700"/>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400 mb-1">Celular de contacto</p>
                        <input type="text" readonly :value="ticket.celular ?? '—'"
                               class="w-full border border-gray-200 bg-gray-50 rounded-5px py-2 px-3 text-sm text-gray-700 font-mono"/>
                    </div>

                </div>
            </div>

            <!-- ── Datos del ticket ──────────────────────────────────── -->
            <div class="bg-white border border-gray-200 rounded-5px p-6 space-y-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Ticket</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <div>
                        <p class="text-xs text-gray-400 mb-1">Código</p>
                        <input type="text" readonly :value="ticket.codigo"
                               class="w-full border border-gray-200 bg-gray-50 rounded-5px py-2 px-3 text-sm text-gray-700 font-medium"/>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400 mb-1">Fecha de creación</p>
                        <input type="text" readonly :value="ticket.fecha"
                               class="w-full border border-gray-200 bg-gray-50 rounded-5px py-2 px-3 text-sm text-gray-700"/>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400 mb-1">Estado</p>
                        <div class="py-2 px-3">
                            <span class="px-2 py-1 rounded-5px text-xs font-medium"
                                  :class="estadoClase(ticket.estado)">
                                {{ estadoLabel(ticket.estado) }}
                            </span>
                        </div>
                    </div>

                    <div>
                        <p class="text-xs text-gray-400 mb-1">Canal</p>
                        <input type="text" readonly :value="ticket.canal ?? '—'"
                               class="w-full border border-gray-200 bg-gray-50 rounded-5px py-2 px-3 text-sm text-gray-700"/>
                    </div>

                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-400 mb-1">Asunto</p>
                        <input type="text" readonly :value="ticket.asunto"
                               class="w-full border border-gray-200 bg-gray-50 rounded-5px py-2 px-3 text-sm text-gray-700"/>
                    </div>

                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-400 mb-1">Descripción</p>
                        <textarea readonly rows="4"
                                  class="w-full border border-gray-200 bg-gray-50 rounded-5px py-2 px-3 text-sm text-gray-700 resize-none"
                                  :value="ticket.descripcion"></textarea>
                    </div>

                </div>
            </div>

            <!-- ── Archivos adjuntos ─────────────────────────────────── -->
            <div v-if="ticket.archivos?.length" class="bg-white border border-gray-200 rounded-5px p-6 space-y-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <i class="fa-solid fa-paperclip mr-1"></i>Archivos adjuntos
                </p>
                <div class="space-y-2">
                    <a v-for="arch in ticket.archivos" :key="arch.id"
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

            <!-- ── Clasificación ─────────────────────────────────────── -->
            <div class="bg-white border border-blue-200 rounded-5px p-6 space-y-4">
                <p class="text-xs font-semibold text-blue-600 uppercase tracking-wider">
                    <i class="fa-solid fa-tags mr-1"></i>Clasificación
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

                    <!-- Tipo (filtro, no se guarda) -->
                    <div>
                        <p class="text-xs text-gray-500 mb-1 font-medium">Tipo</p>
                        <select v-model="tipoSeleccionado" :class="selectClass">
                            <option :value="null">— Todos los tipos —</option>
                            <option v-for="tipo in tipos" :key="tipo.id" :value="tipo.id">
                                {{ tipo.label }}
                            </option>
                        </select>
                    </div>

                    <!-- Servicio -->
                    <div>
                        <p class="text-xs text-gray-500 mb-1 font-medium">
                            Servicio <span class="text-red-500">*</span>
                        </p>
                        <select v-model="form.servicio_id"
                                :class="form.errors.servicio_id ? selectErrorClass : selectClass">
                            <option :value="null">— Seleccione un servicio —</option>
                            <option v-for="s in serviciosFiltrados" :key="s.id" :value="s.id">
                                {{ s.nombre }}
                                <template v-if="s.categoria"> — {{ s.categoria }}</template>
                            </option>
                        </select>
                        <p v-if="form.errors.servicio_id" class="text-xs text-red-500 mt-1">
                            {{ form.errors.servicio_id }}
                        </p>
                    </div>

                    <!-- Prioridad -->
                    <div>
                        <p class="text-xs text-gray-500 mb-1 font-medium">
                            Prioridad <span class="text-red-500">*</span>
                        </p>
                        <select v-model="form.prioridad_id"
                                :class="form.errors.prioridad_id ? selectErrorClass : selectClass">
                            <option :value="null">— Seleccione una prioridad —</option>
                            <option v-for="p in prioridades" :key="p.id" :value="p.id">
                                {{ p.label }}
                            </option>
                        </select>
                        <p v-if="form.errors.prioridad_id" class="text-xs text-red-500 mt-1">
                            {{ form.errors.prioridad_id }}
                        </p>
                    </div>

                    <!-- Especialista -->
                    <div>
                        <p class="text-xs text-gray-500 mb-1 font-medium">
                            Especialista <span class="text-red-500">*</span>
                        </p>
                        <select v-model="form.especialista_id"
                                :class="form.errors.especialista_id ? selectErrorClass : selectClass">
                            <option :value="null">— Seleccione un especialista —</option>
                            <option v-for="e in especialistas" :key="e.id" :value="e.id">
                                {{ e.label }}
                            </option>
                        </select>
                        <p v-if="form.errors.especialista_id" class="text-xs text-red-500 mt-1">
                            {{ form.errors.especialista_id }}
                        </p>
                    </div>

                </div>
            </div>

            <!-- ── Botones acción ─────────────────────────────────────── -->
            <div class="flex items-center justify-end gap-3">
                <button type="button" @click="volver"
                        class="px-4 py-2 text-sm text-gray-600 border border-gray-300 rounded-5px hover:bg-gray-50 transition">
                    Cancelar
                </button>
                <button type="submit" :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-5 py-2 text-sm font-medium bg-blue-600 border-b-2 border-b-blue-700 text-white rounded-5px hover:bg-blue-700 disabled:opacity-50 transition">
                    <i class="fa-solid fa-check"></i>
                    Clasificar y Asignar
                </button>
            </div>

        </form>
    </AuthLayout>
</template>
