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
    articles: Array,
})

const articles = ref(cloneDeep(props.articles))
const tableRef = ref(null)
let datatableInstance = null;

const articleToDelete = ref(null);
const showDeleteConfirm = ref(false);

const showModal = ref(false)

const handleConfirmDelete = () => {
    if (!articleToDelete.value) return;
    showDeleteConfirm.value = false;
    axios
        .delete(route('post.article.destroy', { id: articleToDelete.value.id }))
        .then((res) => {
            articleToDelete.value = null
            router.visit(route('post.article'), {
                data: {
                    success: res.data.message
                }
            })
        })
        .catch((error) => {
            router.visit(route('post.article'), {
                data: {
                    error: error.response?.data?.message
                }
            })
        })
};

const handleCancelDelete = () => {
    showDeleteConfirm.value = false;
    articleToDelete.value = null;
};

const handleDeleteClick = (e) => {
    const target = e.target.closest('[data-action="delete"]');
    if (target) {
        const id = parseInt(target.getAttribute('data-id'));
        const name = target.getAttribute('data-name');
        const article = articles.value.find(a => a.id === id);
        if (article) {
            articleToDelete.value = article;
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
                <h1 class="text-2xl font-bold">文章管理</h1>
            </div>

            <!-- 操作按鈕區塊（靠右排列） -->
            <div class="flex justify-end gap-2 mb-6">
                <Link :href="route('post.article.create')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded shadow">
                    新增文章
                </Link>
            </div>

            <!-- 表格 -->
            <div class="overflow-x-auto bg-white shadow rounded-lg p-4">
                <table ref="tableRef" class="datatable w-full text-sm text-left text-gray-700 border border-gray-300">
                    <thead class="text-xs uppercase bg-gray-100 text-gray-700">
                        <tr>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                文章類別
                            </th>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                文章標題
                            </th>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                文章摘要
                            </th>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                文章狀態
                            </th>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                文章作者
                            </th>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                置頂
                            </th>
                            <th class="px-4 py-3 border border-gray-300 bg-gray-100 text-xs uppercase text-gray-700">
                                操作
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="article in articles"
                            :key="article.id"
                            class="border-b hover:bg-gray-50 transition-colors"
                        >
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                {{ article.category_name }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                {{ article.title }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                {{ article.excerpt }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                {{ article.status }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                {{ article.author_name }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                {{ article.is_top ? '是' : '否' }}
                            </td>
                            <td class="px-4 py-3 font-medium border border-gray-300">
                                <div class="flex justify-center space-x-2">
                                    <Link
                                        :href="route('post.article.edit', article.id)"
                                        class="p-2 rounded hover:bg-blue-100 text-blue-600"
                                        title="編輯"
                                    >
                                        <i class="fas fa-pen-to-square"></i>
                                    </Link>
                                    <!-- 刪除按鈕 -->
                                    <button 
                                        :data-id="article.id"
                                        :data-name="article.title"
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
                v-if="articleToDelete"
                :show="showDeleteConfirm"
                title="刪除文章"
                :confirmMessage="`確定要刪除文章「${articleToDelete?.title}」嗎？`"
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