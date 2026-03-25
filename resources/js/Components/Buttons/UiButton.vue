<script setup>
import { computed } from 'vue'

const props = defineProps({
    size:    { type: String, default: 'xs' },
    color:   { type: String, default: 'blue' },
    variant: { type: String, default: 'principal' },
    label:   { type: String, default: 'Button' },
    icon:    { type: String, default: '' },
    type:    { type: String, default: 'button' }
})

const sizeClasses = computed(() => ({
    xs: 'text-xs  px-2.5 pt-2 pb-1.5',
    sm: 'text-sm  px-5 py-2.5',
    md: 'text-base px-7 py-3.5',
}[props.size] ?? 'text-xs px-3 py-1.5'))

const iconMargin = computed(() => props.size === 'md' ? 'mr-2' : props.size === 'sm' ? 'mr-1.5' : 'mr-1')

const variantColorClasses = computed(() => {
    const map = {
        principal: {
            gray:    'bg-gray-500    border-b-2 border-b-gray-600    text-white    hover:bg-gray-700    focus:bg-gray-700    active:bg-gray-700    focus:ring-gray-600',
            slate:   'bg-slate-500   border-b-2 border-b-slate-600   text-white   hover:bg-slate-700   focus:bg-slate-700   active:bg-slate-700   focus:ring-slate-600',
            zinc:    'bg-zinc-500    border-b-2 border-b-zinc-600    text-white    hover:bg-zinc-700    focus:bg-zinc-700    active:bg-zinc-700    focus:ring-zinc-600',
            neutral: 'bg-neutral-500 border-b-2 border-b-neutral-600 text-white hover:bg-neutral-700 focus:bg-neutral-700 active:bg-neutral-700 focus:ring-neutral-600',
            stone:   'bg-stone-500   border-b-2 border-b-stone-600   text-white   hover:bg-stone-700   focus:bg-stone-700   active:bg-stone-700   focus:ring-stone-600',
            red:     'bg-red-500     border-b-2 border-b-red-600     text-white     hover:bg-red-700     focus:bg-red-700     active:bg-red-700     focus:ring-red-600',
            orange:  'bg-orange-500  border-b-2 border-b-orange-600  text-white  hover:bg-orange-700  focus:bg-orange-700  active:bg-orange-700  focus:ring-orange-600',
            amber:   'bg-amber-500   border-b-2 border-b-amber-600   text-white   hover:bg-amber-700   focus:bg-amber-700   active:bg-amber-700   focus:ring-amber-600',
            yellow:  'bg-yellow-500  border-b-2 border-b-yellow-600  text-white  hover:bg-yellow-700  focus:bg-yellow-700  active:bg-yellow-700  focus:ring-yellow-600',
            lime:    'bg-lime-500    border-b-2 border-b-lime-600    text-white    hover:bg-lime-700    focus:bg-lime-700    active:bg-lime-700    focus:ring-lime-600',
            green:   'bg-green-500   border-b-2 border-b-green-600   text-white   hover:bg-green-700   focus:bg-green-700   active:bg-green-700   focus:ring-green-600',
            emerald: 'bg-emerald-500 border-b-2 border-b-emerald-600 text-white hover:bg-emerald-700 focus:bg-emerald-700 active:bg-emerald-700 focus:ring-emerald-600',
            teal:    'bg-teal-500    border-b-2 border-b-teal-600    text-white    hover:bg-teal-700    focus:bg-teal-700    active:bg-teal-700    focus:ring-teal-600',
            cyan:    'bg-cyan-500    border-b-2 border-b-cyan-600    text-white    hover:bg-cyan-700    focus:bg-cyan-700    active:bg-cyan-700    focus:ring-cyan-600',
            sky:     'bg-sky-500     border-b-2 border-b-sky-600     text-white     hover:bg-sky-700     focus:bg-sky-700     active:bg-sky-700     focus:ring-sky-600',
            blue:    'bg-blue-500    border-b-2 border-b-blue-600    text-white    hover:bg-blue-700    focus:bg-blue-700    active:bg-blue-700    focus:ring-blue-600',
            indigo:  'bg-indigo-500  border-b-2 border-b-indigo-600  text-white  hover:bg-indigo-700  focus:bg-indigo-700  active:bg-indigo-700  focus:ring-indigo-600',
            violet:  'bg-violet-500  border-b-2 border-b-violet-600  text-white  hover:bg-violet-700  focus:bg-violet-700  active:bg-violet-700  focus:ring-violet-600',
            purple:  'bg-purple-500  border-b-2 border-b-purple-600  text-white  hover:bg-purple-700  focus:bg-purple-700  active:bg-purple-700  focus:ring-purple-600',
            fuchsia: 'bg-fuchsia-500 border-b-2 border-b-fuchsia-600 text-white hover:bg-fuchsia-700 focus:bg-fuchsia-700 active:bg-fuchsia-700 focus:ring-fuchsia-600',
            pink:    'bg-pink-500    border-b-2 border-b-pink-600    text-white    hover:bg-pink-700    focus:bg-pink-700    active:bg-pink-700    focus:ring-pink-600',
            rose:    'bg-rose-500    border-b-2 border-b-rose-600    text-white   hover:bg-rose-700    focus:bg-rose-700    active:bg-rose-700    focus:ring-rose-600',
        },
        soft: {
            gray:    'bg-gray-200    border-b-2 border-b-gray-300    text-gray-700    hover:bg-gray-300    hover:text-gray-800    focus:bg-gray-300    active:bg-gray-300    focus:ring-gray-200',
            slate:   'bg-slate-200   border-b-2 border-b-slate-300   text-slate-700   hover:bg-slate-300   hover:text-slate-800   focus:bg-slate-300   active:bg-slate-300   focus:ring-slate-200',
            zinc:    'bg-zinc-200    border-b-2 border-b-zinc-300    text-zinc-700    hover:bg-zinc-300    hover:text-zinc-800    focus:bg-zinc-300    active:bg-zinc-300    focus:ring-zinc-200',
            neutral: 'bg-neutral-200 border-b-2 border-b-neutral-300 text-neutral-700 hover:bg-neutral-300 hover:text-neutral-800 focus:bg-neutral-300 active:bg-neutral-300 focus:ring-neutral-200',
            stone:   'bg-stone-200   border-b-2 border-b-stone-300   text-stone-700   hover:bg-stone-300   hover:text-stone-800   focus:bg-stone-300   active:bg-stone-300   focus:ring-stone-200',
            red:     'bg-red-200     border-b-2 border-b-red-300     text-red-700     hover:bg-red-300     hover:text-red-800     focus:bg-red-300     active:bg-red-300     focus:ring-red-200',
            orange:  'bg-orange-200  border-b-2 border-b-orange-300  text-orange-700  hover:bg-orange-300  hover:text-orange-800  focus:bg-orange-300  active:bg-orange-300  focus:ring-orange-200',
            amber:   'bg-amber-200   border-b-2 border-b-amber-300   text-amber-700   hover:bg-amber-300   hover:text-amber-800   focus:bg-amber-300   active:bg-amber-300   focus:ring-amber-200',
            yellow:  'bg-yellow-200  border-b-2 border-b-yellow-300  text-yellow-700  hover:bg-yellow-300  hover:text-yellow-800  focus:bg-yellow-300  active:bg-yellow-300  focus:ring-yellow-200',
            lime:    'bg-lime-200    border-b-2 border-b-lime-300    text-lime-700    hover:bg-lime-300    hover:text-lime-800    focus:bg-lime-300    active:bg-lime-300    focus:ring-lime-200',
            green:   'bg-green-200   border-b-2 border-b-green-300   text-green-700   hover:bg-green-300   hover:text-green-800   focus:bg-green-300   active:bg-green-300   focus:ring-green-200',
            emerald: 'bg-emerald-200 border-b-2 border-b-emerald-300 text-emerald-700 hover:bg-emerald-300 hover:text-emerald-800 focus:bg-emerald-300 active:bg-emerald-300 focus:ring-emerald-200',
            teal:    'bg-teal-200    border-b-2 border-b-teal-300    text-teal-700    hover:bg-teal-300    hover:text-teal-800    focus:bg-teal-300    active:bg-teal-300    focus:ring-teal-200',
            cyan:    'bg-cyan-200    border-b-2 border-b-cyan-300    text-cyan-700    hover:bg-cyan-300    hover:text-cyan-800    focus:bg-cyan-300    active:bg-cyan-300    focus:ring-cyan-200',
            sky:     'bg-sky-200     border-b-2 border-b-sky-300     text-sky-700     hover:bg-sky-300     hover:text-sky-800     focus:bg-sky-300     active:bg-sky-300     focus:ring-sky-200',
            blue:    'bg-blue-200    border-b-2 border-b-blue-300    text-blue-700    hover:bg-blue-300    hover:text-blue-800    focus:bg-blue-300    active:bg-blue-300    focus:ring-blue-200',
            indigo:  'bg-indigo-200  border-b-2 border-b-indigo-300  text-indigo-700  hover:bg-indigo-300  hover:text-indigo-800  focus:bg-indigo-300  active:bg-indigo-300  focus:ring-indigo-200',
            violet:  'bg-violet-200  border-b-2 border-b-violet-300  text-violet-700  hover:bg-violet-300  hover:text-violet-800  focus:bg-violet-300  active:bg-violet-300  focus:ring-violet-200',
            purple:  'bg-purple-200  border-b-2 border-b-purple-300  text-purple-700  hover:bg-purple-300  hover:text-purple-800  focus:bg-purple-300  active:bg-purple-300  focus:ring-purple-200',
            fuchsia: 'bg-fuchsia-200 border-b-2 border-b-fuchsia-300 text-fuchsia-700 hover:bg-fuchsia-300 hover:text-fuchsia-800 focus:bg-fuchsia-300 active:bg-fuchsia-300 focus:ring-fuchsia-200',
            pink:    'bg-pink-200    border-b-2 border-b-pink-300    text-pink-700    hover:bg-pink-300    hover:text-pink-800    focus:bg-pink-300    active:bg-pink-300    focus:ring-pink-200',
            rose:    'bg-rose-200    border-b-2 border-b-rose-300    text-rose-700    hover:bg-rose-300    hover:text-rose-800    focus:bg-rose-300    active:bg-rose-300    focus:ring-rose-200',
        },
        outline: {
            gray:    'bg-white border border-gray-200    text-gray-700    hover:bg-gray-700    hover:text-gray-50    focus:bg-gray-700    active:bg-gray-700    focus:ring-gray-200',
            slate:   'bg-white border border-slate-200   text-slate-700   hover:bg-slate-700   hover:text-slate-50   focus:bg-slate-50   active:bg-slate-50   focus:ring-slate-200',
            zinc:    'bg-white border border-zinc-200    text-zinc-700    hover:bg-zinc-700    hover:text-zinc-50    focus:bg-zinc-50    active:bg-zinc-50    focus:ring-zinc-200',
            neutral: 'bg-white border border-neutral-200 text-neutral-700 hover:bg-neutral-700 hover:text-neutral-50 focus:bg-neutral-50 active:bg-neutral-50 focus:ring-neutral-200',
            stone:   'bg-white border border-stone-200   text-stone-700   hover:bg-stone-700   hover:text-stone-50   focus:bg-stone-50   active:bg-stone-50   focus:ring-stone-200',
            red:     'bg-white border border-red-200     text-red-700     hover:bg-red-700     hover:text-red-50     focus:bg-red-50     active:bg-red-50     focus:ring-red-200',
            orange:  'bg-white border border-orange-200  text-orange-700  hover:bg-orange-700  hover:text-orange-50  focus:bg-orange-50  active:bg-orange-50  focus:ring-orange-200',
            amber:   'bg-white border border-amber-200   text-amber-700   hover:bg-amber-700   hover:text-amber-50   focus:bg-amber-50   active:bg-amber-50   focus:ring-amber-200',
            yellow:  'bg-white border border-yellow-200  text-yellow-700  hover:bg-yellow-700  hover:text-yellow-50  focus:bg-yellow-50  active:bg-yellow-50  focus:ring-yellow-200',
            lime:    'bg-white border border-lime-200    text-lime-700    hover:bg-lime-700    hover:text-lime-50    focus:bg-lime-50    active:bg-lime-50    focus:ring-lime-200',
            green:   'bg-white border border-green-200   text-green-700   hover:bg-green-700   hover:text-green-50   focus:bg-green-50   active:bg-green-50   focus:ring-green-200',
            emerald: 'bg-white border border-emerald-200 text-emerald-700 hover:bg-emerald-700 hover:text-emerald-50 focus:bg-emerald-50 active:bg-emerald-50 focus:ring-emerald-200',
            teal:    'bg-white border border-teal-200    text-teal-700    hover:bg-teal-700    hover:text-teal-50    focus:bg-teal-50    active:bg-teal-50    focus:ring-teal-200',
            cyan:    'bg-white border border-cyan-200    text-cyan-700    hover:bg-cyan-700    hover:text-cyan-50    focus:bg-cyan-50    active:bg-cyan-50    focus:ring-cyan-200',
            sky:     'bg-white border border-sky-200     text-sky-700     hover:bg-sky-700     hover:text-sky-50     focus:bg-sky-50     active:bg-sky-50     focus:ring-sky-200',
            blue:    'bg-white border border-blue-200    text-blue-700    hover:bg-blue-700    hover:text-blue-50    focus:bg-blue-50    active:bg-blue-50    focus:ring-blue-200',
            indigo:  'bg-white border border-indigo-200  text-indigo-700  hover:bg-indigo-700  hover:text-indigo-50  focus:bg-indigo-50  active:bg-indigo-50  focus:ring-indigo-200',
            violet:  'bg-white border border-violet-200  text-violet-700  hover:bg-violet-700  hover:text-violet-50  focus:bg-violet-50  active:bg-violet-50  focus:ring-violet-200',
            purple:  'bg-white border border-purple-200  text-purple-700  hover:bg-purple-700  hover:text-purple-50  focus:bg-purple-50  active:bg-purple-50  focus:ring-purple-200',
            fuchsia: 'bg-white border border-fuchsia-200 text-fuchsia-700 hover:bg-fuchsia-700 hover:text-fuchsia-50 focus:bg-fuchsia-50 active:bg-fuchsia-50 focus:ring-fuchsia-200',
            pink:    'bg-white border border-pink-200    text-pink-700    hover:bg-pink-700    hover:text-pink-50    focus:bg-pink-50    active:bg-pink-50    focus:ring-pink-200',
            rose:    'bg-white border border-rose-200    text-rose-700    hover:bg-rose-700    hover:text-rose-50    focus:bg-rose-50    active:bg-rose-50    focus:ring-rose-200',
        },
    }
    return map[props.variant]?.[props.color] ?? map.principal.gray
})
</script>

<template>
    <button
        :class="[
            'font-medium',
            'leading-none rounded-[4px] cursor-pointer whitespace-nowrap',
            'focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150',
            sizeClasses,
            variantColorClasses
        ]"
        @click="$emit('click')"
        :type="type"
    >
        <span class="flex items-center justify-center">
            <i v-if="icon" :class="[icon, iconMargin]"></i>
            {{ label }}
        </span>
    </button>
</template>
