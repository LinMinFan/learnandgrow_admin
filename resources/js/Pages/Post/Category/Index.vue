<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import { useTopGlobalNotify } from '@/Composables/useTopGlobalNotify'
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import cloneDeep from 'lodash/cloneDeep'
import draggable from 'vuedraggable'
import axios from 'axios'

const props = defineProps({
    categoryData: Array,
})

const categories = ref(cloneDeep(props.categoryData)) // 深拷貝，避免直接修改 props
const categoryToDelete = ref(null);
const showDeleteConfirm = ref(false);

const originalSorted = ref([])

const { errorNotify, successNotify } = useTopGlobalNotify()

onMounted(() => {
    originalSorted.value = getSortedCategories()
})

const getSortedCategories = () => {
    const sorted = []
    categories.value.forEach((category, parentIndex) => {
        sorted.push({
            id: category.id,
            sort_order: parentIndex + 1,
            parent_id: null,
        })

        if (category.children?.length) {
            category.children.forEach((child, childIndex) => {
                sorted.push({
                    id: child.id,
                    sort_order: childIndex + 1,
                    parent_id: category.id,
                })
            })
        }
    })
    return sorted
}

const onParentDragEnd = () => {
  updateSortedCategories()
}

const onChildDragEnd = (newParentMenu) => {
  updateSortedCategories()
}

// 根據目前的 categories 階層結構產生排序資料並送出
const updateSortedCategories = () => {
    const current = getSortedCategories()

    // 先做字串比對，快速判斷是否有差異
    if (JSON.stringify(current) === JSON.stringify(originalSorted.value)) {
        return // 沒有變更就不送出請求
    }

    axios
        .post(route('post.category.sort'), { sorted: current })
        .then(() => {
            successNotify('資料儲存完成！')
            originalSorted.value = current // 更新原始參考值
        })
        .catch(() => {
            errorNotify('資料儲存失敗！')
        })
}

const handleConfirmDelete = () => {
    if (!categoryToDelete.value) return;
    showDeleteConfirm.value = false;
    axios
        .delete(route('post.category.destroy', { id: categoryToDelete.value.id }))
        .then((res) => {
            categoryToDelete.value = null
            router.visit(route('post.category'), {
                data: {
                    success: res.data.message
                }
            })
        })
        .catch((error) => {
            router.visit(route('post.category'), {
                data: {
                    error: error.response?.data?.message
                }
            })
        })
};

const handleCancelDelete = () => {
    showDeleteConfirm.value = false;
    categoryToDelete.value = null;
};

const handleDeleteClick = (category) => {
  categoryToDelete.value = category;
  showDeleteConfirm.value = true;
};

</script>

<template>
    <AdminLayout>
        <div>
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">後台分類管理</h1>
                <Link :href="route('post.category.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                    新增分類
                </Link>
            </div>

            <div class="bg-white rounded shadow p-4">
                <div v-if="categories.length === 0" class="text-gray-500 text-center py-10">
                    尚無分類資料，請點擊右上角「新增分類」。
                </div>
                <draggable
                    v-model="categories"
                    item-key="id"
                    handle=".handle"
                    @end="onParentDragEnd"
                >
                    <template #item="{ element: category }">
                        <div class="mb-2 border rounded bg-blue-200">
                            <!-- 父分類 -->
                            <div class="p-2 handle flex justify-between">
                                <div>
                                    <strong>{{ category.name }}</strong>
                                    <span class="ml-2 text-sm text-gray-600">({{ category.children.length }})</span>
                                    <span class="ml-2 text-sm text-gray-600">
                                        ({{ category.is_active ? '顯示' : '不顯示' }})
                                    </span>
                                </div>
                                <div class="space-x-2 mr-3">
                                    <span class="inline-flex items-center text-sm font-medium text-yellow-800 bg-yellow-200 px-2.5 py-0.5 rounded">
                                        <i class="fas fa-lock mr-1"></i> 無法編輯
                                    </span>
                                </div>
                            </div>

                            <!-- 子分類 -->
                            <draggable
                                v-model="category.children"
                                :group="'categories'"
                                item-key="id"
                                handle=".handle"
                                @end="() => onChildDragEnd(category)"
                            >
                                <template #item="{ element: child }">
                                    <div class="ml-4 p-2 bg-blue-100 border-t handle flex justify-between items-center">
                                        <div>
                                            ↳ {{ child.name }}
                                            <span :class="{ 'text-gray-400': child.articles_count === 0 }">
                                                ({{ child.articles_count }})
                                            </span>
                                            <span class="ml-2 text-sm text-gray-600">
                                                ({{ child.is_active ? '顯示' : '不顯示' }})
                                            </span>
                                        </div>
                                        <div class="space-x-2 flex-shrink-0 mr-3">
                                            <Link
                                                :href="route('post.category.edit', child.id)"
                                                class="p-2 rounded hover:bg-blue-100 text-blue-600"
                                                title="編輯"
                                            >
                                                <i class="fas fa-pen-to-square"></i>
                                            </Link>
                                            <button 
                                                title="刪除"
                                                class="text-red-600 hover:text-red-800"
                                                @click.stop="handleDeleteClick(child)"
                                            >
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </template>
                            </draggable>
                        </div>
                    </template>
                </draggable>
            </div>
            <!-- 確認對話框 -->
            <ConfirmDialog
                v-if="categoryToDelete"
                :show="showDeleteConfirm"
                title="刪除分類"
                :confirmMessage="`確定要刪除分類「${categoryToDelete?.name}」嗎？`"
                confirmButtonText="刪除"
                @confirm="handleConfirmDelete"
                @cancel="handleCancelDelete"
            />
        </div>
    </AdminLayout>
</template>

<style scoped>

</style>