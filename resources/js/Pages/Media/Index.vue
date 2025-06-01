<script setup>
import { ref } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { router } from '@inertiajs/vue3'
import ConfirmDialog from '@/Components/ConfirmDialog.vue'

const props = defineProps({
    groupedMedia: Object, // path 分組後的媒體資料
})

const selected = ref(null)
const confirmDelete = ref(false)
const toDeleteId = ref(null)

function selectImage(item) {
    selected.value = item
}

function confirmDeleteImage(id) {
    confirmDelete.value = true
    toDeleteId.value = id
}

function deleteImage() {
    if (toDeleteId.value) {
        router.delete(route('media.destroy', toDeleteId.value), {
            preserveScroll: true,
            onSuccess: () => {
                confirmDelete.value = false
                toDeleteId.value = null
                selected.value = null
            }
        })
    }
}

function prettySize(bytes) {
    if (!bytes) return '0 B'
    const units = ['B', 'KB', 'MB', 'GB']
    let i = 0
    while (bytes >= 1024 && i < units.length - 1) {
        bytes /= 1024
        i++
    }
    return `${bytes.toFixed(2)} ${units[i]}`
}
</script>

<template>
    <AdminLayout>
        <div class="p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold">媒體庫</h1>
            </div>

            <!-- 依路徑分組 -->
            <div v-for="(items, path) in groupedMedia" :key="path" class="mb-10">
                <h2 class="text-lg font-semibold text-gray-700 mb-4">{{ path }}</h2>

                <div class="grid grid-cols-6 gap-4">
                    <div v-for="item in items" :key="item.id"
                        class="relative border rounded overflow-hidden group cursor-pointer" @click="selectImage(item)"
                        :class="{ 'ring-2 ring-blue-500': selected?.id === item.id }">
                        <img :src="`/storage/${item.path}`" class="w-full h-[120px] object-cover"
                            :alt="item.alt || item.filename" />
                        <button @click.stop="confirmDeleteImage(item.id)"
                            class="absolute top-1 right-1 text-xs bg-red-600 text-white rounded px-1 py-0.5 opacity-0 group-hover:opacity-100 transition">
                            刪除
                        </button>
                    </div>
                </div>
            </div>

            <!-- 詳細資料區塊 -->
            <div v-if="selected" class="mt-8 border p-4 rounded shadow">
                <h2 class="text-lg font-bold mb-3">圖片詳細資訊</h2>
                <img :src="`/storage/${selected.path}`" class="max-w-xs mb-2 rounded" />
                <p><strong>檔案名稱：</strong>{{ selected.filename }}</p>
                <p><strong>檔案大小：</strong>{{ prettySize(selected.size) }}</p>
                <p><strong>類型：</strong>{{ selected.mime_type }}</p>
                <p><strong>替代文字：</strong>{{ selected.alt }}</p>
                <p><strong>路徑：</strong>{{ selected.path }}</p>
            </div>

            <!-- 確認刪除 Dialog -->
            <ConfirmDialog v-model:open="confirmDelete" title="確認刪除" message="你確定要刪除此圖片嗎？此操作無法還原。" @confirm="deleteImage" />
        </div>
    </AdminLayout>
</template>

<style scoped>
img {
    transition: transform 0.2s;
}

img:hover {
    transform: scale(1.02);
}
</style>
