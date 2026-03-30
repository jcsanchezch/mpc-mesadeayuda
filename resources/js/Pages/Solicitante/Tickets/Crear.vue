<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue'
import InputLabel from '@/Components/InputLabel.vue'
import UiInputText from '@/Components/Inputs/UiInputText.vue'
import UiSelect from '@/Components/Inputs/UiSelect.vue'
import UiButton from '@/Components/Buttons/UiButton.vue'
import { ref, computed } from 'vue'
import { useForm, router } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const props = defineProps({
    categorias:   { type: Array,  default: () => [] },
    dependencias: { type: Array,  default: () => [] },
    locales:      { type: Array,  default: () => [] },
    solicitante:  { type: Object, default: () => ({}) },
})

const modo                  = ref('1')
const solicitudSeleccionada = ref(null)
const servicioDelaSolicitud = ref(null)
const searchSolicitud       = ref('')
const showDropdown          = ref(false)

const form = useForm({
    modo:           '1',
    solicitud_id:   '',
    dependencia_id: props.solicitante?.dependencia_id ?? '',
    local_id:       props.solicitante?.local_id ?? '',
    asunto:         '',
    celular:        props.solicitante?.celular ?? '',
    descripcion:    '',
    archivos:       [],
})

const filteredCategorias = computed(() => {
    const q = searchSolicitud.value.trim().toLowerCase()
    if (!q) return props.categorias
    return props.categorias
        .map(cat => ({
            ...cat,
            servicios: cat.servicios
                .map(srv => ({
                    ...srv,
                    solicitudes: srv.solicitudes.filter(s => s.nombre.toLowerCase().includes(q)),
                }))
                .filter(srv => srv.solicitudes.length > 0),
        }))
        .filter(cat => cat.servicios.length > 0)
})

const seleccionarModo = (m) => {
    modo.value                  = m
    form.modo                   = m
    form.solicitud_id           = ''
    form.asunto                 = ''
    form.descripcion            = ''
    form.archivos               = []
    solicitudSeleccionada.value = null
    servicioDelaSolicitud.value = null
    searchSolicitud.value       = ''
}

const selectSolicitud = (sol, srv) => {
    form.solicitud_id           = sol.id
    searchSolicitud.value       = sol.nombre
    showDropdown.value          = false
    solicitudSeleccionada.value = sol
    servicioDelaSolicitud.value = srv
    form.asunto                 = sol.nombre
}

const onSearchInput = () => {
    if (solicitudSeleccionada.value && searchSolicitud.value !== solicitudSeleccionada.value.nombre) {
        solicitudSeleccionada.value = null
        servicioDelaSolicitud.value = null
        form.solicitud_id           = ''
        form.asunto                 = ''
    }
    showDropdown.value = true
}

const hideDropdown = () => setTimeout(() => { showDropdown.value = false }, 150)

const cancelar = () => router.visit(route('solicitante.index'))

const submit = () => {
    form.post(route('solicitante.crear'), {
        forceFormData: true,
        onSuccess: () => router.visit(route('solicitante.index')),
    })
}
</script>

