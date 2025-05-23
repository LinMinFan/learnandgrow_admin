<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import cloneDeep from 'lodash/cloneDeep'
import { DataTable } from 'simple-datatables'
import 'simple-datatables/dist/style.css'
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import axios from 'axios'

const props = defineProps({
    roleList: Array,
    message: {
        type: String,
        default: '',
    },
    type: {
        type: String,
        default: '',
    },
})

const roleList = ref(cloneDeep(props.roleList))
const tableRef = ref(null)
let datatableInstance = null;

const roleToDelete = ref(null);
const showDeleteConfirm = ref(false);

const formatDate = (dateStr) => {
    const date = new Date(dateStr)
    const yyyy = date.getFullYear()
    const mm = String(date.getMonth() + 1).padStart(2, '0')
    const dd = String(date.getDate()).padStart(2, '0')
    return `${yyyy}/${mm}/${dd}`
}

const handleConfirmDelete = () => {
    if (!roleToDelete.value) return;
    showDeleteConfirm.value = false;
    axios
        .delete(route('admin.role.destroy', { id: roleToDelete.value.id }))
        .then((res) => {
            roleToDelete.value = null
            router.visit(route('admin.role'), {
                data: {
                    success: res.data.message
                }
            })
        })
        .catch((error) => {
            router.visit(route('admin.role'), {
                data: {
                    error: response.data.message
                }
            })
        })
};

const handleCancelDelete = () => {
    showDeleteConfirm.value = false;
    roleToDelete.value = null;
};

const handleDeleteClick = (e) => {
    const target = e.target.closest('[data-action="delete"]');
    if (target) {
        const id = parseInt(target.getAttribute('data-id'));
        const name = target.getAttribute('data-name');
        const role = roleList.value.find(r => r.id === id);
        if (role) {
            roleToDelete.value = role;
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
        <!-- 標題與新增按鈕 -->
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-bold">角色管理</h1>
                <Link :href="route('admin.role.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                    新增角色
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
                                角色名稱
                            </th>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                建立日期
                            </th>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                操作
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="role in roleList"
                            :key="role.id"
                            class="border-b hover:bg-gray-50 transition-colors"
                        >
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                {{ role.id }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                {{ role.name }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                {{ formatDate(role.created_at) }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                <div class="flex justify-center space-x-2">
                                    <Link
                                        :href="route('admin.role.edit', role.id)"
                                        class="p-2 rounded hover:bg-blue-100 text-blue-600"
                                        title="編輯"
                                    >
                                        <i class="fas fa-pen-to-square"></i>
                                    </Link>
                                    <!-- 刪除按鈕 -->
                                    <button 
                                        :data-id="role.id"
                                        :data-name="role.name"
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
            <!-- 確認對話框 -->
            <ConfirmDialog
                v-if="roleToDelete"
                :show="showDeleteConfirm"
                title="刪除角色"
                :confirmMessage="`確定要刪除角色「${roleToDelete?.name}」嗎？`"
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