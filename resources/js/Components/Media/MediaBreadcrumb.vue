<!-- components/Media/MediaBreadcrumb.vue -->
<script setup>
defineProps({
    breadcrumbs: Array
})

defineEmits(['navigate'])
</script>

<template>
    <nav class="flex items-center space-x-2 mb-4 text-sm" v-if="breadcrumbs.length > 1">
        <template v-for="(crumb, index) in breadcrumbs" :key="crumb.id || 'root'">
            <button 
                @click="$emit('navigate', crumb.id)" 
                class="text-blue-600 hover:text-blue-800 transition"
                :class="{ 'font-semibold': index === breadcrumbs.length - 1 }"
            >
                {{ crumb.name }}
            </button>
            <span v-if="index < breadcrumbs.length - 1" class="text-gray-400">></span>
        </template>
    </nav>
</template>