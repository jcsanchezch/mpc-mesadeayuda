<script setup>
/**
 * UiSelect — select sincrónico con búsqueda integrada.
 *
 * Props:
 *   modelValue  — id del ítem seleccionado (v-model)
 *   label       — texto del label
 *   placeholder — placeholder del buscador
 *   options     — array de objetos [ { id, nombre, ... } ]
 *   labelKey    — clave de texto a mostrar (default: 'nombre')
 *   valueKey    — clave del valor a emitir  (default: 'id')
 *   error       — mensaje de error
 *   disabled    — deshabilita el componente
 *
 * Uso:
 *   <UiSelect v-model="form.dependencia_id" label="Dependencia"
 *             :options="dependencias" :error="ve('dependencia_id')" />
 */
import { ref, computed, watch } from 'vue'

const props = defineProps({
    label:       { type: String,  default: '' },
    placeholder: { type: String,  default: 'Buscar...' },
    options:     { type: Array,   default: () => [] },
    labelKey:    { type: String,  default: 'nombre' },
    valueKey:    { type: String,  default: 'id' },
    error:       { type: String,  default: '' },
    disabled:    { type: Boolean, default: false },
})

const emit = defineEmits(['update:modelValue'])
const modelValue = defineModel({ default: null })

const searchText    = ref('')
const showDropdown  = ref(false)

// Sincroniza el texto cuando el valor cambia externamente
watch(modelValue, (val) => {
    if (val === null || val === '' || val === undefined) {
        searchText.value = ''
        return
    }
    const found = props.options.find(o => o[props.valueKey] == val)
    if (found) searchText.value = getLabel(found)
}, { immediate: true })

const getLabel = (opt) => opt[props.labelKey] ?? ''

const filtered = computed(() => {
    const q = searchText.value.trim().toLowerCase()
    if (!q) return props.options
    return props.options.filter(o => getLabel(o).toLowerCase().includes(q))
})

const onInput = () => {
    const current = props.options.find(o => o[props.valueKey] == modelValue.value)
    if (!current || searchText.value !== getLabel(current)) {
        modelValue.value = null
    }
    showDropdown.value = true
}

const select = (opt) => {
    modelValue.value   = opt[props.valueKey]
    searchText.value   = getLabel(opt)
    showDropdown.value = false
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
                @focus="!disabled && (showDropdown = true)"
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
            <i class="fa-solid fa-chevron-down absolute right-2.5 top-1/2 -translate-y-1/2 text-gray-400 text-xs pointer-events-none"></i>

            <div v-if="showDropdown && !disabled"
                 class="absolute z-20 w-full bg-white border border-gray-200 rounded-[4px] shadow-lg mt-1 max-h-56 overflow-y-auto">
                <button
                    v-for="opt in filtered" :key="opt[valueKey]"
                    type="button"
                    @mousedown="select(opt)"
                    class="w-full text-left px-3 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-colors">
                    {{ getLabel(opt) }}
                </button>
                <div v-if="!filtered.length"
                     class="px-3 py-3 text-sm text-gray-400 text-center">Sin coincidencias</div>
            </div>
        </div>
        <p v-if="error" class="mt-1 text-xs text-red-500">{{ error }}</p>
    </div>
</template>
