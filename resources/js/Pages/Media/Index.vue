<script setup>
import { ref, computed, toRef } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import ConfirmDialog from '@/Components/ConfirmDialog.vue'

// 子組件
import MediaHeader from '@/Components/Media/MediaHeader.vue'
import MediaBreadcrumb from '@/Components/Media/MediaBreadcrumb.vue'
import MediaSelectAll from '@/Components/Media/MediaSelectAll.vue'
import MediaGrid from '@/Components/Media/MediaGrid.vue'
import MediaEmptyState from '@/Components/Media/MediaEmptyState.vue'
import MediaDragOverlay from '@/Components/Media/MediaDragOverlay.vue'

// Composables
import { useMediaSelection } from '@/composables/Media/useMediaSelection'
import { useMediaUpload } from '@/composables/Media/useMediaUpload'
import { useMediaDelete } from '@/composables/Media/useMediaDelete'
import { useMediaNavigation } from '@/composables/Media/useMediaNavigation'

// 工具函數
import { getFileIconClass, getFileColorClass } from '@/utils/mediaUtils'

// ==================== Props ====================
const props = defineProps({
    folders: Array,
    files: Array,
    currentFolder: Object,
    breadcrumbs: Array,
    canDelete: Boolean,
    canUpload: Boolean,
})

// ==================== Composables ====================
const currentFolderRef = toRef(props, 'currentFolder')

const {
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
} = useMediaSelection()

const {
    uploadInput,
    isDragging,
    triggerFileUpload,
    handleFileUpload,
    handleDragOver,
    handleDragLeave,
    handleDrop
} = useMediaUpload(currentFolderRef)

const {
    showDeleteDialog,
    showDeleteConfirm,
    hideDeleteConfirm,
    deleteItems
} = useMediaDelete(currentFolderRef)

const {
    navigateToFolder,
    navigateToBreadcrumb
} = useMediaNavigation()

// ==================== Computed ====================
const allItems = computed(() => {
    const folderItems = props.folders.map(folder => ({
        id: folder.id,
        name: folder.name,
        type: 'folder',
        iconClass: 'fas fa-folder',
        colorClass: 'text-yellow-500',
        data: folder
    }))

    const fileItems = props.files.map(file => ({
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

// ==================== 事件處理 ====================
function handleItemClick(item) {
    if (item.type === 'folder') {
        navigateToFolder(item.id)
    }
}

function handleItemSelect(item) {
    toggleItemSelection(item)
    updateSelectAllStatus(allItems.value)
}

function handleSelectAll() {
    toggleSelectAll(allItems.value)
}

function handleDeleteClick() {
    showDeleteConfirm()
}

function handleDeleteConfirm() {
    const selectedData = getSelectedItemsData()
    deleteItems(selectedData, clearSelection)
}

function handleDragDrop(event) {
    handleDrop(event, props.canUpload)
}

// 添加刪除確認狀態
const mediaToDelete = computed(() => showDeleteDialog.value)
</script>

<template>
    <AdminLayout>
        <div 
            class="p-6 min-h-screen transition-colors" 
            :class="{ 'bg-blue-50 border-2 border-dashed border-blue-300': isDragging }"
            @dragover="handleDragOver" 
            @dragleave="handleDragLeave" 
            @drop="handleDragDrop"
        >
            <!-- 標題列 -->
            <MediaHeader
                :can-upload="canUpload"
                :can-delete="canDelete"
                :has-selected-items="hasSelectedItems"
                :selected-count="selectedItemsCount"
                @upload="triggerFileUpload"
                @delete="handleDeleteClick"
            />

            <!-- 麵包屑導航 -->
            <MediaBreadcrumb
                :breadcrumbs="breadcrumbs"
                @navigate="navigateToBreadcrumb"
            />

            <!-- 全選控制 -->
            <MediaSelectAll
                :total-items="allItems.length"
                :can-delete="canDelete"
                :select-all="selectAll"
                @toggle-select-all="handleSelectAll"
            />

            <!-- 項目網格 -->
            <MediaGrid
                v-if="!isEmpty"
                :items="allItems"
                :can-delete="canDelete"
                :is-item-selected="isItemSelected"
                @item-click="handleItemClick"
                @item-select="handleItemSelect"
            />

            <!-- 空狀態 -->
            <MediaEmptyState
                v-else
                :can-upload="canUpload"
            />

            <!-- 拖拽提示 -->
            <MediaDragOverlay
                :is-dragging="isDragging"
                :can-upload="canUpload"
            />
        </div>

        <!-- 隱藏的檔案輸入 -->
        <input 
            ref="uploadInput" 
            type="file" 
            multiple 
            @change="handleFileUpload" 
            class="hidden"
        >

        <!-- 刪除確認對話框 -->
        <ConfirmDialog 
            v-if="mediaToDelete" 
            :show="showDeleteDialog"
            title="確認刪除" 
            :confirmMessage="`確定要刪除選取的 ${selectedItemsCount} 個項目嗎？此操作無法復原。`"
            confirmButtonText="確定"
            @confirm="handleDeleteConfirm" 
            @cancel="hideDeleteConfirm" 
        />
    </AdminLayout>
</template>