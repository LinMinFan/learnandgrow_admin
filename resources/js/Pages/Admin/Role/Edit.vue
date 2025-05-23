<!-- resources/js/Pages/Admin/Role/Edit.vue -->
<script setup>
import { ref, reactive, watchEffect, onMounted, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import RoleForm from '@/Components/RoleForm.vue'


const { role, permissions } = defineProps({
    role: Object,
    permissions: Array
})

const isEdit = ref(true)

</script>

<template>
    <AdminLayout>
        <div class="max-w-3xl mx-auto p-6 bg-white shadow rounded">
            <h1 class="text-xl font-bold mb-4">{{ isEdit ? '編輯角色' : '新增角色' }}</h1>
            <RoleForm :permissions="permissions" :role="role" type="edit" />
        </div>
    </AdminLayout>
</template>
