<script setup>
import { ref, reactive, watchEffect, onMounted, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, usePage  } from '@inertiajs/vue3';
import { onClickOutside  } from '@vueuse/core'
import cloneDeep from 'lodash/cloneDeep'
import axios from 'axios'
import { useNotification } from "@kyvg/vue3-notification";
import { DataTable } from "simple-datatables"

const props = defineProps({
    userList: Array,
})

const userList = ref(cloneDeep(props.userList)) // 深拷貝，避免直接修改 props

const { notify }  = useNotification()

const tableRef = ref(null)

onMounted(() => {
    if (tableRef.value) {
        new DataTable(tableRef.value, {
            perPage: 10,
            labels: {
                placeholder: "搜尋...",
                perPage: "每頁顯示筆數",
                noRows: "查無資料",
                noResults: "查無搜尋結果",
                info: "共 {rows} 筆資料，共 {pages} 頁",
            }
        })
    }
})

</script>

<template>
    <AdminLayout>
        <div>
            <div class="flex justify-between mb-4">
                <h1 class="text-2xl font-bold">系統管理員</h1>
                <Link href="/system/menus/create" class="btn btn-primary bg-blue-600 text-white px-4 py-2 rounded">
                    新增系統管理員
                </Link>
            </div>
            <!-- 表格 -->
            <div class="overflow-x-auto">
                <table ref="tableRef" class="datatable table-auto w-full border-collapse border border-gray-200">
                    <thead>
                        <tr class="bg-gray-100">
                            <th class="border px-4 py-2">ID</th>
                            <th class="border px-4 py-2">名稱</th>
                            <th class="border px-4 py-2">Email</th>
                            <th class="border px-4 py-2">角色</th>
                            <th class="border px-4 py-2">操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="user in userList" :key="user.id">
                            <td class="border px-4 py-2">{{ user.id }}</td>
                            <td class="border px-4 py-2">{{ user.name }}</td>
                            <td class="border px-4 py-2">{{ user.email }}</td>
                            <td class="border px-4 py-2">{{ user.roles.map(role => role.name).join(', ') }}</td>
                            <td class="border px-4 py-2">
                                <Link :href="`/system/users/${user.id}/edit`" class="text-blue-600 hover:underline">
                                    編輯
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </AdminLayout>
</template>

<style scoped>

</style>