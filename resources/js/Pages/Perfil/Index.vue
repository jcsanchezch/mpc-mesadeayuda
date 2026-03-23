<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import {usePage} from '@inertiajs/vue3';
import TextInput from "@/Components/TextInput.vue";
import InputLabel from "@/Components/InputLabel.vue";

defineProps({
    status: {type: String},
});

const user = usePage().props.auth.user;

</script>

<template>

    <AuthLayout>
        <template #header>Perfil</template>

        <div class="space-y-6 max-w-lg">
            <div class="py-6">
                <section>
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">Datos Personales</h2>
                    </header>
                    <div class="mt-5">
                        <InputLabel for="dni" value="DNI"/>
                        <TextInput
                            id="dni"
                            type="text"
                            class="mt-1 block w-full !bg-gray-50 text-sm"
                            v-model="user.dni"
                            disabled="disabled"
                        />
                    </div>

                    <div class="mt-5">
                        <InputLabel for="nombres" value="Nombres"/>
                        <TextInput
                            id="nombres"
                            type="text"
                            class="mt-1 block w-full !bg-gray-50 text-sm"
                            v-model="user.nombres"
                            disabled="disabled"
                        />
                    </div>

                    <div class="mt-5">
                        <InputLabel for="paterno" value="Apellido Paterno"/>
                        <TextInput
                            id="paterno"
                            type="text"
                            class="mt-1 block w-full !bg-gray-50 text-sm"
                            v-model="user.paterno"
                            disabled="disabled"
                        />
                    </div>
                    <div class="mt-5">
                        <InputLabel for="materno" value="Apellido Materno"/>
                        <TextInput
                            id="materno"
                            type="text"
                            class="mt-1 block w-full !bg-gray-50 text-sm"
                            v-model="user.materno"
                            disabled="disabled"
                        />
                    </div>

                </section>
            </div>

        </div>

    </AuthLayout>
</template>
