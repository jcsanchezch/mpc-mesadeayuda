<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import UiButton from '@/Components/Buttons/UiButton.vue'
import { router } from '@inertiajs/vue3'
import { computed } from 'vue'
import { route } from 'ziggy-js'

const props = defineProps({
    ticket:     { type: Object, required: true },
    estados:    { type: Array,  default: () => [] },
    prioridades: { type: Array, default: () => [] },
})

const estadoMap = computed(() =>
    Object.fromEntries(props.estados.map(e => [e.codigo, e]))
)
const estadoLabel = (codigo) => estadoMap.value[codigo]?.label ?? codigo
const estadoClase = (codigo) => {
    const e = estadoMap.value[codigo]
    return `${e?.text_color ?? 'text-gray-500'} ${e?.bg_color ?? 'bg-gray-100'}`
}

const prioridadMap = computed(() =>
    Object.fromEntries(props.prioridades.map(p => [p.codigo, p]))
)
const prioridadLabel = (codigo) => prioridadMap.value[codigo]?.label ?? codigo
const prioridadClase = (codigo) => {
    const p = prioridadMap.value[codigo]
    return `${p?.text_color ?? 'text-gray-500'} ${p?.bg_color ?? 'bg-gray-100'}`
}
</script>

<template>
    <AuthLayout>
        <template #header>Mis Tickets — {{ ticket.codigo }}</template>

        <div class="max-w-6xl space-y-4">

            <!-- Volver -->
            <div class="flex items-center justify-between">
                <UiButton
                    label="Regresar a mis tickets"
                    variant="outline"
                    color="gray"
                    size="md"
                    icon="fa-solid fa-arrow-left"
                    @click="router.visit(route('solicitante.index'))"
                />
            </div>

            <!-- Ticket -->
            <div class="bg-white border border-gray-200 rounded-[4px] p-6 space-y-4">

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">

                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-400 mb-1">Código</p>
                        <span class="px-2 py-1 rounded-[4px] text-sm font-medium bg-gray-200 text-gray-700">
                            {{ ticket.codigo }}
                        </span>
                    </div>

                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-400 mb-1">Estado</p>
                        <span class="px-2 py-1 rounded-[4px] text-sm font-medium"
                            :class="estadoClase(ticket.estado)">
                            {{ estadoLabel(ticket.estado) }}
                        </span>
                    </div>

                    <div class="sm:col-span-2">
                        <p class="text-xs text-gray-400 mb-1">Prioridad</p>
                        <span v-if="ticket.prioridad" class="px-2 py-1 rounded-[4px] text-sm font-medium"
                            :class="prioridadClase(ticket.prioridad)">
                            {{ prioridadLabel(ticket.prioridad) }}
                        </span>
                        <span v-else class="text-gray-400 text-sm">—</span>
                    </div>

                    <div class="sm:col-span-4">
                        <p class="text-xs text-gray-400 mb-1">Fecha de creación</p>
                        <span class="px-2 py-1 rounded-[4px] text-sm font-medium bg-gray-200 text-gray-700">
                            {{ ticket.fecha }}
                        </span>
                    </div>

                </div>

                <div class="h-4 border-b border-gray-200"></div>

                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Solicitante</p>
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">

                    <div class="sm:col-span-6">
                        <p class="text-xs text-gray-400 mb-1">Solicitante</p>
                        <input type="text" readonly :value="`${ticket.dni ?? '—'} ${ticket.solicitante}`"
                            class="w-full border border-gray-200 bg-gray-50 rounded-[4px] px-2 py-1.5 text-sm text-gray-700"/>
                    </div>

                    <div class="sm:col-span-6">
                        <p class="text-xs text-gray-400 mb-1">Dependencia</p>
                        <input type="text" readonly :value="ticket.dependencia ?? '—'"
                            class="w-full border border-gray-200 bg-gray-50 rounded-[4px] px-2 py-1.5 text-sm text-gray-700"/>
                    </div>

                    <div class="sm:col-span-6">
                        <p class="text-xs text-gray-400 mb-1">Local</p>
                        <input type="text" readonly :value="ticket.local ?? '—'"
                            class="w-full border border-gray-200 bg-gray-50 rounded-[4px] px-2 py-1.5 text-sm text-gray-700"/>
                    </div>

                    <div class="sm:col-span-6">
                        <p class="text-xs text-gray-400 mb-1">Celular de contacto</p>
                        <input type="text" readonly :value="ticket.celular ?? '—'"
                            class="w-full border border-gray-200 bg-gray-50 rounded-[4px] px-2 py-1.5 text-sm text-gray-700"/>
                    </div>

                </div>

                <div class="h-4 border-b border-gray-200"></div>

                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Ticket</p>
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">

                    <div class="sm:col-span-12">
                        <p class="text-xs text-gray-400 mb-1">Asunto</p>
                        <input type="text" readonly :value="ticket.asunto"
                            class="w-full border border-gray-200 bg-gray-50 rounded-[4px] px-2 py-1.5 text-sm text-gray-700"/>
                    </div>

                    <div class="sm:col-span-12">
                        <p class="text-xs text-gray-400 mb-1">Descripción</p>
                        <textarea readonly rows="3"
                            class="w-full border border-gray-200 bg-gray-50 rounded-[4px] px-2 py-1.5 text-sm text-gray-700 resize-none"
                            :value="ticket.descripcion ?? '—'"></textarea>
                    </div>

                    <div v-if="ticket.resolucion" class="sm:col-span-12">
                        <p class="text-xs text-gray-400 mb-1">Resolución</p>
                        <textarea readonly rows="3"
                            class="w-full border border-gray-200 bg-emerald-50 rounded-[4px] px-2 py-1.5 text-sm text-gray-700 resize-none"
                            :value="ticket.resolucion"></textarea>
                    </div>

                    <div class="sm:col-span-12">
                        <p class="text-xs text-gray-400 mb-1">Archivos adjuntos</p>
                        <div v-if="ticket.archivos?.length" class="space-y-1">
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
                        <div v-else class="bg-white rounded-[4px]">
                            <div class="px-4">
                                <span class="text-xs text-gray-400">Sin Archivos</span>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="h-4 border-b border-gray-200"></div>

                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Servicio</p>
                <div class="grid grid-cols-1 sm:grid-cols-12 gap-4">

                    <div class="sm:col-span-12">
                        <p class="text-xs text-gray-400 mb-1">Categoría</p>
                        <input type="text" readonly :value="ticket.categoria ?? '—'"
                            class="w-full border border-gray-200 bg-gray-50 rounded-[4px] px-2 py-1.5 text-sm text-gray-700"/>
                    </div>

                    <div class="sm:col-span-12">
                        <p class="text-xs text-gray-400 mb-1">Servicio</p>
                        <textarea readonly rows="2"
                            class="w-full border border-gray-200 bg-gray-50 rounded-[4px] px-2 py-1.5 text-sm text-gray-700 resize-none"
                            :value="ticket.servicio ?? '—'"></textarea>
                    </div>

                </div>
            </div>

            <!-- Historial -->
            <div class="bg-white border border-gray-200 rounded-[4px] p-6 space-y-3">
                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    <i class="fa-solid fa-clock-rotate-left mr-1"></i>Historial
                </p>

                <div v-if="!ticket.historial?.length" class="text-center text-gray-400 py-6 text-sm">
                    Sin movimientos registrados
                </div>

                <div v-else class="relative">
                    <div class="absolute left-3.5 top-2 bottom-2 w-px bg-gray-200"></div>
                    <div v-for="h in ticket.historial" :key="h.id" class="relative flex gap-4 pb-5">
                        <div :class="['relative z-10 flex-shrink-0 w-7 h-7 rounded-full border-2 flex items-center justify-center text-xs',
                            h.es_conformidad
                                ? 'border-emerald-400 bg-emerald-50 text-emerald-500'
                                : 'border-blue-300 bg-blue-50 text-blue-400']">
                            <i :class="h.es_conformidad ? 'fa-solid fa-check' : 'fa-solid fa-arrow-right'"></i>
                        </div>
                        <div class="flex-1 pt-0.5">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span v-if="h.estado_anterior" class="text-xs text-gray-400">
                                    {{ estadoLabel(h.estado_anterior) }}
                                </span>
                                <i v-if="h.estado_anterior" class="fa-solid fa-arrow-right text-gray-300 text-xs"></i>
                                <span class="text-xs font-semibold px-2 py-0.5 rounded-[4px]"
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
    </AuthLayout>
</template>
