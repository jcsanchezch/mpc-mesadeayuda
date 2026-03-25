<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import { usePage, useForm } from '@inertiajs/vue3';
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";
import SearchSelect from "@/Components/SearchSelect.vue";
import { route } from 'ziggy-js';
import UiButton from "@/Components/Buttons/UiButton.vue";

const props = defineProps({
    celular:        { type: String, default: null },
    local_id:       { type: Number, default: null },
    dependencia_id: { type: Number, default: null },
    locales:        { type: Array,  default: () => [] },
    dependencias:   { type: Array,  default: () => [] },
    status:         { type: String },
});

const user = usePage().props.auth.user;

const form = useForm({
    celular:        props.celular        ?? '',
    local_id:       props.local_id       ?? '',
    dependencia_id: props.dependencia_id ?? '',
});

const submit = () => {
    form.put(route('perfil.update'));
};
</script>

<template>
    <AuthLayout>
        <template #header>Perfil</template>

        <div class="max-w-4xl space-y-6">

            <!-- Datos personales (readonly) -->
            <div class="bg-white border border-gray-200 rounded-[4px] p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-5">Datos del Usuario</h2>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <InputLabel for="usuario" value="Usuario" />
                        <input id="usuario" type="text" :value="user.usuario" readonly
                            class="mt-1 block w-full border border-gray-200 bg-gray-50 text-gray-600 rounded-[4px] py-2.5 px-2.5 text-sm cursor-default" />
                    </div>
                    <div>
                        <InputLabel for="dni" value="DNI" />
                        <input id="dni" type="text" :value="user.dni" readonly
                            class="mt-1 block w-full border border-gray-200 bg-gray-50 text-gray-600 rounded-[4px] py-2.5 px-2.5 text-sm cursor-default" />
                    </div>
                    <div>
                        <InputLabel for="nombres" value="Nombres" />
                        <input id="nombres" type="text" :value="user.nombres" readonly
                            class="mt-1 block w-full border border-gray-200 bg-gray-50 text-gray-600 rounded-[4px] py-2.5 px-2.5 text-sm cursor-default" />
                    </div>
                    <div>
                        <InputLabel for="paterno" value="Apellido Paterno" />
                        <input id="paterno" type="text" :value="user.paterno" readonly
                            class="mt-1 block w-full border border-gray-200 bg-gray-50 text-gray-600 rounded-[4px] py-2.5 px-2.5 text-sm cursor-default" />
                    </div>
                    <div>
                        <InputLabel for="materno" value="Apellido Materno" />
                        <input id="materno" type="text" :value="user.materno" readonly
                            class="mt-1 block w-full border border-gray-200 bg-gray-50 text-gray-600 rounded-[4px] py-2.5 px-2.5 text-sm cursor-default" />
                    </div>
                </div>
            </div>

            <!-- Datos editables -->
            <form @submit.prevent="submit" class="bg-white border border-gray-200 rounded-[4px] p-6">
                <h2 class="text-sm font-semibold text-gray-700 mb-5">Datos del Trabajador</h2>

                <div v-if="status" class="mb-4 text-sm text-emerald-600 bg-emerald-50 border border-emerald-100 rounded-[4px] px-4 py-2">
                    {{ status }}
                </div>

                <div class="space-y-4">
                    <div>
                        <InputLabel for="celular" value="Celular" />
                        <TextInput id="celular" type="text" v-model="form.celular"
                            class="mt-1 block w-full text-sm"
                            :class="{ '!border-red-400': form.errors.celular }"
                            placeholder="Ej. 987654321" maxlength="15" />
                        <p v-if="form.errors.celular" class="mt-1 text-xs text-red-500">{{ form.errors.celular }}</p>
                    </div>

                    <div>
                        <InputLabel for="dependencia_id" value="Dependencia" />
                        <SearchSelect
                            v-model="form.dependencia_id"
                            :options="dependencias"
                            placeholder="— Sin dependencia asignada —"
                            :has-error="!!form.errors.dependencia_id" />
                        <p v-if="form.errors.dependencia_id" class="mt-1 text-xs text-red-500">{{ form.errors.dependencia_id }}</p>
                    </div>

                    <div>
                        <InputLabel for="local_id" value="Local" />
                        <SearchSelect
                            v-model="form.local_id"
                            :options="locales"
                            placeholder="— Sin local asignado —"
                            :has-error="!!form.errors.local_id" />
                        <p v-if="form.errors.local_id" class="mt-1 text-xs text-red-500">{{ form.errors.local_id }}</p>
                    </div>
                </div>

                <div class="mt-5 flex items-center gap-3">
                    <UiButton
                        type="submit"
                        label="Guardar cambios"
                        icon="fa-solid fa-floppy-disk"
                        size="md"
                        :disabled="form.processing" />
                </div>
            </form>

        </div>
    </AuthLayout>
</template>
