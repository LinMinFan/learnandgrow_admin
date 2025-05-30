<script setup>
import { ref, reactive, watchEffect, onMounted, computed } from 'vue'
import AdminLayout from '@/Layouts/AdminLayout.vue';
import MenuForm from '@/Components/MenuForm.vue'


const { parents, permissions } = defineProps({
  parents: Object,
  permissions: Object,
  menu: Object,
})

const isEdit = ref(true)


</script>

<template>
    <AdminLayout>
        <div class="max-w-3xl mx-auto p-6 bg-white shadow rounded">
            <h1 class="text-xl font-bold mb-4">{{ isEdit ? '編輯選單' : '新增選單' }}</h1>
            <MenuForm :parents="parents" :permissions="permissions" :menu="menu" type="edit" />
        </div>
    </AdminLayout>
</template>
