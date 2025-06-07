// Composables/Media/useMediaPicker.js
import { ref } from 'vue'

export function useMediaPicker() {
    const showMediaModal = ref(false)
    const mediaTargetField = ref(null)
    const onSelectCallback = ref(null)

    const openMediaPicker = (field = null, callback = null) => {
        mediaTargetField.value = field
        onSelectCallback.value = callback
        showMediaModal.value = true
    }

    const handleSelectFromMedia = (media) => {
        if (onSelectCallback.value) {
            onSelectCallback.value(media, mediaTargetField.value)
        }
        showMediaModal.value = false
        mediaTargetField.value = null
        onSelectCallback.value = null
    }

    const clearImageField = (modelValue, fieldKey, fieldName) => {
        if (fieldKey && modelValue[fieldKey]) {
            modelValue[fieldKey][fieldName] = ''
        }
    }

    return {
        showMediaModal,
        openMediaPicker,
        handleSelectFromMedia,
        clearImageField,
    }
}

