<script setup>
import { onMounted, ref } from 'vue';

const model = defineModel({
    type: [String, Number],
    required: true,
});

const input = ref(null);

onMounted(() => {
    if (input.value.hasAttribute('autofocus')) {
        input.value.focus();
    }
});

defineExpose({ focus: () => input.value.focus() });
</script>

<template>
    <input
        class="border border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-5px py-2.5 px-2.5 text-sm block focus:outline-blue-500 bg-white"
        v-model="model"
        ref="input"
    />
</template>

