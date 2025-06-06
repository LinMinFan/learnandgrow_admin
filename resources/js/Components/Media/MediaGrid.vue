<!-- components/Media/MediaGrid.vue -->
<script setup>
import { computed } from 'vue'
import MediaItem from '@/Components/Media/MediaItem.vue'

const props = defineProps({
    items: Array,
    canDelete: Boolean,
    isItemSelected: Function
})

defineEmits(['item-click', 'item-select'])

const itemRows = computed(() => {
    const ITEMS_PER_ROW = 6
    const rows = []
    for (let i = 0; i < props.items.length; i += ITEMS_PER_ROW) {
        rows.push(props.items.slice(i, i + ITEMS_PER_ROW))
    }
    return rows
})
</script>

<template>
    <main class="space-y-4" v-if="items.length > 0">
        <div 
            v-for="(row, rowIndex) in itemRows" 
            :key="rowIndex" 
            class="grid grid-cols-6 gap-4"
        >
            <MediaItem
                v-for="item in row"
                :key="`${item.type}-${item.id}`"
                :item="item"
                :is-selected="isItemSelected(item)"
                :can-delete="canDelete"
                @click="$emit('item-click', item)"
                @select="$emit('item-select', item)"
            />
        </div>
    </main>
</template>

<style scoped>
.grid-cols-6 {
    grid-template-columns: repeat(6, minmax(0, 1fr));
}

@media (max-width: 1024px) {
    .grid-cols-6 {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    .grid-cols-6 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .grid-cols-6 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
</style>