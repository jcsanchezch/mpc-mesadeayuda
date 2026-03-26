<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import {useForm, router} from '@inertiajs/vue3';
import {computed, ref, watch} from 'vue';
import {route} from 'ziggy-js';
import UiButton from "@/Components/Buttons/UiButton.vue";
import InputLabel from "@/Components/InputLabel.vue";

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
    form.post(route('mesadeservicio.tickets.clasificar', props.ticket.id));
};

const pendientes = () => router.visit(route('mesadeservicio.tickets.index'));

const selectClass = 'w-full border border-gray-300 rounded-[4px] py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-blue-400 focus:border-blue-400 bg-white';
const selectErrorClass = 'w-full border border-red-400 rounded-[4px] py-2 px-3 text-sm text-gray-700 focus:outline-none focus:ring-1 focus:ring-red-400 bg-white';
</script>

<template>
    <AuthLayout>
        <template #header>Mesa de Servicio — Clasificar Ticket {{ ticket.codigo }}</template>

        <form @submit.prevent="submit" class="max-w-4xl space-y-4">

            <!-- ── Botón volver ──────────────────────────────────────── -->
            <div>
                <UiButton
                    label="Regresar a Pendientes"
                    variant="outline"
                    color="gray"
                    size="md"
                    icon="fa-solid fa-arrow-left"
                    @click="pendientes"
                />
            </div>

            <!-- ── Datos del solicitante ─────────────────────────────── -->
            <div class="bg-white border border-gray-200 rounded-[4px] p-6 space-y-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Solicitante</p>
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">

                    <div class="sm:col-span-6">
                        <p class="text-xs text-gray-400 mb-1">Nombres</p>
                        <input type="text" readonly :value="`${ticket.dni ?? '—'} ${ticket.solicitante}`"
                               class="w-full border border-gray-200 bg-gray-50 rounded-[4px] py-2 px-3 text-sm text-gray-700"/>
                    </div>
                    <div class="sm:col-span-6">
                        <p class="text-xs text-gray-400 mb-1">Dependencia</p>
                        <input type="text" readonly :value="ticket.dependencia ?? '—'"
                               class="w-full border border-gray-200 bg-gray-50 rounded-[4px] py-2 px-3 text-sm text-gray-700"/>
                    </div>
                    <div class="sm:col-span-6">
                        <p class="text-xs text-gray-400 mb-1">Local</p>
                        <input type="text" readonly :value="ticket.local ?? '—'"
                               class="w-full border border-gray-200 bg-gray-50 rounded-[4px] py-2 px-3 text-sm text-gray-700"/>
                    </div>
                    <div class="sm:col-span-6">
                        <p class="text-xs text-gray-400 mb-1">Celular de contacto</p>
                        <input type="text" readonly :value="ticket.celular ?? '—'"
                               class="w-full border border-gray-200 bg-gray-50 rounded-[4px] py-2 px-3 text-sm text-gray-700"/>
                    </div>
                </div>
            </div>

            <!-- ── Datos del ticket ──────────────────────────────────── -->
            <div class="bg-white border border-gray-200 rounded-[4px] p-6 space-y-4">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Ticket</p>
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">

                    <div class="sm:col-span-3">
                        <p class="text-xs text-gray-400 mb-1">Código</p>
                        <input type="text" readonly :value="ticket.codigo"
                               class="w-full border border-gray-200 bg-gray-50 rounded-[4px] py-2 px-3 text-sm text-gray-700 font-medium"/>
                    </div>
                    <div class="sm:col-span-3">
                        <p class="text-xs text-gray-400 mb-1">Fecha de creación</p>
                        <input type="text" readonly :value="ticket.fecha"
                               class="w-full border border-gray-200 bg-gray-50 rounded-[4px] py-2 px-3 text-sm text-gray-700"/>
                    </div>
                    <div class="sm:col-span-3">
                        <p class="text-xs text-gray-400 mb-1">Canal</p>
                        <input type="text" readonly :value="ticket.canal ?? '—'"
                               class="w-full border border-gray-200 bg-gray-50 rounded-[4px] py-2 px-3 text-sm text-gray-700"/>
                    </div>
                    <div class="sm:col-span-3">
                        <p class="text-xs text-gray-400 mb-1">Estado</p>
                        <div class="py-2">
                            <span class="px-2 py-1 rounded-[4px] text-xs font-medium"
                                  :class="estadoClase(ticket.estado)">
                                {{ estadoLabel(ticket.estado) }}
                            </span>
                        </div>
                    </div>

                    <div class="sm:col-span-12">
                        <p class="text-xs text-gray-400 mb-1">Asunto</p>
                        <textarea readonly rows="2"
                                  class="w-full border border-gray-200 bg-gray-50 rounded-[4px] py-2 px-3 text-sm text-gray-700 resize-none"
                                  :value="ticket.asunto"></textarea>
                    </div>

                    <div class="sm:col-span-12">
                        <p class="text-xs text-gray-400 mb-1">Descripción</p>
                        <textarea readonly rows="4"
                                  class="w-full border border-gray-200 bg-gray-50 rounded-[4px] py-2 px-3 text-sm text-gray-700 resize-none"
                                  :value="ticket.descripcion"></textarea>
                    </div>
                    <div class="sm:col-span-12">

                        <p class="text-xs text-gray-400 mb-1">Adjuntos</p>

                        <div class="space-y-2">
                            <a v-for="arch in ticket.archivos" :key="arch.id"
                               :href="arch.ruta" target="_blank"
                               class="flex items-center gap-3 p-2.5 border border-gray-200 rounded-[4px] hover:border-blue-300 hover:bg-blue-50/40 transition group">
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


            <!-- ── Botones acción ─────────────────────────────────────── -->
            <div class="flex items-center justify-start gap-3">

                <UiButton
                    label="Clasificar y Asignar"
                    variant="principal"
                    color="blue"
                    size="md"
                    icon="fa-solid fa-check"
                    :disabled="form.processing"
                    @click="clasificar"
                />
            </div>

        </form>
    </AuthLayout>
</template>
