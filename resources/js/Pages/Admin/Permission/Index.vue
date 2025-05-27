<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import cloneDeep from 'lodash/cloneDeep'
import { DataTable } from 'simple-datatables'
import 'simple-datatables/dist/style.css'
import PermissionGroupModal from '@/Components/PermissionGroupModal.vue'
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import axios from 'axios'

const props = defineProps({
    permissions: Array,
})

const permissions = ref(cloneDeep(props.permissions))
const tableRef = ref(null)
let datatableInstance = null;

const permissionToDelete = ref(null);
const showDeleteConfirm = ref(false);

const showModal = ref(false)

const handleConfirmDelete = () => {
    if (!permissionToDelete.value) return;
    showDeleteConfirm.value = false;
    axios
        .delete(route('admin.permission.destroy', { id: permissionToDelete.value.id }))
        .then((res) => {
            permissionToDelete.value = null
            router.visit(route('admin.permission'), {
                data: {
                    success: res.data.message
                }
            })
        })
        .catch((error) => {
            router.visit(route('admin.permission'), {
                data: {
                    error: error.response?.data?.message
                }
            })
        })
};

const handleCancelDelete = () => {
    showDeleteConfirm.value = false;
    permissionToDelete.value = null;
};

const handleDeleteClick = (e) => {
    const target = e.target.closest('[data-action="delete"]');
    if (target) {
        const id = parseInt(target.getAttribute('data-id'));
        const name = target.getAttribute('data-name');
        const permission = permissions.value.find(p => p.id === id);
        if (permission) {
            permissionToDelete.value = permission;
            showDeleteConfirm.value = true;
        }
    }
}

const initDataTable = () => {
    if (tableRef.value && !datatableInstance) {
        datatableInstance = new DataTable(tableRef.value, {
            perPage: 10,
            labels: {
                placeholder: '搜尋...',
                perPage: '每頁顯示筆數',
                noRows: '查無資料',
                noResults: '查無搜尋結果',
                info: '共 {rows} 筆資料，共 {pages} 頁',
            },
        })

        // 加上事件代理，處理刪除按鈕點擊
        tableRef.value.addEventListener('click', handleDeleteClick);
    }
}

const destroyDataTable = () => {
    // 移除事件監聽
    if (tableRef.value) {
        tableRef.value.removeEventListener('click', handleDeleteClick);
    }

    // 銷毀 DataTable 實例（視 DataTable 套件是否提供 destroy 方法）
    if (datatableInstance && typeof datatableInstance.destroy === 'function') {
        datatableInstance.destroy()
    }

    datatableInstance = null
}

onMounted(initDataTable)
onUnmounted(destroyDataTable)

</script>

<template>
    <AdminLayout>
        <div class="space-y-6">
            <!-- 標題區塊 -->
            <div class="mb-4">
                <h1 class="text-2xl font-bold">權限管理</h1>
            </div>

            <!-- 操作按鈕區塊（靠右排列） -->
            <div class="flex justify-end gap-2 mb-6">
                <button
                    @click="showModal = true"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded shadow"
                >
                    新增群組
                </button>
                <Link :href="route('admin.permission.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                    新增權限
                </Link>
            </div>

            <!-- 表格 -->
            <div class="overflow-x-auto bg-white shadow rounded-lg p-4">
                <table ref="tableRef" class="datatable w-full text-sm text-left text-gray-700 border border-gray-300">
                    <thead class="text-xs uppercase bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                編號
                            </th>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                權限代號
                            </th>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                權限名稱
                            </th>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                群組名稱
                            </th>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                操作
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="permission in permissions"
                            :key="permission.id"
                            class="border-b hover:bg-gray-50 transition-colors"
                        >
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                {{ permission.id }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                {{ permission.name }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                {{ permission.display_name }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                <span class="px-2 py-0.5 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">
                                    {{ permission.group_display_name }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                <div class="flex justify-center space-x-2">
                                    <Link
                                        :href="route('admin.permission.edit', permission.id)"
                                        class="p-2 rounded hover:bg-blue-100 text-blue-600"
                                        title="編輯"
                                    >
                                        <i class="fas fa-pen-to-square"></i>
                                    </Link>
                                    <!-- 刪除按鈕 -->
                                    <button 
                                        :data-id="permission.id"
                                        :data-name="permission.name"
                                        data-action="delete"
                                        title="刪除"
                                        class="text-red-600 hover:text-red-800"
                                    >
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <!-- 群組表格... -->
            <PermissionGroupModal 
                :show="showModal" 
                @close="showModal = false" 
            />
            <!-- 確認對話框 -->
            <ConfirmDialog
                v-if="permissionToDelete"
                :show="showDeleteConfirm"
                title="刪除權限"
                :confirmMessage="`確定要刪除權限「${permissionToDelete?.name}」嗎？`"
                confirmButtonText="刪除"
                @confirm="handleConfirmDelete"
                @cancel="handleCancelDelete"
            />
        </div>
    </AdminLayout>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}
</style>