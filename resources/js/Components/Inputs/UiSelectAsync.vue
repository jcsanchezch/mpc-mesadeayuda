<script setup>
/**
 * UiSelectAsync — select con búsqueda asincrónica y debounce.
 *
 * Props:
 *   modelValue  — id del ítem seleccionado (v-model)
 *   label       — texto del label
 *   placeholder — placeholder del buscador
 *   url         — endpoint de búsqueda; se concatena ?q=<texto>
 *   minChars    — caracteres mínimos para disparar la búsqueda (default: 3)
 *   debounce    — ms de debounce (default: 300)
 *   error       — mensaje de error
 *   disabled    — deshabilita el componente
 *
 * Emits:
 *   update:modelValue — id del ítem seleccionado
 *   select            — objeto completo seleccionado (para extraer campos extra)
 *
 * Uso:
 *   <UiSelectAsync
 *       v-model="form.trabajador_id"
 *       label="Trabajador *"
 *       placeholder="DNI, Apellidos o Nombres"
 *       :url="route('mesadeservicio.trabajadores.buscar')"
 *       :min-chars="3"
 *       :error="ve('trabajador_id')"
 *       @select="onTrabajadorSelect"
 *   />
 *
 * El array de resultados del endpoint debe tener la forma:
 *   [ { id, label, ...camposExtra }, ... ]
 */
import { ref, watch } from 'vue'

const props = defineProps({
    label:       { type: String,  default: '' },
    placeholder: { type: String,  default: 'Buscar...' },
    url:         { type: String,  required: true },
    minChars:    { type: Number,  default: 3 },
    debounce:    { type: Number,  default: 300 },
    error:       { type: String,  default: '' },
    disabled:    { type: Boolean, default: false },
})

const emit = defineEmits(['select'])
const modelValue = defineModel({ default: null })

const searchText   = ref('')
const showDropdown = ref(false)
const loading      = ref(false)
const results      = ref([])
let timer          = null

// Si el valor se limpia externamente, limpia también el texto
watch(modelValue, (val) => {
    if (val === null || val === '' || val === undefined) searchText.value = ''
})

const onInput = async () => {
    // Si el texto ya no corresponde al ítem seleccionado, deselecciona
    if (modelValue.value !== null && modelValue.value !== '') {
        modelValue.value = null
        results.value    = []
    }

    clearTimeout(timer)
    const q = searchText.value.trim()

    if (q.length < props.minChars) {
        results.value      = []
        showDropdown.value = false
        return
    }

    showDropdown.value = true
    loading.value      = true

    timer = setTimeout(async () => {
        try {
            const res = await fetch(`${props.url}?q=${encodeURIComponent(q)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
            })
            results.value = await res.json()
        } finally {
            loading.value = false
        }
    }, props.debounce)
}

const select = (item) => {
    modelValue.value   = item.id
    searchText.value   = item.label
    showDropdown.value = false
    emit('select', item)
}

const hide = () => setTimeout(() => showDropdown.value = false, 150)
</script>

<template>
    <div>
        <label v-if="label" class="block text-sm font-medium text-gray-700">{{ label }}</label>
        <div class="relative mt-1">
            <input
                v-model="searchText"
                @input="onInput"
                @blur="hide"
                :disabled="disabled"
                :placeholder="placeholder"
                type="text"
                :class="[
                    'w-full border rounded-[4px] py-2.5 pl-3 pr-8 text-sm bg-white focus:outline-blue-500 transition-colors',
                    disabled ? 'bg-gray-50 border-gray-200 text-gray-400 cursor-not-allowed'
                             : 'border-gray-300 focus:border-blue-500',
                    error    ? '!border-red-400' : '',
                ]"
                autocomplete="off"
            />
            <!-- Ícono de estado -->
            <i v-if="loading"
               class="fa-solid fa-circle-notch fa-spin absolute right-2.5 top-1/2 -translate-y-1/2 text-blue-400 text-xs pointer-events-none"></i>
            <i v-else
               class="fa-solid fa-magnifying-glass absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>

            <!-- Dropdown de resultados -->
            <div v-if="showDropdown && !disabled"
                 class="absolute z-20 w-full bg-white border border-gray-200 rounded-[4px] shadow-lg mt-1 max-h-56 overflow-y-auto">
                <template v-if="results.length">
                    <button
                        v-for="item in results" :key="item.id"
                        type="button"
                        @mousedown="select(item)"
                        class="w-full text-left px-3 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                        {{ item.label }}
                    </button>
                </template>
                <div v-else-if="!loading"
                     class="px-3 py-3 text-sm text-gray-400 text-center">Sin resultados</div>
            </div>
        </div>
        <p v-if="error" class="mt-1 text-xs text-red-500">{{ error }}</p>
    </div>
</template>
