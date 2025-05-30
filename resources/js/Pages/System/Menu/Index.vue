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
    menuData: Array,
})

const menus = ref(cloneDeep(props.menuData)) // 深拷貝，避免直接修改 props
const menuToDelete = ref(null);
const showDeleteConfirm = ref(false);

const originalSorted = ref([])

const { errorNotify, successNotify } = useTopGlobalNotify()

onMounted(() => {
    originalSorted.value = getSortedMenus()
})

const getSortedMenus = () => {
    const sorted = []
    menus.value.forEach((menu, parentIndex) => {
        sorted.push({
            id: menu.id,
            sort: parentIndex + 1,
            parent_id: null,
        })

        if (menu.children?.length) {
            menu.children.forEach((child, childIndex) => {
                sorted.push({
                    id: child.id,
                    sort: childIndex + 1,
                    parent_id: menu.id,
                })
            })
        }
    })
    return sorted
}

const onParentDragEnd = () => {
  updateSortedMenus()
}

const onChildDragEnd = (newParentMenu) => {
  updateSortedMenus()
}

// 根據目前的 menus 階層結構產生排序資料並送出
const updateSortedMenus = () => {
    const current = getSortedMenus()

    // 先做字串比對，快速判斷是否有差異
    if (JSON.stringify(current) === JSON.stringify(originalSorted.value)) {
        return // 沒有變更就不送出請求
    }

    axios
        .post(route('system.menu.sort'), { sorted: current })
        .then(() => {
            successNotify('資料儲存完成！')
            originalSorted.value = current // 更新原始參考值
        })
        .catch(() => {
            errorNotify('資料儲存失敗！')
        })
}

const handleConfirmDelete = () => {
    if (!menuToDelete.value) return;
    showDeleteConfirm.value = false;
    axios
        .delete(route('system.menu.destroy', { id: menuToDelete.value.id }))
        .then((res) => {
            menuToDelete.value = null
            router.visit(route('system.menu'), {
                data: {
                    success: res.data.message
                }
            })
        })
        .catch((error) => {
            router.visit(route('system.menu'), {
                data: {
                    error: error.response?.data?.message
                }
            })
        })
};

const handleCancelDelete = () => {
    showDeleteConfirm.value = false;
    menuToDelete.value = null;
};

const handleDeleteClick = (menu) => {
  menuToDelete.value = menu;
  showDeleteConfirm.value = true;
};

</script>

<template>
    <AdminLayout>
        <div>
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">後台選單管理</h1>
                <Link :href="route('system.menu.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                    新增選單
                </Link>
            </div>

            <div class="bg-white rounded shadow p-4">
                <div v-if="menus.length === 0" class="text-gray-500 text-center py-10">
                    尚無選單資料，請點擊右上角「新增選單」。
                </div>
                <draggable
                    v-model="menus"
                    item-key="id"
                    handle=".handle"
                    @end="onParentDragEnd"
                >
                    <template #item="{ element: menu }">
                        <div class="mb-2 border rounded bg-blue-200">
                            <!-- 父選單 -->
                            <div class="p-2 handle flex justify-between">
                                <div>
                                    <strong>{{ menu.title }}</strong>
                                    <span class="ml-2 text-sm text-gray-600">({{ menu.children.length || menu.route }})</span>
                                    <span class="ml-2 text-sm text-gray-600">
                                        ({{ menu.is_active ? '顯示' : '不顯示' }})
                                    </span>
                                </div>
                                <div class="space-x-2 mr-3">
                                    <Link
                                        :href="route('system.menu.edit', menu.id)"
                                        class="p-2 rounded hover:bg-blue-100 text-blue-600"
                                        title="編輯"
                                    >
                                        <i class="fas fa-pen-to-square"></i>
                                    </Link>
                                    <button 
                                        class="text-red-600 hover:text-red-800"
                                        @click.stop="handleDeleteClick(menu)"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- 子選單 -->
                            <draggable
                                v-model="menu.children"
                                :group="'menus'"
                                item-key="id"
                                handle=".handle"
                                @end="() => onChildDragEnd(menu)"
                            >
                                <template #item="{ element: child }">
                                    <div class="ml-4 p-2 bg-blue-100 border-t handle flex justify-between items-center">
                                        <div>
                                            ↳ {{ child.title }} ({{ child.route || '—' }})
                                            <span v-if="child.permission?.display_name" class="ml-2 text-sm text-gray-600">
                                                / {{ child.permission.display_name }}
                                            </span>
                                            <span class="ml-2 text-sm text-gray-600">
                                                ({{ child.is_active ? '顯示' : '不顯示' }})
                                            </span>
                                        </div>
                                        <div class="space-x-2 flex-shrink-0 mr-3">
                                            <Link
                                                :href="route('system.menu.edit', child.id)"
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
                v-if="menuToDelete"
                :show="showDeleteConfirm"
                title="刪除選單"
                :confirmMessage="`確定要刪除選單「${menuToDelete?.title}」嗎？`"
                confirmButtonText="刪除"
                @confirm="handleConfirmDelete"
                @cancel="handleCancelDelete"
            />
        </div>
    </AdminLayout>
</template>

<style scoped>

</style>