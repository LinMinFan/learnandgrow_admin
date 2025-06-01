<script setup>
import { ref, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { router } from '@inertiajs/vue3'
import ConfirmDialog from '@/Components/ConfirmDialog.vue'

const props = defineProps({
    folders: Array,
    files: Array,
    currentFolder: Object,
    breadcrumbs: Array,
    canDelete: Boolean,
    canUpload: Boolean,
})

// 選取狀態管理
const selectedItems = ref(new Set())
const selectAll = ref(false)
const showDeleteDialog = ref(false)
const uploadInput = ref(null)
const isDragging = ref(false)

// 所有項目（資料夾 + 檔案）
const allItems = computed(() => {
    const items = []

    // 加入資料夾
    props.folders.forEach(folder => {
        items.push({
            id: folder.id,
            name: folder.name,
            type: 'folder',
            iconClass: 'fas fa-folder',
            colorClass: 'text-yellow-500',
            data: folder
        })
    })

    // 加入檔案
    props.files.forEach(file => {
        items.push({
            id: file.id,
            name: file.name,
            type: 'file',
            iconClass: getFileIconClass(file.mime_type),
            colorClass: getFileColorClass(file.mime_type),
            data: file
        })
    })

    return items
})

// 分批顯示（每行6個）
const itemRows = computed(() => {
    const rows = []
    for (let i = 0; i < allItems.value.length; i += 6) {
        rows.push(allItems.value.slice(i, i + 6))
    }
    return rows
})

// 取得檔案圖示
function getFileIconClass(mimeType) {
    if (mimeType.startsWith('image/')) return 'fas fa-file-image'
    if (mimeType.includes('pdf')) return 'fas fa-file-pdf'
    if (mimeType.includes('word')) return 'fas fa-file-word'
    return 'fas fa-file'
}

// 取得 icon 顏色
function getFileColorClass(mimeType) {
    if (mimeType.startsWith('image/')) return 'text-blue-500'
    if (mimeType.includes('pdf')) return 'text-red-500'
    if (mimeType.includes('word')) return 'text-indigo-500'
    return 'text-gray-500'
}

// 導航到資料夾
function navigateToFolder(folderId) {
    router.visit(route('media.index'), {
        data: {
            media_folder_id: folderId
        }
    })
}

// 麵包屑導航
function navigateToBreadcrumb(folderId) {
    if (folderId) {
        router.visit(route('media.index'), {
            data: {
                media_folder_id: folderId
            }
        })
    } else {
        router.visit(route('media.index'))
    }
    
}

// 選取項目
function toggleItemSelection(item) {
    const key = `${item.type}-${item.id}`
    if (selectedItems.value.has(key)) {
        selectedItems.value.delete(key)
    } else {
        selectedItems.value.add(key)
    }
    updateSelectAllStatus()
}

// 全選/取消全選
function toggleSelectAll() {
    if (selectAll.value) {
        selectedItems.value.clear()
    } else {
        allItems.value.forEach(item => {
            selectedItems.value.add(`${item.type}-${item.id}`)
        })
    }
    selectAll.value = !selectAll.value
}

// 更新全選狀態
function updateSelectAllStatus() {
    selectAll.value = selectedItems.value.size === allItems.value.length && allItems.value.length > 0
}

// 檢查項目是否被選取
function isItemSelected(item) {
    return selectedItems.value.has(`${item.type}-${item.id}`)
}

// 刪除選取項目
function deleteSelectedItems() {
    const items = Array.from(selectedItems.value).map(key => {
        const [type, id] = key.split('-')
        return { type, id: parseInt(id) }
    })

    router.delete(route('media.deleteSelected'), {
        data: { selected_items: items },
        onSuccess: () => {
            selectedItems.value.clear()
            selectAll.value = false
            showDeleteDialog.value = false
        }
    })
}

// 檔案上傳
function triggerFileUpload() {
    uploadInput.value.click()
}

function handleFileUpload(event) {
    const files = Array.from(event.target.files)
    if (files.length === 0) return

    const formData = new FormData()
    files.forEach(file => {
        formData.append('files[]', file)
    })
    formData.append('media_folder_id', props.currentFolder.id)

    router.put(route('media.update'), formData, {
        onSuccess: () => {
            event.target.value = '' // 清空 input
        }
    })
}

// 拖拽上傳
function handleDragOver(event) {
    event.preventDefault()
    isDragging.value = true
}

function handleDragLeave(event) {
    event.preventDefault()
    isDragging.value = false
}

function handleDrop(event) {
    event.preventDefault()
    isDragging.value = false

    if (!props.canUpload) return

    const files = Array.from(event.dataTransfer.files)
    if (files.length === 0) return

    const formData = new FormData()
    files.forEach(file => {
        formData.append('files[]', file)
    })
    formData.append('media_folder_id', props.currentFolder.id)

    router.put(route('media.update'), formData)
}
</script>

<template>
    <AdminLayout>
        <div class="p-6 min-h-screen" :class="{ 'bg-blue-50 border-2 border-dashed border-blue-300': isDragging }"
            @dragover="handleDragOver" @dragleave="handleDragLeave" @drop="handleDrop">
            <!-- 標題列 -->
            <div class="flex justify-between items-center mb-4">
                <h1 class="text-2xl font-bold">媒體庫</h1>

                <!-- 操作按鈕 -->
                <div class="flex gap-2" v-if="canUpload || canDelete">
                    <button v-if="canUpload" @click="triggerFileUpload"
                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                        <i class="fas fa-upload"></i> 上傳檔案
                    </button>

                    <button v-if="canDelete && selectedItems.size > 0" @click="showDeleteDialog = true"
                        class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                        <i class="fas fa-trash-can"></i> 刪除選取 ({{ selectedItems.size }})
                    </button>
                </div>
            </div>

            <!-- 麵包屑導航 -->
            <nav class="flex items-center space-x-2 mb-4 text-sm" v-if="breadcrumbs.length > 1">
                <template v-for="(crumb, index) in breadcrumbs" :key="crumb.id || 'root'">
                    <button @click="navigateToBreadcrumb(crumb.id)" class="text-blue-600 hover:text-blue-800 transition"
                        :class="{ 'font-semibold': index === breadcrumbs.length - 1 }">
                        {{ crumb.name }}
                    </button>
                    <span v-if="index < breadcrumbs.length - 1" class="text-gray-400">></span>
                </template>
            </nav>

            <!-- 全選控制 -->
            <div class="flex items-center mb-4" v-if="allItems.length > 0 && canDelete">
                <label class="flex items-center cursor-pointer">
                    <input type="checkbox" :checked="selectAll" @change="toggleSelectAll" class="mr-2">
                    <span class="text-sm">全選 ({{ allItems.length }} 個項目)</span>
                </label>
            </div>

            <!-- 項目網格 -->
            <div class="space-y-4" v-if="allItems.length > 0">
                <div v-for="(row, rowIndex) in itemRows" :key="rowIndex" class="grid grid-cols-6 gap-4">
                    <div v-for="item in row" :key="`${item.type}-${item.id}`"
                        class="relative flex flex-col items-center p-4 border rounded-lg shadow hover:bg-gray-50 transition cursor-pointer group"
                        :class="{ 'bg-blue-100 border-blue-300': isItemSelected(item) }"
                        @click="item.type === 'folder' ? navigateToFolder(item.id) : null">
                        <!-- 選取框 -->
                        <div v-if="canDelete"
                            class="absolute top-2 left-2 opacity-0 group-hover:opacity-100 transition-opacity"
                            @click.stop="toggleItemSelection(item)">
                            <input type="checkbox" :checked="isItemSelected(item)" class="w-4 h-4">
                        </div>

                        <!-- 圖示 -->
                        <div class="text-4xl mb-2 transition-transform group-hover:scale-105">
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
                    </div>
                </div>
            </div>

            <!-- 空狀態 -->
            <div v-else class="text-center py-16">
                <div class="text-6xl mb-4"><i class="fas fa-folder text-yellow-500"></i></div>
                <h3 class="text-lg font-medium text-gray-900 mb-2">資料夾是空的</h3>
                <p class="text-gray-500">
                    <span v-if="canUpload">拖拽檔案到這裡或點擊上傳按鈕來新增檔案</span>
                    <span v-else>目前沒有任何項目</span>
                </p>
            </div>

            <!-- 拖拽提示 -->
            <div v-if="isDragging && canUpload"
                class="fixed inset-0 bg-blue-500 bg-opacity-20 flex items-center justify-center z-50 pointer-events-none">
                <div class="bg-white p-8 rounded-lg shadow-lg border-2 border-dashed border-blue-400">
                    <div class="text-center">
                        <div class="text-4xl mb-2"><i class="fas fa-upload"></i></div>
                        <p class="text-lg font-medium">放開檔案來上傳</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 隱藏的檔案輸入 -->
        <input ref="uploadInput" type="file" multiple @change="handleFileUpload" class="hidden">

        <!-- 刪除確認對話框 -->
        <ConfirmDialog v-if="showDeleteDialog" title="確認刪除" :message="`確定要刪除選取的 ${selectedItems.size} 個項目嗎？此操作無法復原。`"
            @confirm="deleteSelectedItems" @cancel="showDeleteDialog = false" />
    </AdminLayout>
</template>

<script>
// 檔案大小格式化函數
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB', 'GB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>

<style scoped>
.grid-cols-6 {
    grid-template-columns: repeat(6, minmax(0, 1fr));
}

@media (max-width: 1024px) {
    .grid-cols-6 {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }
}

@media (max-width: 768px) {
    .grid-cols-6 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
}

@media (max-width: 640px) {
    .grid-cols-6 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}
</style>