<script setup>
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';

const props = defineProps({
    modelValue: { type: [Number, String], default: null },
    options:    { type: Array, default: () => [] },
    placeholder:{ type: String, default: '— Seleccionar —' },
    hasError:   { type: Boolean, default: false },
});

const emit = defineEmits(['update:modelValue']);

const query        = ref('');
const open         = ref(false);
const inputRef     = ref(null);
const containerRef = ref(null);

// Texto mostrado en el input cuando hay una selección activa
const selectedLabel = computed(() => {
    if (!props.modelValue && props.modelValue !== 0) return '';
    return props.options.find(o => o.id === props.modelValue)?.nombre ?? '';
});

// Sincroniza el texto del input con la selección actual
watch(() => props.modelValue, () => {
    if (!open.value) query.value = selectedLabel.value;
}, { immediate: true });

const filtered = computed(() => {
    const q = query.value.toLowerCase().trim();
    if (!q) return props.options;
    return props.options.filter(o => o.nombre.toLowerCase().includes(q));
});

function onFocus() {
    query.value = '';
    open.value  = true;
}

function select(option) {
    emit('update:modelValue', option.id);
    query.value = option.nombre;
    open.value  = false;
}

function clear() {
    emit('update:modelValue', null);
    query.value = '';
    open.value  = false;
    inputRef.value?.focus();
}

function onClickOutside(e) {
    if (containerRef.value && !containerRef.value.contains(e.target)) {
        open.value  = false;
        query.value = selectedLabel.value;
    }
}

onMounted(() => document.addEventListener('mousedown', onClickOutside));
onUnmounted(() => document.removeEventListener('mousedown', onClickOutside));
</script>

<template>
    <div class="relative" ref="containerRef">
        <div class="relative mt-1">
            <input
                ref="inputRef"
                type="text"
                v-model="query"
                :placeholder="selectedLabel || placeholder"
                @focus="onFocus"
                autocomplete="off"
                class="block w-full border rounded-[4px] py-2.5 pl-2.5 pr-8 text-sm bg-white focus:outline-blue-500"
                :class="hasError ? 'border-red-400' : 'border-gray-300 focus:border-blue-500'"
            />
            <!-- Botón limpiar -->
            <button
                v-if="modelValue"
                type="button"
                @mousedown.prevent="clear"
                class="absolute inset-y-0 right-0 flex items-center px-2.5 text-gray-400 hover:text-gray-600"
                tabindex="-1">
                <i class="fa-solid fa-xmark text-xs"></i>
            </button>
            <!-- Chevron cuando no hay selección -->
            <span v-else class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-gray-400">
                <i class="fa-solid fa-chevron-down text-xs"></i>
            </span>
        </div>

        <!-- Dropdown -->
        <ul v-if="open"
            class="absolute z-50 mt-1 w-full bg-white border border-gray-200 rounded-[4px] shadow-md max-h-56 overflow-y-auto text-sm">
            <li
                v-for="option in filtered"
                :key="option.id"
                @mousedown.prevent="select(option)"
                class="px-3 py-2 cursor-pointer hover:bg-blue-50 hover:text-blue-700"
                :class="{ 'bg-blue-50 text-blue-700 font-medium': option.id === modelValue }">
                {{ option.nombre }}
            </li>
            <li v-if="filtered.length === 0" class="px-3 py-2 text-gray-400 italic">
                Sin resultados
            </li>
        </ul>
    </div>
</template>
