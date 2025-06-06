// composables/useMediaDelete.js
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import { useTopGlobalNotify } from '@/Composables/useTopGlobalNotify'

export function useMediaDelete(currentFolder) {
    const { errorNotify } = useTopGlobalNotify()
    const showDeleteDialog = ref(false)

    function showDeleteConfirm() {
        showDeleteDialog.value = true
    }

    function hideDeleteConfirm() {
        showDeleteDialog.value = false
    }

    function deleteItems(selectedItemsData, onSuccess) {
        hideDeleteConfirm()
        
        const requestData = {
            selected_items: selectedItemsData,
            folder_id: currentFolder.value.id
        }

        router.post(route('media.deleteSelected'), requestData, {
            onSuccess: () => {
                onSuccess?.()
                showDeleteDialog.value = false
            },
            onError: (errors) => {
                console.log('Validation errors:', errors)
                errorNotify('Validation errors:', errors)
            }
        })
    }

    return {
        showDeleteDialog,
        showDeleteConfirm,
        hideDeleteConfirm,
        deleteItems
    }
}