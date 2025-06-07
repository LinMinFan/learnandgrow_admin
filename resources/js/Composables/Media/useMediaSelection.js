// Composables/Media/useMediaSelection.js
import { ref, computed } from 'vue'

export function useMediaSelection() {
    const selectedItems = ref(new Set())
    const selectAll = ref(false)

    const selectedItemsCount = computed(() => selectedItems.value.size)
    const hasSelectedItems = computed(() => selectedItemsCount.value > 0)

    function getItemKey(item) {
        return `${item.type}-${item.id}`
    }

    function toggleItemSelection(item) {
        const key = getItemKey(item)
        if (selectedItems.value.has(key)) {
            selectedItems.value.delete(key)
        } else {
            selectedItems.value.add(key)
        }
    }

    function toggleSelectAll(allItems) {
        if (selectAll.value) {
            selectedItems.value.clear()
        } else {
            allItems.forEach(item => {
                selectedItems.value.add(getItemKey(item))
            })
        }
        selectAll.value = !selectAll.value
    }

    function updateSelectAllStatus(allItems) {
        selectAll.value = selectedItems.value.size === allItems.length && allItems.length > 0
    }

    function isItemSelected(item) {
        return selectedItems.value.has(getItemKey(item))
    }

    function clearSelection() {
        selectedItems.value.clear()
        selectAll.value = false
    }

    function getSelectedItemsData() {
        return Array.from(selectedItems.value).map(key => {
            const [type, id] = key.split('-')
            return { type, id: parseInt(id) }
        })
    }

    return {
        selectedItems,
        selectAll,
        selectedItemsCount,
        hasSelectedItems,
        toggleItemSelection,
        toggleSelectAll,
        updateSelectAllStatus,
        isItemSelected,
        clearSelection,
        getSelectedItemsData
    }
}