<template>
    <AuthLayout>
        <template #header>Nuevo Ticket</template>

        <div class="max-w-6xl space-y-5">

            <!-- Cabecera -->
            <div class="flex items-center gap-3">
                <button type="button" @click="cancelar"
                    class="text-gray-400 hover:text-gray-600 transition">
                    <i class="fa-solid fa-arrow-left"></i>
                </button>
                <h2 class="text-sm font-semibold text-gray-700">Nuevo Ticket</h2>
            </div>

            <!-- Switch de modo -->
            <div class="grid grid-cols-2 gap-3">
                <button type="button" @click="seleccionarModo('1')"
                    :class="['text-left p-4 border-2 rounded-[4px] transition',
                        modo === '1' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-white hover:border-blue-300 hover:bg-blue-50/40']">
                    <div class="flex items-start gap-3">
                        <div :class="['mt-0.5 w-4 h-4 rounded-full border-2 flex-shrink-0 flex items-center justify-center',
                            modo === '1' ? 'border-blue-500' : 'border-gray-300']">
                            <div v-if="modo === '1'" class="w-2 h-2 rounded-full bg-blue-500"></div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Sin Solicitud</p>
                            <p class="text-xs text-gray-400 mt-0.5">Describa libremente su solicitud</p>
                        </div>
                    </div>
                </button>
                <button type="button" @click="seleccionarModo('2')"
                    :class="['text-left p-4 border-2 rounded-[4px] transition',
                        modo === '2' ? 'border-blue-500 bg-blue-50' : 'border-gray-200 bg-white hover:border-blue-300 hover:bg-blue-50/40']">
                    <div class="flex items-start gap-3">
                        <div :class="['mt-0.5 w-4 h-4 rounded-full border-2 flex-shrink-0 flex items-center justify-center',
                            modo === '2' ? 'border-blue-500' : 'border-gray-300']">
                            <div v-if="modo === '2'" class="w-2 h-2 rounded-full bg-blue-500"></div>
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-gray-700">Con Solicitud</p>
                            <p class="text-xs text-gray-400 mt-0.5">Seleccione el tipo de solicitud que necesita</p>
                        </div>
                    </div>
                </button>
            </div>

            <!-- Formulario -->
            <form @submit.prevent="submit" class="space-y-4">

                <!-- ── Card: Datos del Solicitante ────────────────────── -->
                <div class="bg-white border border-gray-200 rounded-[4px] p-6 space-y-4">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Datos del Solicitante</h3>

                    <UiInputText
                        label="Solicitante"
                        :model-value="solicitante.nombre"
                        disabled
                    />

                    <div class="grid grid-cols-2 gap-3">
                        <UiSelect
                            v-model="form.dependencia_id"
                            label="Dependencia"
                            placeholder="Buscar dependencia..."
                            :options="dependencias"
                            :error="form.errors.dependencia_id"
                        />
                        <UiSelect
                            v-model="form.local_id"
                            label="Local"
                            placeholder="Buscar local..."
                            :options="locales"
                            :error="form.errors.local_id"
                        />
                    </div>

                    <UiInputText
                        v-model="form.celular"
                        label="Celular de contacto"
                        placeholder="Ej. 987654321"
                        maxlength="15"
                        :error="form.errors.celular"
                    />
                </div>

                <!-- ── Card: Selector de solicitud (solo modo 2) ────── -->
                <template v-if="modo === '2'">
                    <div class="bg-white border border-gray-200 rounded-[4px] p-6 space-y-4">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Solicitud</h3>

                        <div class="relative">
                            <InputLabel value="Solicitud *" />
                            <div class="relative mt-1">
                                <input
                                    v-model="searchSolicitud"
                                    @input="onSearchInput"
                                    @focus="showDropdown = true"
                                    @blur="hideDropdown"
                                    type="text"
                                    placeholder="Buscar solicitud..."
                                    class="w-full border border-gray-300 focus:border-blue-500 rounded-[4px] py-2.5 pl-2.5 pr-8 bg-white text-sm focus:outline-blue-500"
                                    :class="{ '!border-red-400': form.errors.solicitud_id }"
                                    autocomplete="off"
                                />
                                <i class="fa-solid fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>
                                <div v-if="showDropdown"
                                    class="absolute z-20 w-full bg-white border border-gray-200 rounded-[4px] shadow-lg mt-1 max-h-64 overflow-y-auto">
                                    <template v-if="filteredCategorias.length">
                                        <template v-for="cat in filteredCategorias" :key="cat.id">
                                            <!-- Cabecera de categoría -->
                                            <div class="px-3 py-1.5 text-xs font-bold text-gray-500 uppercase bg-gray-100 sticky top-0">
                                                {{ cat.nombre }}
                                            </div>
                                            <template v-for="srv in cat.servicios" :key="srv.id">
                                                <!-- Cabecera de servicio -->
                                                <div class="px-4 py-1 text-xs font-semibold text-gray-400 bg-gray-50">
                                                    {{ srv.nombre }}
                                                </div>
                                                <!-- Solicitudes -->
                                                <button v-for="sol in srv.solicitudes" :key="sol.id"
                                                    type="button"
                                                    @mousedown="selectSolicitud(sol, srv)"
                                                    class="w-full text-left px-6 py-2 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                                                    {{ sol.nombre }}
                                                </button>
                                            </template>
                                        </template>
                                    </template>
                                    <div v-else class="px-4 py-3 text-sm text-gray-400 text-center">Sin coincidencias</div>
                                </div>
                            </div>
                            <p v-if="form.errors.solicitud_id" class="mt-1 text-xs text-red-500">{{ form.errors.solicitud_id }}</p>
                        </div>

                        <!-- Descripción de la solicitud seleccionada -->
                        <div v-if="solicitudSeleccionada?.descripcion"
                            class="bg-blue-50 border border-blue-100 rounded-[4px] px-4 py-3 text-sm text-blue-700 leading-relaxed">
                            {{ solicitudSeleccionada.descripcion }}
                        </div>

                        <!-- Formatos del servicio padre -->
                        <div v-if="servicioDelaSolicitud?.formatos?.length">
                            <p class="text-xs font-semibold text-gray-600 mb-1.5">
                                <i class="fa-solid fa-file-lines mr-1"></i>Formatos requeridos
                            </p>
                            <div class="space-y-1">
                                <a v-for="fmt in servicioDelaSolicitud.formatos" :key="fmt.id"
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

                <!-- ── Card: Datos del Ticket ──────────────────────── -->
                <div v-if="modo === '1' || solicitudSeleccionada"
                    class="bg-white border border-gray-200 rounded-[4px] p-6 space-y-5">
                    <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">Datos del Ticket</h3>

                    <div>
                        <UiInputText v-if="modo === '1'"
                            v-model="form.asunto"
                            label="Asunto *"
                            placeholder="Describa brevemente el motivo del ticket"
                            maxlength="500"
                            :error="form.errors.asunto"
                        />
                        <div v-else>
                            <InputLabel value="Asunto" />
                            <input :value="form.asunto" readonly
                                class="mt-1 w-full border border-gray-200 bg-gray-50 text-gray-600 rounded-[4px] py-2.5 px-2.5 text-sm cursor-default" />
                        </div>
                    </div>

                    <div>
                        <InputLabel value="Descripción" />
                        <textarea v-model="form.descripcion" rows="5"
                            placeholder="Detalle el problema o solicitud (opcional)"
                            class="mt-1 w-full border border-gray-300 focus:border-blue-500 rounded-[4px] py-2.5 px-2.5 text-sm focus:outline-blue-500 resize-none"
                            :class="{ 'border-red-400': form.errors.descripcion }"></textarea>
                        <p v-if="form.errors.descripcion" class="mt-1 text-xs text-red-500">{{ form.errors.descripcion }}</p>
                    </div>

                    <div>
                        <InputLabel value="Archivos adjuntos" />
                        <label class="mt-1 flex flex-col items-center justify-center w-full border-2 border-dashed border-gray-300 rounded-[4px] py-6 px-4 cursor-pointer hover:border-blue-400 hover:bg-blue-50/30 transition">
                            <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-400 mb-2"></i>
                            <span class="text-sm text-gray-500">Arrastra archivos aquí o <span class="text-blue-500 underline">selecciona</span></span>
                            <span class="text-xs text-gray-400 mt-1">PDF, Word, Excel, imágenes — máx. 10 MB por archivo</span>
                            <input type="file" multiple class="hidden"
                                @change="(e) => form.archivos = Array.from(e.target.files)" />
                        </label>
                        <p v-if="form.errors.archivos" class="mt-1 text-xs text-red-500">{{ form.errors.archivos }}</p>
                        <ul v-if="form.archivos.length" class="mt-2 space-y-1">
                            <li v-for="(f, i) in form.archivos" :key="i"
                                class="flex items-center justify-between text-xs text-gray-600 bg-gray-50 border border-gray-200 rounded-[4px] px-3 py-1.5">
                                <span class="truncate"><i class="fa-solid fa-file mr-1.5 text-gray-400"></i>{{ f.name }}</span>
                                <button type="button" class="ml-2 text-gray-400 hover:text-red-500 transition flex-shrink-0"
                                    @click="form.archivos = form.archivos.filter((_, j) => j !== i)">
                                    <i class="fa-solid fa-xmark"></i>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Acciones -->
                <div class="flex items-center gap-3 pt-1">
                    <UiButton
                        type="submit"
                        label="Enviar Ticket"
                        icon="fa-solid fa-paper-plane"
                        :disabled="form.processing"
                        size="md"
                    />
                    <button type="button" class="text-sm text-gray-500 hover:text-gray-700 transition"
                        @click="cancelar">
                        Cancelar
                    </button>
                </div>

            </form>
        </div>
    </AuthLayout>
</template>
