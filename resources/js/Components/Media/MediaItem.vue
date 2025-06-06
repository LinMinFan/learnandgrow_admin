<!-- components/Media/MediaItem.vue -->
<script setup>
import { ref, computed } from 'vue'
import { formatFileSize } from '@/utils/mediaUtils'

const props = defineProps({
    item: Object,
    isSelected: Boolean,
    canDelete: Boolean
})

const emit = defineEmits(['click', 'select'])

const imageError = ref(false)

// 計算是否應該顯示縮圖
const shouldShowThumbnail = computed(() => {
    return props.item.type === 'file' && 
           props.item.data.has_thumbnail && 
           props.item.data.thumbnail_url && 
           !imageError.value
})

// 計算是否為圖片檔案（但沒有縮圖或載入失敗）
const isImageWithoutThumbnail = computed(() => {
    return props.item.type === 'file' && 
           props.item.data.mime_type && 
           props.item.data.mime_type.startsWith('image/') && 
           (!props.item.data.has_thumbnail || imageError.value)
})

function handleClick() {
    emit('click', props.item)
}

function handleImageError() {
    imageError.value = true
}
</script>

<template>
    <div 
        class="relative flex flex-col items-center p-4 border rounded-lg shadow hover:bg-gray-50 transition cursor-pointer group"
        :class="{ 'bg-blue-100 border-blue-300': isSelected }"
        @click="handleClick"
    >
        <!-- 選取框 -->
        <div 
            v-if="canDelete"
            class="absolute top-2 left-2 opacity-0 group-hover:opacity-100 transition-opacity"
            @click.stop="$emit('select', item)"
        >
            <input type="checkbox" :checked="isSelected" class="w-4 h-4">
        </div>

        <!-- 縮圖顯示 -->
        <div v-if="shouldShowThumbnail" class="w-15 h-15 mb-2 overflow-hidden rounded transition-transform group-hover:scale-105">
            <img 
                :src="item.data.thumbnail_url" 
                :alt="item.name"
                @error="handleImageError"
                class="w-full h-full object-cover"
            >
        </div>

        <!-- 圖示顯示 (資料夾或無縮圖的檔案) -->
        <div 
            v-else
            class="text-4xl mb-2 transition-transform group-hover:scale-105"
            :class="{ 'text-red-400': isImageWithoutThumbnail }"
        >
            <i :class="[item.iconClass, item.colorClass]"></i>
        </div>

        <!-- 名稱 -->
        <span class="text-sm font-medium text-center break-words w-full">
            {{ item.name }}
        </span>

        <!-- 檔案大小 (僅檔案) -->
        <span v-if="item.type === 'file'" class="text-xs text-gray-500 mt-1">
            {{ formatFileSize(item.data.size) }}
        </span>

        <!-- 縮圖載入失敗提示 -->
        <span v-if="isImageWithoutThumbnail" class="text-xs text-red-400 mt-1">
            縮圖載入失敗
        </span>
    </div>
</template>