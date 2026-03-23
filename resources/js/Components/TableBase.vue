<script setup>
</script>
<template>
    <div class="border border-gray-200 w-full">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <slot name="thead" />
            </thead>
            <tbody class="divide-y divide-gray-200 bg-white">
                <slot name="tbody" />
            </tbody>
        </table>
    </div>
</template>
