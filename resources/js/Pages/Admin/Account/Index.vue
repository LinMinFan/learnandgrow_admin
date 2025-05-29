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
    userList: Object,
})

const userList = ref(cloneDeep(props.userList))
const tableRef = ref(null)
let datatableInstance = null;

const userToDelete = ref(null);
const showDeleteConfirm = ref(false);

const handleConfirmDelete = () => {
    if (!userToDelete.value) return;
    showDeleteConfirm.value = false;
    axios
        .delete(route('admin.account.destroy', { id: userToDelete.value.id }))
        .then((res) => {
            userToDelete.value = null
            router.visit(route('admin.account'), {
                data: {
                    success: res.data.message
                }
            })
        })
        .catch((error) => {
            router.visit(route('admin.account'), {
                data: {
                    error: error.response?.data?.message
                }
            })
        })
};

const handleCancelDelete = () => {
    showDeleteConfirm.value = false;
    userToDelete.value = null;
};

const handleDeleteClick = (e) => {
    const target = e.target.closest('[data-action="delete"]');
    if (target) {
        const id = parseInt(target.getAttribute('data-id'));
        const name = target.getAttribute('data-name');
        const user = userList.value.find(p => p.id === id);
        if (user) {
            userToDelete.value = user;
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
                <h1 class="text-2xl font-bold">帳號管理</h1>
            </div>

            <!-- 操作按鈕區塊（靠右排列） -->
            <div class="flex justify-end gap-2 mb-6">
                <Link :href="route('admin.account.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                    新增帳號
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
                                帳號名稱
                            </th>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                帳號email
                            </th>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                擁有角色
                            </th>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                操作
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="user in userList"
                            :key="user.id"
                            class="border-b hover:bg-gray-50 transition-colors"
                        >
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                {{ user.id }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                {{ user.name }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                {{ user.email }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                <span 
                                    v-for="role in user.roles"
                                    :key="role.id"
                                    class="px-2 py-0.5 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full"
                                >
                                    {{ role.display_name }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                <div class="flex justify-center space-x-2">
                                    <Link
                                        :href="route('admin.account.edit', user.id)"
                                        class="p-2 rounded hover:bg-blue-100 text-blue-600"
                                        title="編輯"
                                    >
                                        <i class="fas fa-pen-to-square"></i>
                                    </Link>
                                    <!-- 刪除按鈕 -->
                                    <button 
                                        :data-id="user.id"
                                        :data-name="user.name"
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
                v-if="userToDelete"
                :show="showDeleteConfirm"
                title="刪除帳號"
                :confirmMessage="`確定要刪除帳號「${userToDelete?.name}」嗎？`"
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