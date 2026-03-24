<script setup>
import { useForm } from '@inertiajs/vue3';
import imgLogoTextoDerecha from '../../images/logoTextoDerecha.png';
import { route } from "ziggy-js";

const form = useForm({
    username: '',
    password: ''
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};

</script>

<template>
    <div class="min-h-screen flex justify-center items-center bg-gray-100">
        <div class="w-[448px] m-4 pt-6 pb-12 px-12 bg-white overflow-hidden rounded-xl shadow-lg leading-4">

            <div class="flex flex-col items-center mt-8 mx-0">
                <img :src="imgLogoTextoDerecha" class="w-48" alt="Logo" />
                <div
                    class="mt-2 px-8 py-4 text-center inline-block text-[24px] font-bold tracking-normal rounded-5px bg-cyan-100 border-b-2 border-b-cyan-200  text-sky-700 leading-none">
                    Mesa de Servicio
                </div>
            </div>
            <div class="mt-10">
                <p class="text-center text-[24px] text-gray-700 font-bold">
                    Inicio de Sesión
                </p>
                <p class="mt-3 text-center text-sm text-gray-800 font-light">
                    Credenciales del <span class="font-medium">Directorio Activo</span>.
                </p>
            </div>
            <div class="mt-8">
                <form @submit.prevent="submit">
                    <div class="mt-2">
                        <div class="w-full space-y-6">
                            <div class="relative">
                                <input type="text" id="hs-floating-gray-input-username" name="username"
                                    class="peer p-4 block w-full bg-white border border-gray-200 rounded-lg sm:text-sm placeholder:text-transparent focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none focus:pt-6 focus:pb-2 [&:not(:placeholder-shown)]:pt-6 [&:not(:placeholder-shown)]:pb-2 autofill:pt-6 autofill:pb-2 focus:outline-blue-500 focus:outline-1 autofill:shadow-[inset_0_0_0px_1000px_rgb(244,251,255)] autofill:border-blue-400"
                                    placeholder=" " v-model="form.username" required autofocus
                                    autocomplete="username" />
                                <label for="hs-floating-gray-input-username"
                                    class="absolute top-0 start-0 p-4 h-full sm:text-sm truncate pointer-events-none transition ease-in-out duration-100 border border-transparent origin-[0_0] peer-disabled:opacity-50 peer-disabled:pointer-events-none peer-focus:scale-90 peer-focus:translate-x-0.5 peer-focus:-translate-y-2 peer-focus:text-gray-500 peer-[&:not(:placeholder-shown)]:scale-90 peer-[&:not(:placeholder-shown)]:translate-x-0.5 peer-[&:not(:placeholder-shown)]:-translate-y-2 peer-[&:not(:placeholder-shown)]:text-gray-500">Usuario
                                </label>
                            </div>

                            <div class="relative">
                                <input type="password" id="hs-floating-gray-input-passowrd" name="passowrd"
                                    class="peer p-4 block w-full bg-white border border-gray-200 rounded-lg sm:text-sm placeholder:text-transparent focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none focus:pt-6 focus:pb-2 [&:not(:placeholder-shown)]:pt-6 [&:not(:placeholder-shown)]:pb-2 autofill:pt-6 autofill:pb-2 focus:outline-blue-500 focus:outline-1 autofill:shadow-[inset_0_0_0px_1000px_rgb(244,251,255)] autofill:border-blue-400"
                                    placeholder=" " v-model="form.password" required autocomplete="password" />
                                <label for="hs-floating-gray-input-passowrd"
                                    class="absolute top-0 start-0 p-4 h-full sm:text-sm truncate pointer-events-none transition ease-in-out duration-100 border border-transparent origin-[0_0] peer-disabled:opacity-50 peer-disabled:pointer-events-none peer-focus:scale-90 peer-focus:translate-x-0.5 peer-focus:-translate-y-2 peer-focus:text-gray-500 peer-[&:not(:placeholder-shown)]:scale-90 peer-[&:not(:placeholder-shown)]:translate-x-0.5 peer-[&:not(:placeholder-shown)]:-translate-y-2 peer-[&:not(:placeholder-shown)]:text-gray-500">Contraseña</label>
                            </div>

                        </div>
                    </div>
                    <div class="mt-4 mx-3">
                        <div v-show="form.errors.username">
                            <p class="text-sm text-red-600 text-center">
                                {{ form.errors.username }}
                            </p>
                        </div>
                        <div v-show="form.errors.password">
                            <p class="text-sm text-red-600 text-center">
                                {{ form.errors.password }}
                            </p>
                        </div>
                    </div>
                    <div class="mt-6">
                        <button
                            class="w-full items-center px-8 py-4 bg-blue-500 border border-transparent rounded-lg font-normal text-sm text-white uppercase tracking-widest hover:bg-blue-600 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition ease-in-out duration-150 cursor-pointer"
                            :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            <i class="fa-solid fa-lock-open mr-2"></i> Iniciar Sesión
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</template>
