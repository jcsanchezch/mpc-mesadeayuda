<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const MAX_FILES  = 10;
const MAX_MB     = 5;
const MAX_TITULO = 200;

const form = useForm({
    titulo:      '',
    descripcion: '',
    adjuntos:    [],
});

// -----------------------------------------------------------------------
// Archivos adjuntos
// -----------------------------------------------------------------------
const archivos     = ref([]);   // File[] seleccionados
const dropOver     = ref(false);
const fileInput    = ref(null);
const errorArchivo = ref('');

function agregarArchivos(lista) {
    errorArchivo.value = '';
    const combined = [...archivos.value, ...Array.from(lista)];

    if (combined.length > MAX_FILES) {
        errorArchivo.value = `Máximo ${MAX_FILES} archivos permitidos.`;
        return;
    }

    const sobrePeso = Array.from(lista).find(f => f.size > MAX_MB * 1024 * 1024);
    if (sobrePeso) {
        errorArchivo.value = `"${sobrePeso.name}" supera los ${MAX_MB} MB permitidos.`;
        return;
    }

    archivos.value   = combined;
    form.adjuntos    = combined;
}

function onFileInput(e) {
    agregarArchivos(e.target.files);
    e.target.value = '';
}

function onDrop(e) {
    dropOver.value = false;
    agregarArchivos(e.dataTransfer.files);
}

function quitarArchivo(idx) {
    archivos.value.splice(idx, 1);
    form.adjuntos = [...archivos.value];
}

function formatSize(bytes) {
    if (bytes < 1024)        return bytes + ' B';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
}

// -----------------------------------------------------------------------
// Submit
// -----------------------------------------------------------------------
function submit() {
    form.post(route('mis-tickets.store'), {
        forceFormData: true,
    });
}

const tituloRestante = computed(() => MAX_TITULO - form.titulo.length);
</script>

