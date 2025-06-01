<script setup>
import { ref, reactive, watchEffect, onMounted, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import AccountForm from '@/Components/AccountForm.vue'


const { roles } = defineProps({
    roles: Array,
    user: Object,
})

const isEdit = ref(true)


</script>

<template>
    <AdminLayout>
        <div class="max-w-3xl mx-auto p-6 bg-white shadow rounded">
            <h1 class="text-xl font-bold mb-4">{{ isEdit ? '編輯帳號' : '新增帳號' }}</h1>
            <AccountForm :roles="roles" :user="user" type="edit" />
        </div>
    </AdminLayout>
</template>
