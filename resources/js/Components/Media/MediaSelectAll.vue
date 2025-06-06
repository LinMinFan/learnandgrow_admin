<!-- components/Media/MediaSelectAll.vue -->
<script setup>
defineProps({
    totalItems: Number,
    canDelete: Boolean,
    selectAll: Boolean
})

defineEmits(['toggle-select-all'])
</script>

<template>
    <div class="flex items-center mb-4" v-if="totalItems > 0 && canDelete">
        <label class="flex items-center cursor-pointer">
            <input 
                type="checkbox" 
                :checked="selectAll" 
                @change="$emit('toggle-select-all')" 
                class="mr-2"
            >
            <span class="text-sm">全選 ({{ totalItems }} 個項目)</span>
        </label>
    </div>
</template>