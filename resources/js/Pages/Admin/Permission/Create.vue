<script setup>
import { ref, reactive, watchEffect, onMounted, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import PermissionForm from '@/Components/Form/PermissionForm.vue'


const { permissionGroups } = defineProps({
    permissionGroups: Array
})

const isEdit = ref(false)


</script>

<template>
    <AdminLayout>
        <div class="max-w-3xl mx-auto p-6 bg-white shadow rounded">
            <h1 class="text-xl font-bold mb-4">{{ isEdit ? '編輯權限' : '新增權限' }}</h1>
            <PermissionForm :permissionGroups="permissionGroups" type="create" />
        </div>
    </AdminLayout>
</template>
