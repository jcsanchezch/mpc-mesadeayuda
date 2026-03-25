<script setup>
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    align: {
        type: String,
        default: 'right',
    },
    width: {
        type: String,
        default: '48',
    },
    contentClasses: {
        type: String,
        default: 'py-1 bg-white',
    },
});

const closeOnEscape = (e) => {
    if (open.value && e.key === 'Escape') {
        open.value = false;
    }
};

onMounted(() => document.addEventListener('keydown', closeOnEscape));
onUnmounted(() => document.removeEventListener('keydown', closeOnEscape));

const widthClass = computed(() => {
    return {
        32: 'w-32',
        36: 'w-36',
        40: 'w-40',
        44: 'w-44',
        48: 'w-48',
        56: 'w-56',
        64: 'w-64',
        72: 'w-72',
    }[props.width.toString()];
});

const alignmentClasses = computed(() => {
    if (props.align === 'left') {
        return 'ltr:origin-top-left rtl:origin-top-right start-0';
    } else if (props.align === 'right') {
        return 'ltr:origin-top-right rtl:origin-top-left end-0';
    } else {
        return 'origin-top';
    }
});

const open = ref(false);
</script>

<template>
    <div class="relative">
        <div @click="open = !open" class="py-2.5 pl-3 pr-3 border border-transparent rounded-[4px] font-medium text-[13px] uppercase text-white bg-blue-500 border-b-2 border-b-blue-600 hover:text-white hover:bg-blue-600 focus:bg-blue-600 active:bg-blue-700 transition ease-in-out duration-150 leading-none cursor-pointer inline-block">
            <div class="inline-flex items-center tracking-tight">
                <slot name="trigger"/>
                <i class="fa-solid fa-angle-down text-xs ml-2"></i>
            </div>
        </div>
        <div v-show="open" class="fixed inset-0 z-40" @click="open = false"></div>
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition ease-in duration-75"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-show="open"
                class="absolute z-50 mt-1 rounded-[4px] shadow-md"
                :class="[widthClass, alignmentClasses]"
                style="display: none"
                @click="open = false"
            >
                <div class="rounded-[4px] ring-1 ring-gray-300 ring-opacity-5" :class="contentClasses">
                    <slot name="content" />
                </div>
            </div>
        </Transition>
    </div>
</template>
