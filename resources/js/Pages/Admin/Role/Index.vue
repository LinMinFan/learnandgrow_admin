<script setup>
import { ref, reactive, watchEffect, onMounted, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import { Link, usePage  } from '@inertiajs/vue3';
import { onClickOutside  } from '@vueuse/core'
import cloneDeep from 'lodash/cloneDeep'
import axios from 'axios'
import { useNotification } from "@kyvg/vue3-notification";

const props = defineProps({
    roleList: Array,
})

const roleList = ref(cloneDeep(props.roleList)) // 深拷貝，避免直接修改 props

const { notify }  = useNotification()


</script>

<template>
    <AdminLayout>
        <div>
            <div class="flex justify-between mb-4">
                <h1 class="text-2xl font-bold">角色管理</h1>
                <Link :href="route('admin.role.create')" class="btn btn-primary bg-blue-600 text-white px-4 py-2 rounded">
                    新增角色
                </Link>
            </div>
            <!-- 表格 -->
            <div class="overflow-x-auto">
                <table class="table-auto w-full">
                    <thead>
                        <tr>
                            <th>角色名稱</th>
                            <th>擁有權限</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="role in roleList" :key="role.id">
                            <td>{{ role.name }}</td>
                            <td>
                                <span v-for="permission in role.permissions" :key="permission.id" class="badge mr-1">
                                    {{ permission.name }}
                                </span>
                            </td>
                            <td>
                                <Link :href="route('admin.role.edit', role.id)">編輯</Link>
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