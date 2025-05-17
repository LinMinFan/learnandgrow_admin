<script setup>
import { ref, reactive, watchEffect, onMounted, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, usePage  } from '@inertiajs/vue3';
import { onClickOutside  } from '@vueuse/core'
import cloneDeep from 'lodash/cloneDeep'
import draggable from 'vuedraggable'
import axios from 'axios'
import { useNotification } from "@kyvg/vue3-notification";

const props = defineProps({
    menuData: Array,
})

const menus = ref(cloneDeep(props.menuData)) // 深拷貝，避免直接修改 props

const originalSorted = ref([])

const { notify }  = useNotification()

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
        .post('/system/menus/sort', { sorted: current })
        .then(() => {
            notify({
                group: 'top',
                text: '資料儲存完成！',
                type: 'success',
            })
            originalSorted.value = current // 更新原始參考值
        })
        .catch(() => {
            notify({
                group: 'top',
                text: '資料儲存失敗！',
                type: 'error',
            })
        })
}

</script>

<template>
    <AdminLayout>
        <div>
            <div class="flex justify-between mb-4">
                <h1 class="text-2xl font-bold">後台選單管理</h1>
                <Link href="/system/menus/create" class="btn btn-primary bg-blue-600 text-white px-4 py-2 rounded">
                    新增選單
                </Link>
            </div>

            <div class="bg-white rounded shadow p-4">
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
                                    <span class="ml-2 text-sm text-gray-600">({{ menu.route || menu.children.length }})</span>
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
                                    <div class="ml-4 p-2 bg-blue-100 border-t handle">
                                        ↳ {{ child.title }} ({{ child.route || '—' }})
                                    </div>
                                </template>
                            </draggable>
                        </div>
                    </template>
                </draggable>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>

</style>