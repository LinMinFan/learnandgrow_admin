<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link, router, usePage } from '@inertiajs/vue3'
import cloneDeep from 'lodash/cloneDeep'
import { DataTable } from 'simple-datatables'
import 'simple-datatables/dist/style.css'
import ConfirmDialog from '@/Components/ConfirmDialog.vue';
import ContactFormModal from '@/Components/ContactForm/ContactFormModal.vue'
import { useTopGlobalNotify } from '@/Composables/useTopGlobalNotify'
import axios from 'axios'

const props = defineProps({
    contactForm: Array,
})

const { errorNotify, successNotify } = useTopGlobalNotify()
const contactForm = ref(cloneDeep(props.contactForm))
const tableRef = ref(null)
let datatableInstance = null;

const formToDelete = ref(null);
const showDeleteConfirm = ref(false);
const showDetailModal = ref(false)
const selectedForm = ref(null)

const formatDate = (dateStr) => {
    const date = new Date(dateStr)
    const yyyy = date.getFullYear()
    const mm = String(date.getMonth() + 1).padStart(2, '0')
    const dd = String(date.getDate()).padStart(2, '0')
    return `${yyyy}/${mm}/${dd}`
}

const handleViewClick = async (e) => {
    const target = e.target.closest('[data-action="view"]');
    if (!target) return;

    try {
        const id = parseInt(target.getAttribute('data-id'));
        const form = contactForm.value.find(f => f.id === id)
        if (!form) return

        const res = await axios.get(route('api.form.show', {id}))

        // 更新 message（避免多次開窗都重複請求，可加快彈窗顯示）
        form.message = res.data.message

        // 標記為已讀（更新前端狀態）
        if (!form.is_read) {
            form.is_read = true

            updateReadStatusInDOM(id, true)
        }

        selectedForm.value = form
        showDetailModal.value = true

    } catch (err) {
        errorNotify('無法載入留言內容，請稍後再試')
    }
}

const updateReadStatusInDOM = (formId, isRead) => {
    const row = tableRef.value.querySelector(`tr[data-form-id="${formId}"]`)
    if (row) {
        const readStatusCell = row.querySelector('.read-status-cell')
        if (readStatusCell) {
            readStatusCell.textContent = isRead ? '已讀取' : '未讀取'
        }
    }
}

const closeModal = () => {
    showDetailModal.value = false
    selectedForm.value = null
}

const handleConfirmDelete = () => {
    if (!formToDelete.value) return;
    showDeleteConfirm.value = false;
    const formId = formToDelete.value.id;

    axios
        .delete(route('form.destroy', { id: formId }))
        .then((res) => {
            formToDelete.value = null
            router.visit(route('form.index'), {
                data: {
                    success: res.data.message
                }
            })
        })
        .catch((error) => {
            router.visit(route('form.index'), {
                data: {
                    error: error.response?.data?.message
                }
            })
        })
};

const handleCancelDelete = () => {
    showDeleteConfirm.value = false;
    formToDelete.value = null;
};

const handleDeleteClick = (e) => {
    const target = e.target.closest('[data-action="delete"]');
    if (target) {
        const id = parseInt(target.getAttribute('data-id'));
        const subject = target.getAttribute('data-name');
        const formItem = contactForm.value.find(f => f.id === id);
        if (formItem) {
            formToDelete.value = formItem;
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
        tableRef.value.addEventListener('click', handleViewClick);
    }
}

const destroyDataTable = () => {
    // 移除事件監聽
    if (tableRef.value) {
        tableRef.value.removeEventListener('click', handleDeleteClick);
        tableRef.value.removeEventListener('click', handleViewClick);
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
                <h1 class="text-2xl font-bold">聯絡表單管理</h1>
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
                                主題
                            </th>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                聯絡人名稱
                            </th>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                Email
                            </th>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                電話
                            </th>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                是否已讀
                            </th>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                建立時間
                            </th>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                操作
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="formData in contactForm"
                            :key="formData.id"
                            :data-form-id="formData.id"
                            class="border-b hover:bg-gray-50 transition-colors"
                        >
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                {{ formData.id }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                {{ formData.subject }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                {{ formData.name }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                {{ formData.email }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                {{ formData.phone }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300 read-status-cell">
                                {{ formData.is_read ? '已讀取' : '未讀取' }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                {{ formatDate(formData.created_at) }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                <div class="flex justify-center space-x-2">
                                    <!-- 詳細按鈕（改為觸發彈窗） -->
                                    <button
                                        class="p-2 rounded hover:bg-blue-100 text-blue-600"
                                        title="詳細"
                                        data-action="view"
                                        :data-id="formData.id"
                                    >
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <!-- 刪除按鈕 -->
                                    <button 
                                        :data-id="formData.id"
                                        :data-name="formData.subject"
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
            <!-- 詳細內容 -->
            <ContactFormModal
                :show="showDetailModal"
                :data="selectedForm"
                @close="closeModal"
            />
            <!-- 確認對話框 -->
            <ConfirmDialog
                v-if="formToDelete"
                :show="showDeleteConfirm"
                title="刪除表單"
                :confirmMessage="`確定要刪除表單「${formToDelete?.subject}」嗎？`"
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