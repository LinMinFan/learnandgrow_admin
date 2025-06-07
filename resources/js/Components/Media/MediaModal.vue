<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3';
import axios from 'axios'
import MediaBreadcrumb from '@/Components/Media/MediaBreadcrumb.vue'
import MediaGrid from '@/Components/Media/MediaGrid.vue'
import MediaEmptyState from '@/Components/Media/MediaEmptyState.vue'
import MediaDragOverlay from '@/Components/Media/MediaDragOverlay.vue'

import { useMediaSelection } from '@/Composables/Media/useMediaSelection'
import { useMediaUpload } from '@/Composables/Media/useMediaUpload'
import { getFileIconClass, getFileColorClass } from '@/utils/mediaUtils'

const props = defineProps({
    show: Boolean,
    canUpload: {
        type: Boolean,
        default: true,
    },
})

const emit = defineEmits(['select', 'update:show'])

const folders = ref([])
const files = ref([])
const currentFolder = ref(null)
const breadcrumbs = ref([])

// 載入媒體資料
const fetchMediaData = async (folderId = null) => {
    try {
        const url = folderId 
            ? route('api.media.show', folderId)
            : route('api.media.index')
        const { data } = await axios.get(url)

        folders.value = data.folders ?? []
        files.value = data.files ?? []
        currentFolder.value = data.currentFolder ?? null
        breadcrumbs.value = data.breadcrumbs ?? []
    } catch (error) {
        console.error('載入媒體失敗', error)
    }
}

// 監聽 show 變更，彈窗開啟時載入資料
watch(() => props.show, (visible) => {
    if (visible) {
        fetchMediaData(null)  // 一定要明確帶 null 表示根目錄
    }
})

// 處理點擊檔案或資料夾
const handleItemClick = (item) => {
    if (item.type === 'folder') {
        fetchMediaData(item.id)
    } else if (item.type === 'file') {
        emit('select', item.data)
        emit('update:show', false)
    }
}

const navigateToBreadcrumb = (folderId) => {
    fetchMediaData(folderId)
}

const {
    clearSelection,
} = useMediaSelection()

const {
    uploadInput,
    isDragging,
    handleDragOver,
    handleDragLeave,
    handleDrop
} = useMediaUpload(currentFolder)

const allItems = computed(() => {
    const folderItems = folders.value.map(folder => ({
        id: folder.id,
        name: folder.name,
        type: 'folder',
        iconClass: 'fas fa-folder',
        colorClass: 'text-yellow-500',
        data: folder
    }))

    const fileItems = files.value.map(file => ({
        id: file.id,
        name: file.name,
        type: 'file',
        iconClass: getFileIconClass(file.mime_type),
        colorClass: getFileColorClass(file.mime_type),
        data: file
    }))

    return [...folderItems, ...fileItems]
})

const isEmpty = computed(() => allItems.value.length === 0)

const handleClose = () => {
    clearSelection()
    emit('update:show', false)
}

const handleDragDrop = (event) => {
    handleDrop(event, props.canUpload)
}

const toMediaPage = (currentFolder) => {
    if (currentFolder && currentFolder.id) {
        router.visit(route('media.show', currentFolder.id))
    } else {
        router.visit(route('media.index'))
    }
}

</script>

<template>
    <teleport to="body">
        <div 
            v-if="show" 
            class="fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center px-4 sm:px-6 transition-all"
        >
            <div 
                class="bg-white w-full max-w-6xl h-[80vh] rounded-2xl shadow-2xl overflow-hidden flex flex-col relative transition-all"
            >
                <!-- Header -->
                <div class="flex justify-between items-center p-5 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-2xl font-semibold text-gray-800">媒體庫</h2>
                    <div class="flex space-x-2">
                        <!-- 上傳按鈕 -->
                        <button 
                            @click="toMediaPage(currentFolder)"
                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition inline-flex items-center"
                        >
                            <i class="fas fa-upload mr-2"></i> 上傳檔案
                        </button>

                        <!-- 關閉按鈕 -->
                        <button 
                            @click="handleClose" 
                            class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition inline-flex items-center"
                        >
                            <i class="fas fa-times mr-2"></i> 關閉
                        </button>
                    </div>
                </div>

                <!-- Content -->
                <div 
                    class="flex-1 overflow-auto relative p-4 sm:p-6"
                    :class="{ 'bg-blue-50 border-2 border-dashed border-blue-300': isDragging }"
                    @dragover="handleDragOver"
                    @dragleave="handleDragLeave"
                    @drop="handleDragDrop">

                    <MediaBreadcrumb :breadcrumbs="breadcrumbs" @navigate="navigateToBreadcrumb" />

                    <MediaGrid
                        v-if="!isEmpty"
                        :items="allItems"
                        :can-delete="false"
                        :is-item-selected="() => false"
                        @item-click="handleItemClick"
                        @item-select="() => {}"
                    />

                    <MediaEmptyState v-else :can-upload="canUpload" />

                    <MediaDragOverlay :is-dragging="isDragging" :can-upload="canUpload" />
                </div>
            </div>
        </div>
    </teleport>
</template>