<template>
    <AppLayout title="Nuevo ticket">
        <main class="p-8">

            <!-- Encabezado -->
            <header class="mb-6 flex items-center gap-3">
                <Link
                    :href="route('mis-tickets.index')"
                    class="flex h-8 w-8 items-center justify-center rounded-lg border border-stone-200 bg-white text-stone-400 shadow-sm transition hover:bg-stone-50 hover:text-stone-600"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                    </svg>
                </Link>
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.3em] text-teal-600">Mis Tickets</p>
                    <h1 class="mt-0.5 text-2xl font-semibold tracking-tight text-stone-900">Registrar solicitud</h1>
                </div>
            </header>

            <!-- Formulario -->
            <form class="max-w-2xl space-y-6" @submit.prevent="submit">

                <!-- Título -->
                <div>
                    <div class="mb-1.5 flex items-center justify-between">
                        <label for="titulo" class="block text-sm font-medium text-stone-700">
                            Título <span class="text-red-500">*</span>
                        </label>
                        <span
                            class="text-xs"
                            :class="tituloRestante < 20 ? 'text-red-500' : 'text-stone-400'"
                        >{{ tituloRestante }} restantes</span>
                    </div>
                    <input
                        id="titulo"
                        v-model="form.titulo"
                        type="text"
                        :maxlength="MAX_TITULO"
                        placeholder="Describe brevemente el problema o solicitud"
                        class="w-full rounded-lg border border-stone-300 bg-white px-4 py-2.5 text-sm text-stone-900 outline-none transition placeholder:text-stone-400 focus:border-teal-500 focus:ring-3 focus:ring-teal-100"
                        :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-100': form.errors.titulo }"
                    >
                    <p v-if="form.errors.titulo" class="mt-1.5 text-xs text-red-600">{{ form.errors.titulo }}</p>
                </div>

                <!-- Descripción -->
                <div>
                    <label for="descripcion" class="mb-1.5 block text-sm font-medium text-stone-700">
                        Descripción <span class="text-red-500">*</span>
                    </label>
                    <textarea
                        id="descripcion"
                        v-model="form.descripcion"
                        rows="6"
                        placeholder="Explica en detalle el problema o lo que necesitas. Incluye pasos para reproducirlo si aplica."
                        class="w-full resize-y rounded-lg border border-stone-300 bg-white px-4 py-2.5 text-sm text-stone-900 outline-none transition placeholder:text-stone-400 focus:border-teal-500 focus:ring-3 focus:ring-teal-100"
                        :class="{ 'border-red-400 focus:border-red-400 focus:ring-red-100': form.errors.descripcion }"
                    />
                    <p v-if="form.errors.descripcion" class="mt-1.5 text-xs text-red-600">{{ form.errors.descripcion }}</p>
                </div>

                <!-- Adjuntos -->
                <div>
                    <label class="mb-1.5 block text-sm font-medium text-stone-700">
                        Archivos adjuntos
                        <span class="ml-1 font-normal text-stone-400">(máx. {{ MAX_FILES }} archivos · {{ MAX_MB }} MB c/u)</span>
                    </label>

                    <!-- Zona de drop -->
                    <div
                        class="relative flex cursor-pointer flex-col items-center justify-center rounded-lg border-2 border-dashed py-8 text-center transition"
                        :class="dropOver
                            ? 'border-teal-400 bg-teal-50'
                            : 'border-stone-300 bg-stone-50 hover:border-teal-400 hover:bg-teal-50'"
                        @click="fileInput.click()"
                        @dragover.prevent="dropOver = true"
                        @dragleave.prevent="dropOver = false"
                        @drop.prevent="onDrop"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="mb-2 h-8 w-8 text-stone-400">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l-3 3m3-3l3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.338-2.32 5.75 5.75 0 011.55 11.068H6.75z" />
                        </svg>
                        <p class="text-sm font-medium text-stone-600">Arrastra archivos aquí o <span class="text-teal-600 underline underline-offset-2">selecciona</span></p>
                        <p class="mt-1 text-xs text-stone-400">Cualquier tipo de archivo · {{ archivos.length }}/{{ MAX_FILES }} seleccionados</p>

                        <input
                            ref="fileInput"
                            type="file"
                            multiple
                            class="hidden"
                            @change="onFileInput"
                        >
                    </div>

                    <!-- Error de archivo -->
                    <p v-if="errorArchivo" class="mt-1.5 text-xs text-red-600">{{ errorArchivo }}</p>
                    <p v-if="form.errors.adjuntos" class="mt-1.5 text-xs text-red-600">{{ form.errors.adjuntos }}</p>

                    <!-- Lista de archivos seleccionados -->
                    <ul v-if="archivos.length > 0" class="mt-3 space-y-1.5">
                        <li
                            v-for="(file, idx) in archivos"
                            :key="idx"
                            class="flex items-center justify-between rounded-lg border border-stone-200 bg-white px-4 py-2.5 text-sm"
                        >
                            <div class="flex items-center gap-3 min-w-0">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-4 w-4 flex-shrink-0 text-stone-400">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.375 12.739l-7.693 7.693a4.5 4.5 0 01-6.364-6.364l10.94-10.94A3 3 0 1119.5 7.372L8.552 18.32m.009-.01l-.01.01m5.699-9.941l-7.81 7.81a1.5 1.5 0 002.112 2.13" />
                                </svg>
                                <span class="truncate text-stone-700">{{ file.name }}</span>
                            </div>
                            <div class="ml-3 flex flex-shrink-0 items-center gap-3">
                                <span class="text-xs text-stone-400">{{ formatSize(file.size) }}</span>
                                <button
                                    type="button"
                                    class="text-stone-400 transition hover:text-red-500"
                                    @click.stop="quitarArchivo(idx)"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        </li>
                    </ul>
                </div>

                <!-- Acciones -->
                <div class="flex items-center gap-3 border-t border-stone-100 pt-5">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-lg bg-teal-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-teal-700 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <svg v-if="form.processing" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="h-4 w-4 animate-spin">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        {{ form.processing ? 'Enviando…' : 'Registrar ticket' }}
                    </button>
                    <Link
                        :href="route('mis-tickets.index')"
                        class="rounded-lg border border-stone-200 bg-white px-5 py-2.5 text-sm font-semibold text-stone-600 transition hover:bg-stone-50"
                    >
                        Cancelar
                    </Link>
                </div>

            </form>
        </main>
    </AppLayout>
</template>
