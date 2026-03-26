<script setup>
import { ref, onMounted } from 'vue'

const props = defineProps({
    label:       { type: String,  default: '' },
    placeholder: { type: String,  default: '' },
    type:        { type: String,  default: 'text' },
    error:       { type: String,  default: '' },
    maxlength:   { type: [String, Number], default: undefined },
    disabled:    { type: Boolean, default: false },
})

const model = defineModel({ type: [String, Number], default: '' })

const input = ref(null)
onMounted(() => { if (input.value?.hasAttribute('autofocus')) input.value.focus() })
defineExpose({ focus: () => input.value?.focus() })
</script>

<template>
    <div>
        <label v-if="label" class="block text-sm font-medium text-gray-700">{{ label }}</label>
        <input
            ref="input"
            v-model="model"
            :type="type"
            :placeholder="placeholder"
            :maxlength="maxlength"
            :disabled="disabled"
            :class="[
                'mt-1 w-full border rounded-[4px] py-2.5 px-3 text-sm bg-white focus:outline-blue-500 transition-colors',
                disabled  ? 'bg-gray-50 border-gray-200 text-gray-400 cursor-not-allowed' : 'border-gray-300 focus:border-blue-500',
                error     ? 'border-red-400' : '',
            ]"
            autocomplete="off"
        />
        <p v-if="error" class="mt-1 text-xs text-red-500">{{ error }}</p>
    </div>
</template>
