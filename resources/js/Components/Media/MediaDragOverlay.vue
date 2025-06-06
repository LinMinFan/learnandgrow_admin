<!-- components/Media/MediaDragOverlay.vue -->
<script setup>
defineProps({
    isDragging: Boolean,
    canUpload: Boolean
})
</script>

<template>
    <div 
        v-if="isDragging && canUpload"
        class="fixed inset-0 bg-blue-500 bg-opacity-20 flex items-center justify-center z-50 pointer-events-none"
    >
        <div class="bg-white p-8 rounded-lg shadow-lg border-2 border-dashed border-blue-400">
            <div class="text-center">
                <div class="text-4xl mb-2"><i class="fas fa-upload"></i></div>
                <p class="text-lg font-medium">放開檔案來上傳</p>
            </div>
        </div>
    </div>
</template>