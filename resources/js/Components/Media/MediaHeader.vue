<!-- components/Media/MediaHeader.vue -->
<script setup>
defineProps({
    canUpload: Boolean,
    canDelete: Boolean,
    hasSelectedItems: Boolean,
    selectedCount: Number
})

defineEmits(['upload', 'delete'])
</script>

<template>
    <header class="flex justify-between items-center mb-4">
        <h1 class="text-2xl font-bold">媒體庫</h1>

        <div class="flex gap-2" v-if="canUpload || canDelete">
            <button 
                v-if="canUpload" 
                @click="$emit('upload')"
                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition"
            >
                <i class="fas fa-upload"></i> 上傳檔案
            </button>

            <button 
                v-if="canDelete && hasSelectedItems" 
                @click="$emit('delete')"
                class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition"
            >
                <i class="fas fa-trash-can"></i> 刪除選取 ({{ selectedCount }})
            </button>
        </div>
    </header>
</template>