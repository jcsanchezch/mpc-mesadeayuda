<script setup>

defineProps({
    label: {
        type: String,
        default: 'Button'
    },
    icon: {
        type: String,
        default: 'fa-solid fa-search'
    },
    type: {
        type: String,
        default: 'button'
    },
    extraClass: {
        type: String,
        default: ''
    }
})
</script>

<template>
    <button
        class="bg-blue-500 border-b-2 border-b-blue-600 text-white px-4 py-2 rounded-5px cursor-pointer whitespace-nowrap focus:bg-blue-700 active:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2 transition ease-in-out duration-150"

        @click="$emit('click')"
        :type="type"
    >
        <i v-if="icon" :class="icon + ' mr-1.5'"></i>
        <slot>{{ label }}</slot>
    </button>
</template>


