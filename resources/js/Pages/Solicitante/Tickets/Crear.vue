<script setup>
import AuthLayout from '@/Layouts/AuthLayout.vue';
import CrearTicket from './CrearTicket.vue';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

defineProps({
    categorias:   { type: Array,  default: () => [] },
    dependencias: { type: Array,  default: () => [] },
    locales:      { type: Array,  default: () => [] },
    solicitante:  { type: Object, default: () => ({}) },
});
</script>

<template>
    <AuthLayout>
        <template #header>Nuevo Ticket</template>

        <CrearTicket
            :categorias="categorias"
            :solicitante="solicitante"
            :dependencias="dependencias"
            :locales="locales"
            @cancelar="router.visit(route('solicitante.index'))"
        />
    </AuthLayout>
</template>
