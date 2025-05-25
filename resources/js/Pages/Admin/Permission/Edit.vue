<script setup>
import { ref, reactive, watchEffect, onMounted, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PermissionForm from '@/Components/PermissionForm.vue'


const { permissionGroups } = defineProps({
    permissionGroups: Object,
    permission: Object
})

const isEdit = ref(true)


</script>

<template>
    <AdminLayout>
        <div class="max-w-3xl mx-auto p-6 bg-white shadow rounded">
            <h1 class="text-xl font-bold mb-4">{{ isEdit ? '編輯權限' : '新增權限' }}</h1>
            <PermissionForm :permissionGroups="permissionGroups" :permission="permission" type="edit" />
        </div>
    </AdminLayout>
</template>
