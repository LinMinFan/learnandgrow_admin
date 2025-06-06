<!-- components/Media/MediaEmptyState.vue -->
<script setup>
defineProps({
    canUpload: Boolean
})
</script>

<template>
    <div class="text-center py-16">
        <div class="text-6xl mb-4">
            <i class="fas fa-folder text-yellow-500"></i>
        </div>
        <h3 class="text-lg font-medium text-gray-900 mb-2">資料夾是空的</h3>
        <p class="text-gray-500">
            <span v-if="canUpload">拖拽檔案到這裡或點擊上傳按鈕來新增檔案</span>
            <span v-else>目前沒有任何項目</span>
        </p>
    </div>
</template>