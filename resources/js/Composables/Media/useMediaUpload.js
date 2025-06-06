// composables/useMediaUpload.js
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

export function useMediaUpload(currentFolder) {
    const uploadInput = ref(null)
    const isDragging = ref(false)

    function createUploadFormData(files) {
        const formData = new FormData()
        files.forEach(file => formData.append('files[]', file))
        formData.append('folder_id', currentFolder.value.id)
        return formData
    }

    function triggerFileUpload() {
        uploadInput.value.click()
    }

    function handleFileUpload(event) {
        const files = Array.from(event.target.files)
        if (files.length === 0) return

        const formData = createUploadFormData(files)
        
        router.post(route('media.store'), formData, {
            onSuccess: () => {
                event.target.value = ''
            }
        })
    }

    function handleDragOver(event) {
        event.preventDefault()
        isDragging.value = true
    }

    function handleDragLeave(event) {
        event.preventDefault()
        isDragging.value = false
    }

    function handleDrop(event, canUpload) {
        event.preventDefault()
        isDragging.value = false

        if (!canUpload) return

        const files = Array.from(event.dataTransfer.files)
        if (files.length === 0) return

        const formData = createUploadFormData(files)
        router.post(route('media.store'), formData)
    }

    return {
        uploadInput,
        isDragging,
        triggerFileUpload,
        handleFileUpload,
        handleDragOver,
        handleDragLeave,
        handleDrop
    }
}